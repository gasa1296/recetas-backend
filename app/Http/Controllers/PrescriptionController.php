<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinishPrescriptionRequest;
use App\Http\Requests\PrescriptionRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\PrescriptionCollection;
use App\Http\Resources\PrescriptionResource;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\TcpdfFpdi;

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
        return DB::transaction(function () use ($request) {
            $data = $request->validated();
            $data['prescription_hash'] = hash('sha256', json_encode($data));

            $prescription = auth()
                ->user()
                ->prescriptions()
                ->create($data);
            $medicaments = $request->input('medicament_data', []);

            $prescription->medicaments()->sync($medicaments);

            return $this->success(
                __('messages.operation_success'),
                new PrescriptionResource(
                    $prescription->load(['medicaments', 'patient', 'room', 'specialty']),
                ),
            );
        });
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
        return DB::transaction(function () use ($request, $prescription) {
            $prescription = auth()
                ->user()
                ->prescriptions()
                ->where('status', config('custom.prescription.status_keys.draft'))
                ->lockForUpdate()
                ->findOrFail($prescription);
            $prescription->update($request->validated());
            $medicaments = $request->input('medicament_data', []);
            $prescription->medicaments()->sync($medicaments);

            return $this->success(
                __('messages.operation_success'),
                new PrescriptionResource(
                    $prescription->load(['medicaments', 'patient', 'room', 'specialty']),
                ),
            );
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $prescription): JsonResponse
    {
        return DB::transaction(function () use ($prescription) {
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
        });
    }

    public function finishPrescription(FinishPrescriptionRequest $request, int $prescription): JsonResponse
    {
        return DB::transaction(function () use ($request, $prescription) {
            $prescription = auth()
                ->user()
                ->prescriptions()
                ->where('status', config('custom.prescription.status_keys.draft'))
                ->lockForUpdate()
                ->findOrFail($prescription);

            $prescription->loadMissing(['user', 'patient', 'room', 'specialty', 'medicaments']);
            $qrOptions = new QROptions;
            $qrOptions->outputType = 'png';
            $qrOptions->scale = 5;
            $qrCode = (new QRCode($qrOptions))->render(route('public.prescription.show', $prescription->prescription_hash));

            $pdfContent = Pdf::loadView('pdf.prescription_model_1', [
                'prescription' => $prescription,
                'signature' => $request->input('signature'),
                'qrCode' => $qrCode,
            ])->output();

            // 2. Initialize FPDI with TCPDF engine
            $pdf = new TcpdfFpdi;

            // 3. Configure the Digital Signature
            // Path to your .crt or .pfx certificate converted to PEM format
            $certificate = 'file://'.app_path('docker-compose/nginx/certs/recetas.localhost.crt');
            $privateKey = 'file://'.app_path('docker-compose/nginx/certs/recetas.localhost.key');

            $info = [
                'Name' => config('app.name'),
                'Location' => $prescription->room->address,
                'Reason' => $prescription->room->identification,
            ];
            $pdf->setSignature($certificate, $privateKey, '', '', 2, $info);

            // 4. Import the Dompdf document pages
            // Save temporary file because FPDI requires a filepath or a stream wrapper
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

            $prescription->handleUploadFile($pdf->Output(), 'signed');

            // Clean up temporary file
            unlink($tempFile);

            $prescription->update(['status' => config('custom.prescription.status_keys.active')]);

            return $this->success(
                __('messages.operation_success'),
            );
        });
    }

    /**
     * Display the specified resource.
     */
    public function getFile(string $prescription)
    {
        if (config('app.debug')) {
            $prescription = auth()
                ->user()
                ->prescriptions()
                ->where('status', config('custom.prescription.status_keys.active'))
                ->findOrFail($prescription);
        } else {
            $prescription = auth()
                ->user()
                ->prescriptions()
                ->where('status', config('custom.prescription.status_keys.active'))
                ->where('prescription_hash', $prescription)
                ->firstOrFail();
        }

        if ($prescription->signed_file) {
            $path = Storage::disk('local')->path($prescription->signed_file->path);

            return response()->file($path);
        }
    }
}
