<?php

namespace App\Services\Statistics;

use App\Models\Brand;
use App\Models\Laboratory;
use App\Models\Prescription;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PrescriptionAnalyticsService
{
    /**
     * Build the filtered prescription query based on request parameters.
     */
    public function buildFilteredQuery(Request $request): Builder
    {
        $query = Prescription::query();

        if (auth()->check()) {
            $query->where('prescriptions.user_id', auth()->id());
        }

        if ($request->filled('room_id')) {
            $query->where('prescriptions.room_id', $request->integer('room_id'));
        }

        if ($request->filled('from')) {
            $query->where('prescriptions.created_at', '>=', Carbon::parse($request->input('from'))->startOfDay());
        }

        if ($request->filled('to')) {
            $query->where('prescriptions.created_at', '<=', Carbon::parse($request->input('to'))->endOfDay());
        }

        return $query;
    }

    /**
     * Get KPI overview metrics.
     *
     * @return array<string, mixed>
     */
    public function getOverview(Request $request): array
    {
        $prescriptionQuery = $this->buildFilteredQuery($request);
        $prescriptionSubquery = (clone $prescriptionQuery)->select('prescriptions.id');

        $activeKey = (int) config('custom.prescription.status_keys.active', 1);
        $partiallyDispensedKey = (int) config('custom.prescription.status_keys.partially_dispensed', 2);
        $fullyDispensedKey = (int) config('custom.prescription.status_keys.fully_dispensed', 3);

        $prescriptionStats = (clone $prescriptionQuery)
            ->selectRaw("
                COUNT(*) as total_prescriptions,
                COUNT(CASE WHEN status = {$activeKey} THEN 1 END) as active_prescriptions,
                COUNT(CASE WHEN status IN ({$partiallyDispensedKey}, {$fullyDispensedKey}) THEN 1 END) as dispensed_prescriptions,
                COUNT(DISTINCT patient_id) as total_patients_attended
            ")
            ->first();

        $totalPrescriptions = (int) ($prescriptionStats->total_prescriptions ?? 0);
        $activePrescriptions = (int) ($prescriptionStats->active_prescriptions ?? 0);
        $dispensedPrescriptions = (int) ($prescriptionStats->dispensed_prescriptions ?? 0);
        $totalPatientsAttended = (int) ($prescriptionStats->total_patients_attended ?? 0);

        $medicamentStats = DB::table('medicament_prescriptions')
            ->whereIn('prescription_id', $prescriptionSubquery)
            ->whereNull('deleted_at')
            ->selectRaw('
                COUNT(*) as total_medicaments_prescribed,
                COALESCE(SUM(medicament_quantity), 0) as total_units_prescribed
            ')
            ->first();

        $totalMedicamentsPrescribed = (int) ($medicamentStats->total_medicaments_prescribed ?? 0);
        $totalUnitsPrescribed = (int) ($medicamentStats->total_units_prescribed ?? 0);

        $avgMedicaments = $totalPrescriptions > 0
            ? round($totalMedicamentsPrescribed / $totalPrescriptions, 1)
            : 0;

        return [
            'total_prescriptions' => $totalPrescriptions,
            'active_prescriptions' => $activePrescriptions,
            'dispensed_prescriptions' => $dispensedPrescriptions,
            'total_patients_attended' => $totalPatientsAttended,
            'total_medicaments_prescribed' => $totalMedicamentsPrescribed,
            'total_units_prescribed' => (int) $totalUnitsPrescribed,
            'average_medicaments_per_prescription' => $avgMedicaments,
        ];
    }

    /**
     * Statistics by active ingredient / medicament.
     *
     * @return array{data: Collection, total_items: int}
     */
    public function getByMedicament(Request $request): array
    {
        $prescriptionSubquery = $this->buildFilteredQuery($request)->select('prescriptions.id');
        $limit = $request->integer('limit', 10);

        $totalItems = DB::table('medicament_prescriptions')
            ->whereIn('prescription_id', $prescriptionSubquery)
            ->whereNull('deleted_at')
            ->count();

        $stats = DB::table('medicament_prescriptions as mp')
            ->join('medicaments as m', 'mp.medicament_id', '=', 'm.id')
            ->whereIn('mp.prescription_id', $prescriptionSubquery)
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

        return [
            'data' => $stats,
            'total_items' => $totalItems,
        ];
    }

    /**
     * Statistics by recommended brand.
     *
     * @return array{data: Collection, total_branded: int}
     */
    public function getByBrand(Request $request): array
    {
        $prescriptionSubquery = $this->buildFilteredQuery($request)->select('prescriptions.id');
        $limit = $request->integer('limit', 10);

        $totalBranded = DB::table('medicament_prescriptions')
            ->whereIn('prescription_id', $prescriptionSubquery)
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
            ->whereIn('mp.prescription_id', $prescriptionSubquery)
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

        return [
            'data' => $stats,
            'total_branded' => $totalBranded,
        ];
    }

    /**
     * Statistics by pharmaceutical laboratory.
     *
     * @return array{data: Collection, total_items: int}
     */
    public function getByLaboratory(Request $request): array
    {
        $prescriptionSubquery = $this->buildFilteredQuery($request)->select('prescriptions.id');
        $limit = $request->integer('limit', 10);

        $totalWithLab = DB::table('medicament_prescriptions as mp')
            ->leftJoin('laboratories as l_direct', 'mp.laboratory_id', '=', 'l_direct.id')
            ->leftJoin('brands as b', 'mp.brand_id', '=', 'b.id')
            ->leftJoin('laboratories as l_brand', 'b.laboratory_id', '=', 'l_brand.id')
            ->whereIn('mp.prescription_id', $prescriptionSubquery)
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
            ->whereIn('mp.prescription_id', $prescriptionSubquery)
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

        return [
            'data' => $stats,
            'total_items' => $totalWithLab,
        ];
    }

    /**
     * Statistics by patient.
     */
    public function getByPatient(Request $request): Collection
    {
        $prescriptionQuery = $this->buildFilteredQuery($request);
        $limit = $request->integer('limit', 10);

        $patientList = (clone $prescriptionQuery)
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
            ->get();

        $patientIds = $patientList->pluck('patient_id')->filter()->all();

        $distinctCounts = [];
        if (! empty($patientIds)) {
            $distinctCounts = DB::table('medicament_prescriptions as mp')
                ->join('prescriptions as pr', 'mp.prescription_id', '=', 'pr.id')
                ->whereIn('pr.patient_id', $patientIds)
                ->when(auth()->check(), fn ($q) => $q->where('pr.user_id', auth()->id()))
                ->whereNull('mp.deleted_at')
                ->select('pr.patient_id', DB::raw('COUNT(DISTINCT mp.medicament_id) as count'))
                ->groupBy('pr.patient_id')
                ->pluck('count', 'patient_id')
                ->all();
        }

        return $patientList->map(function ($item) use ($distinctCounts) {
            $item->patient_name = "{$item->first_name} {$item->last_name}";
            $item->distinct_medicaments = (int) ($distinctCounts[$item->patient_id] ?? 0);

            return $item;
        });
    }

    /**
     * Monthly or daily prescription timeline.
     */
    public function getTimeline(Request $request): Collection
    {
        $prescriptionQuery = $this->buildFilteredQuery($request);
        $driver = DB::connection()->getDriverName();

        $periodExpr = match ($driver) {
            'pgsql' => "TO_CHAR(prescriptions.created_at, 'YYYY-MM')",
            'mysql', 'mariadb' => "DATE_FORMAT(prescriptions.created_at, '%Y-%m')",
            default => "strftime('%Y-%m', prescriptions.created_at)",
        };

        return (clone $prescriptionQuery)
            ->select(
                DB::raw("{$periodExpr} as period"),
                DB::raw('COUNT(prescriptions.id) as total')
            )
            ->groupBy('period')
            ->orderBy('period', 'asc')
            ->get();
    }

    /**
     * Cached catalog of laboratories.
     */
    public function getLaboratoriesCatalog(): Collection
    {
        return Cache::remember('catalog_laboratories', 3600, function () {
            return Laboratory::with('brands')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Cached catalog of brands.
     */
    public function getBrandsCatalog(): Collection
    {
        return Cache::remember('catalog_brands', 3600, function () {
            return Brand::with('laboratory')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        });
    }
}
