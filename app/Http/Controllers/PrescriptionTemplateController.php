<?php

namespace App\Http\Controllers;

use App\Http\Requests\PrescriptionTemplateRequest;
use App\Http\Resources\PrescriptionTemplateResource;
use Illuminate\Http\JsonResponse;

class PrescriptionTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $templates = auth()
            ->user()
            ->prescriptionTemplates()
            ->with('medicaments')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return $this->success(
            __('messages.operation_success'),
            PrescriptionTemplateResource::collection($templates),
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PrescriptionTemplateRequest $request): JsonResponse
    {
        $data = $request->validated();

        $template = auth()
            ->user()
            ->prescriptionTemplates()
            ->create($data);

        if (! empty($data['medicaments'])) {
            $template->medicaments()->sync($data['medicaments']);
        }

        return $this->success(
            __('messages.operation_success'),
            new PrescriptionTemplateResource($template->loadMissing('medicaments')),
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(int $prescriptionTemplate): JsonResponse
    {
        $template = auth()
            ->user()
            ->prescriptionTemplates()
            ->findOrFail($prescriptionTemplate);

        return $this->success(
            __('messages.operation_success'),
            new PrescriptionTemplateResource($template->loadMissing('medicaments')),
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        PrescriptionTemplateRequest $request,
        int $prescriptionTemplate,
    ): JsonResponse {
        $template = auth()
            ->user()
            ->prescriptionTemplates()
            ->findOrFail($prescriptionTemplate);
        $data = $request->validated();
        $template->update($data);

        if (! empty($data['medicaments'])) {
            $template->medicaments()->sync($data['medicaments']);
        }

        return $this->success(
            __('messages.operation_success'),
            new PrescriptionTemplateResource($template->loadMissing('medicaments')),
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $prescriptionTemplate): JsonResponse
    {
        $template = auth()
            ->user()
            ->prescriptionTemplates()
            ->findOrFail($prescriptionTemplate);
        $template->delete();

        return $this->success(
            __('messages.operation_success'),
        );
    }
}
