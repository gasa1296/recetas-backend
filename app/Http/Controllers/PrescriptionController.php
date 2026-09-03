<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinishPrescriptionRequest;
use App\Http\Requests\PrescriptionRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\PrescriptionCollection;
use App\Http\Resources\PrescriptionResource;
use App\Notifications\PrescriptionReadyNotification;
use App\Services\TimestampService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use setasign\Fpdi\Tcpdf\Fpdi;

class PrescriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SearchRequest $request): JsonResponse
    {
        $prescriptions = auth()->user()->prescriptions()->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $search = $request->input('search');
            $prescriptions = $prescriptions->whereLike('diagnostic', "%$search%", false);
        }

        return (new PrescriptionCollection($prescriptions->paginate(10)))->response();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PrescriptionRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['prescription_hash'] = hash('sha256', json_encode($data).Str::random(16).microtime(true));

        $prescription = auth()
            ->user()
            ->prescriptions()
            ->create($data);
        if (! empty($data['medicaments'])) {
            $prescription->medicaments()->sync($data['medicaments']);
        }

        return $this->success(
            __('messages.operation_success'),
            new PrescriptionResource(
                $prescription->load(['medicaments', 'patient', 'room', 'specialty']),
            ),
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(int $prescription): JsonResponse
    {
        $prescription = auth()
            ->user()
            ->prescriptions()
            ->findOrFail($prescription);

        return $this->success(
            __('messages.operation_success'),
            new PrescriptionResource(
                $prescription->load(['medicaments', 'patient', 'room', 'specialty', 'user']),
            ),
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        PrescriptionRequest $request,
        int $prescription,
    ): JsonResponse {
        $prescription = auth()
            ->user()
            ->prescriptions()
            ->where('status', config('custom.prescription.status_keys.draft'))
            ->findOrFail($prescription);
        $data = $request->validated();
        $prescription->update($data);

        if (! empty($data['medicaments'])) {
            $prescription->medicaments()->sync($data['medicaments']);
        }

        return $this->success(
            __('messages.operation_success'),
            new PrescriptionResource(
                $prescription->load(['medicaments', 'patient', 'room', 'specialty']),
            ),
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $prescription): JsonResponse
    {
        $prescription = auth()
            ->user()
            ->prescriptions()
            ->where('status', config('custom.prescription.status_keys.draft'))
            ->lockForUpdate()
            ->findOrFail($prescription);
        $prescription->delete();

        return $this->success(
            __('messages.operation_success'),
        );
    }

    public function finishPrescription(FinishPrescriptionRequest $request, int $prescription): JsonResponse
    {
        $user = auth()->user();
        $prescription = $user
            ->prescriptions()
            ->where('status', config('custom.prescription.status_keys.draft'))
            ->findOrFail($prescription);

        $expirationDaysConf = config('custom.prescription.expiration_days', []);
        $expirationDays = $expirationDaysConf['default'] ?? 30;
        $expiresAt = now()->addDays($expirationDays);

        foreach ($expirationDaysConf as $type => $days) {
            if ($type === 'default') {
                continue;
            }
            if ($prescription->medicaments->contains('type', $type)) {
                $expirationDays = $days;
            }
        }
        $expiresAt = now()->addDays($expirationDays);

        $prescription->loadMissing(['user', 'patient', 'room', 'specialty', 'medicaments']);
        $qrOptions = new QROptions;
        $qrOptions->outputType = 'png';
        $qrOptions->scale = 5;
        $qrCode = (new QRCode($qrOptions))->render(route('public.prescription.show', $prescription->prescription_hash));

        $pdfContent = Pdf::loadView('pdf.prescription_model_1', [
            'prescription' => $prescription,
            'signature' => $expirationDays != 0 ? $request->input('signature') : null,
            'qrCode' => $qrCode,
        ])->output();

        if ($expirationDays == 0) {
            $prescription->handleUploadFile($pdfContent);
            $prescription->update(['status' => config('custom.prescription.status_keys.active'), 'expires_at' => $expiresAt]);

            return $this->success(
                __('messages.operation_success'),
                new PrescriptionResource(
                    $prescription->load(['medicaments', 'patient', 'room', 'specialty', 'user']),
                ),
            );
        }

        // 2. Initialize FPDI with TCPDF engine
        $pdf = new Fpdi;

        // 3. Configure the Digital Signature
        // Use user's certificate if available, otherwise fall back to default
        if ($user->hasValidCertificate()) {
            $certificate = 'file://'.$user->getCertificatePath();
            $privateKey = 'file://'.$user->getCertificateKeyPath();
        } else {
            $certificate = 'file://'.base_path(config('custom.prescription.signature.default_certificate.path'));
            $privateKey = 'file://'.base_path(config('custom.prescription.signature.default_certificate.key_path'));
        }

        $info = [
            'Name' => config('app.name'),
            'Location' => $prescription->room->address,
            'Reason' => $prescription->room->identification,
        ];
        $pdf->setSignature($certificate, $privateKey, '', '', 2, $info);

        // 4. Save temporary file because FPDI requires a filepath or a stream wrapper
        $tempFile = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($tempFile, $pdfContent);

        $pageCount = $pdf->setSourceFile($tempFile);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);

            // Add a page matching the imported layout size/orientation
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);
        }

        // 5. Apply TSA timestamp if enabled
        $tsaConfig = config('custom.prescription.signature.tsa', []);
        if (! empty($tsaConfig['enabled']) && ! empty($tsaConfig['url'])) {
            $signedPdf = $pdf->Output('', 'S');
            $timestampService = new TimestampService(
                $tsaConfig['url'],
                $tsaConfig['hash_algorithm'] ?? 'sha256'
            );
            $timestampToken = $timestampService->timestamp($signedPdf);

            if ($timestampToken) {
                // Embed the timestamp token into the PDF
                $signedPdf = $this->embedTimestampInPdf($signedPdf, $timestampToken);
            }

            $prescription->handleUploadFile($signedPdf, 'signed');
        } else {
            $prescription->handleUploadFile($pdf->Output('', 'S'), 'signed');
        }

        unlink($tempFile);

        $prescription->update(['status' => config('custom.prescription.status_keys.active'), 'expires_at' => $expiresAt]);

        $prescription->loadMissing('patient');
        $prescription->patient->notify(new PrescriptionReadyNotification($prescription));

        return $this->success(
            __('messages.operation_success'),
            new PrescriptionResource(
                $prescription->load(['medicaments', 'patient', 'room', 'specialty', 'user']),
            ),
        );
    }

    public function nullPrescription(int $prescription): JsonResponse
    {
        $prescription = auth()
            ->user()
            ->prescriptions()
            ->where('status', config('custom.prescription.status_keys.active'))
            ->findOrFail($prescription);

        $prescription->update([
            'status' => config('custom.prescription.status_keys.nulled'),
        ]);

        return $this->success(
            __('messages.operation_success'),
            new PrescriptionResource(
                $prescription->load(['medicaments', 'patient', 'room', 'specialty', 'user']),
            ),
        );
    }

    /**
     * Display the specified resource.
     */
    public function getFile(string $prescription)
    {
        $prescription = auth()
            ->user()
            ->prescriptions()
            ->where('status', config('custom.prescription.status_keys.active'))
            ->whereNotNull('expires_at')
            ->with(['signed_file', 'unsigned_file'])
            ->findOrFail($prescription);

        // If expires_at date equals updated_at date, prefer unsigned file path
        $useUnsigned = false;
        $expiresDate = Carbon::parse($prescription->expires_at)->toDateString();
        $updatedDate = Carbon::parse($prescription->updated_at)->toDateString();
        if ($expiresDate === $updatedDate) {
            $useUnsigned = true;
        }
        $file = ($useUnsigned ? $prescription->unsigned_file : $prescription->signed_file)
            ?? $prescription->signed_file
            ?? $prescription->unsigned_file;

        if (! $file || ! Storage::disk('local')->exists($file->path)) {
            return $this->error(
                __('messages.not_found'),
                404
            );
        }

        $path = Storage::disk('local')->path($file->path);

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Embed a timestamp token into an existing signed PDF.
     *
     * This method adds the timestamp token to the PDF's signature dictionary
     * as a /SigTst attribute, which is the standard way to embed timestamps
     * in PDF signatures according to the PAdES (PDF Advanced Electronic Signatures) standard.
     *
     * @param  string  $pdfContent  The signed PDF content
     * @param  string  $timestampToken  The RFC 3161 timestamp token
     * @return string The PDF with embedded timestamp
     */
    private function embedTimestampInPdf(string $pdfContent, string $timestampToken): string
    {
        // Find the signature dictionary in the PDF
        // The signature dictionary contains /Contents which holds the signature value
        // We need to add /SigTst with the timestamp token

        // Look for the signature object pattern
        // In PDF, the signature dictionary looks like:
        // /Type /Sig /Filter /Adobe.PPKLite /SubFilter /adbe.pkcs7.detached /Contents <hex> ...

        // Find the /Contents entry in the signature
        $pattern = '/\/Contents\s*<([0-9A-Fa-f]+)>/';
        if (preg_match($pattern, $pdfContent, $matches)) {
            $hexContents = $matches[1];
            $byteRange = [];

            // Find the /ByteRange entry
            $byteRangePattern = '/\/ByteRange\s*\[(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\]/';
            if (preg_match($byteRangePattern, $pdfContent, $byteRangeMatches)) {
                $byteRange = [
                    (int) $byteRangeMatches[1],
                    (int) $byteRangeMatches[2],
                    (int) $byteRangeMatches[3],
                    (int) $byteRangeMatches[4],
                ];
            }

            // Calculate where to insert the timestamp
            // We'll add it after the /Contents entry
            $insertPos = strpos($pdfContent, $matches[0]) + strlen($matches[0]);

            // Encode the timestamp token as hex
            $timestampHex = bin2hex($timestampToken);

            // Build the SigTst dictionary
            $sigTst = ' /SigTst << /Type /SigTst /Version 1 /Objects [ << /ObjRef 1 /UseInstalled 0 >> ] /TimeStampToken <'.$timestampHex.'> >>';

            // Insert the timestamp after the Contents entry
            $pdfContent = substr_replace($pdfContent, $sigTst, $insertPos, 0);
        }

        return $pdfContent;
    }
}
