<?php

namespace App\Http\Controllers;

use App\Services\Statistics\PrescriptionAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    public function __construct(
        protected PrescriptionAnalyticsService $analyticsService
    ) {}

    /**
     * General KPI metrics.
     */
    public function overview(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->analyticsService->getOverview($request),
        ]);
    }

    /**
     * Statistics by active ingredient / medicament.
     */
    public function byMedicament(Request $request): JsonResponse
    {
        $result = $this->analyticsService->getByMedicament($request);

        return response()->json([
            'success' => true,
            'data' => $result['data'],
            'total_items' => $result['total_items'],
        ]);
    }

    /**
     * Statistics by recommended brand.
     */
    public function byBrand(Request $request): JsonResponse
    {
        $result = $this->analyticsService->getByBrand($request);

        return response()->json([
            'success' => true,
            'data' => $result['data'],
            'total_branded' => $result['total_branded'],
        ]);
    }

    /**
     * Statistics by pharmaceutical laboratory.
     */
    public function byLaboratory(Request $request): JsonResponse
    {
        $result = $this->analyticsService->getByLaboratory($request);

        return response()->json([
            'success' => true,
            'data' => $result['data'],
            'total_items' => $result['total_items'],
        ]);
    }

    /**
     * Statistics by patient (top prescribed patients and adherence indicators).
     */
    public function byPatient(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->analyticsService->getByPatient($request),
        ]);
    }

    /**
     * Monthly or daily prescription timeline.
     */
    public function timeline(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->analyticsService->getTimeline($request),
        ]);
    }

    /**
     * Catalog of laboratories.
     */
    public function laboratories(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->analyticsService->getLaboratoriesCatalog(),
        ]);
    }

    /**
     * Catalog of brands.
     */
    public function brands(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->analyticsService->getBrandsCatalog(),
        ]);
    }
}
