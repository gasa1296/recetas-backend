<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Laboratory;
use App\Models\Prescription;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticController extends Controller
{
    /**
     * Apply date range and doctor scope to prescription queries.
     */
    protected function filterPrescriptions(Request $request)
    {
        $query = Prescription::query();

        // If authenticated user is a doctor, filter by their prescriptions
        if (auth()->check()) {
            $query->where('prescriptions.user_id', auth()->id());
        }

        if ($request->filled('room_id')) {
            $query->where('prescriptions.room_id', $request->integer('room_id'));
        }

        if ($request->filled('from')) {
            $query->whereDate('prescriptions.created_at', '>=', Carbon::parse($request->input('from')));
        }

        if ($request->filled('to')) {
            $query->whereDate('prescriptions.created_at', '<=', Carbon::parse($request->input('to')));
        }

        return $query;
    }

    /**
     * General KPI metrics.
     */
    public function overview(Request $request): JsonResponse
    {
        $prescriptionQuery = $this->filterPrescriptions($request);
        $prescriptionIds = (clone $prescriptionQuery)->pluck('id');

        $totalPrescriptions = $prescriptionIds->count();
        $activePrescriptions = (clone $prescriptionQuery)->where('status', config('custom.prescription.status_keys.active', 1))->count();
        $dispensedPrescriptions = (clone $prescriptionQuery)->whereIn('status', [
            config('custom.prescription.status_keys.partially_dispensed', 2),
            config('custom.prescription.status_keys.fully_dispensed', 3),
        ])->count();

        $totalPatientsAttended = (clone $prescriptionQuery)->distinct('patient_id')->count('patient_id');

        $medicamentPivotQuery = DB::table('medicament_prescriptions')
            ->whereIn('prescription_id', $prescriptionIds)
            ->whereNull('deleted_at');

        $totalMedicamentsPrescribed = (clone $medicamentPivotQuery)->count();
        $totalUnitsPrescribed = (clone $medicamentPivotQuery)->sum('medicament_quantity');

        $avgMedicaments = $totalPrescriptions > 0
            ? round($totalMedicamentsPrescribed / $totalPrescriptions, 1)
            : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'total_prescriptions' => $totalPrescriptions,
                'active_prescriptions' => $activePrescriptions,
                'dispensed_prescriptions' => $dispensedPrescriptions,
                'total_patients_attended' => $totalPatientsAttended,
                'total_medicaments_prescribed' => $totalMedicamentsPrescribed,
                'total_units_prescribed' => (int) $totalUnitsPrescribed,
                'average_medicaments_per_prescription' => $avgMedicaments,
            ],
        ]);
    }

    /**
     * Statistics by active ingredient / medicament.
     */
    public function byMedicament(Request $request): JsonResponse
    {
        $prescriptionIds = $this->filterPrescriptions($request)->pluck('id');
        $limit = $request->integer('limit', 10);

        $totalItems = DB::table('medicament_prescriptions')
            ->whereIn('prescription_id', $prescriptionIds)
            ->whereNull('deleted_at')
            ->count();

        $stats = DB::table('medicament_prescriptions as mp')
            ->join('medicaments as m', 'mp.medicament_id', '=', 'm.id')
            ->whereIn('mp.prescription_id', $prescriptionIds)
            ->whereNull('mp.deleted_at')
            ->select(
                'm.id as medicament_id',
                'm.active_ingredient',
                'm.type',
                'm.group',
                DB::raw('COUNT(mp.id) as prescription_count'),
                DB::raw('SUM(mp.medicament_quantity) as total_quantity')
            )
            ->groupBy('m.id', 'm.active_ingredient', 'm.type', 'm.group')
            ->orderByDesc('prescription_count')
            ->limit($limit)
            ->get()
            ->map(function ($item) use ($totalItems) {
                $item->percentage = $totalItems > 0
                    ? round(($item->prescription_count / $totalItems) * 100, 1)
                    : 0;
                $item->total_quantity = (int) $item->total_quantity;

                return $item;
            });

        return response()->json([
            'success' => true,
            'data' => $stats,
            'total_items' => $totalItems,
        ]);
    }

    /**
     * Statistics by recommended brand.
     */
    public function byBrand(Request $request): JsonResponse
    {
        $prescriptionIds = $this->filterPrescriptions($request)->pluck('id');
        $limit = $request->integer('limit', 10);

        $totalBranded = DB::table('medicament_prescriptions')
            ->whereIn('prescription_id', $prescriptionIds)
            ->whereNull('deleted_at')
            ->where(function ($q) {
                $q->whereNotNull('recommended_brand')
                    ->where('recommended_brand', '!=', '')
                    ->orWhereNotNull('brand_id');
            })
            ->count();

        $stats = DB::table('medicament_prescriptions as mp')
            ->leftJoin('brands as b', 'mp.brand_id', '=', 'b.id')
            ->leftJoin('laboratories as l', 'b.laboratory_id', '=', 'l.id')
            ->whereIn('mp.prescription_id', $prescriptionIds)
            ->whereNull('mp.deleted_at')
            ->where(function ($q) {
                $q->whereNotNull('mp.recommended_brand')
                    ->where('mp.recommended_brand', '!=', '')
                    ->orWhereNotNull('mp.brand_id');
            })
            ->select(
                DB::raw('COALESCE(b.name, mp.recommended_brand) as brand_name'),
                'l.name as laboratory_name',
                DB::raw('COUNT(mp.id) as prescription_count')
            )
            ->groupBy(DB::raw('COALESCE(b.name, mp.recommended_brand)'), 'l.name')
            ->orderByDesc('prescription_count')
            ->limit($limit)
            ->get()
            ->map(function ($item) use ($totalBranded) {
                $item->percentage = $totalBranded > 0
                    ? round(($item->prescription_count / $totalBranded) * 100, 1)
                    : 0;

                return $item;
            });

        return response()->json([
            'success' => true,
            'data' => $stats,
            'total_branded' => $totalBranded,
        ]);
    }

    /**
     * Statistics by pharmaceutical laboratory.
     */
    public function byLaboratory(Request $request): JsonResponse
    {
        $prescriptionIds = $this->filterPrescriptions($request)->pluck('id');
        $limit = $request->integer('limit', 10);

        // Subquery or direct join with laboratory through brand or laboratory_id
        $totalWithLab = DB::table('medicament_prescriptions as mp')
            ->leftJoin('laboratories as l_direct', 'mp.laboratory_id', '=', 'l_direct.id')
            ->leftJoin('brands as b', 'mp.brand_id', '=', 'b.id')
            ->leftJoin('laboratories as l_brand', 'b.laboratory_id', '=', 'l_brand.id')
            ->whereIn('mp.prescription_id', $prescriptionIds)
            ->whereNull('mp.deleted_at')
            ->where(function ($q) {
                $q->whereNotNull('l_direct.name')
                    ->orWhereNotNull('l_brand.name');
            })
            ->count();

        $stats = DB::table('medicament_prescriptions as mp')
            ->leftJoin('laboratories as l_direct', 'mp.laboratory_id', '=', 'l_direct.id')
            ->leftJoin('brands as b', 'mp.brand_id', '=', 'b.id')
            ->leftJoin('laboratories as l_brand', 'b.laboratory_id', '=', 'l_brand.id')
            ->whereIn('mp.prescription_id', $prescriptionIds)
            ->whereNull('mp.deleted_at')
            ->where(function ($q) {
                $q->whereNotNull('l_direct.name')
                    ->orWhereNotNull('l_brand.name');
            })
            ->select(
                DB::raw('COALESCE(l_direct.name, l_brand.name) as laboratory_name'),
                DB::raw('COALESCE(l_direct.country, l_brand.country) as country'),
                DB::raw('COUNT(mp.id) as prescription_count')
            )
            ->groupBy(DB::raw('COALESCE(l_direct.name, l_brand.name)'), DB::raw('COALESCE(l_direct.country, l_brand.country)'))
            ->orderByDesc('prescription_count')
            ->limit($limit)
            ->get()
            ->map(function ($item) use ($totalWithLab) {
                $item->percentage = $totalWithLab > 0
                    ? round(($item->prescription_count / $totalWithLab) * 100, 1)
                    : 0;

                return $item;
            });

        return response()->json([
            'success' => true,
            'data' => $stats,
            'total_items' => $totalWithLab,
        ]);
    }

    /**
     * Statistics by patient (top prescribed patients and adherence indicators).
     */
    public function byPatient(Request $request): JsonResponse
    {
        $prescriptionQuery = $this->filterPrescriptions($request);
        $limit = $request->integer('limit', 10);

        $stats = (clone $prescriptionQuery)
            ->join('patients as p', 'prescriptions.patient_id', '=', 'p.id')
            ->select(
                'p.id as patient_id',
                'p.first_name',
                'p.last_name',
                'p.identification',
                'p.gender',
                DB::raw('COUNT(prescriptions.id) as prescriptions_count'),
                DB::raw('MAX(prescriptions.created_at) as last_prescription_at')
            )
            ->groupBy('p.id', 'p.first_name', 'p.last_name', 'p.identification', 'p.gender')
            ->orderByDesc('prescriptions_count')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                $item->patient_name = "{$item->first_name} {$item->last_name}";

                // Count distinct medicaments prescribed to this patient
                $item->distinct_medicaments = DB::table('medicament_prescriptions as mp')
                    ->join('prescriptions as pr', 'mp.prescription_id', '=', 'pr.id')
                    ->where('pr.patient_id', $item->patient_id)
                    ->whereNull('mp.deleted_at')
                    ->distinct('mp.medicament_id')
                    ->count('mp.medicament_id');

                return $item;
            });

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Monthly or daily prescription timeline.
     */
    public function timeline(Request $request): JsonResponse
    {
        $prescriptionQuery = $this->filterPrescriptions($request);
        $driver = DB::connection()->getDriverName();

        $periodExpr = match ($driver) {
            'pgsql' => "TO_CHAR(prescriptions.created_at, 'YYYY-MM')",
            'mysql', 'mariadb' => "DATE_FORMAT(prescriptions.created_at, '%Y-%m')",
            default => "strftime('%Y-%m', prescriptions.created_at)",
        };

        $timeline = (clone $prescriptionQuery)
            ->select(
                DB::raw("{$periodExpr} as period"),
                DB::raw('COUNT(prescriptions.id) as total')
            )
            ->groupBy('period')
            ->orderBy('period', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $timeline,
        ]);
    }

    /**
     * Catalog of laboratories.
     */
    public function laboratories(): JsonResponse
    {
        $laboratories = Laboratory::with('brands')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $laboratories,
        ]);
    }

    /**
     * Catalog of brands.
     */
    public function brands(): JsonResponse
    {
        $brands = Brand::with('laboratory')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $brands,
        ]);
    }
}
