<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Postulante;
use App\Models\Project;
use App\Models\Modality;
use App\Models\Departamento;
use App\Models\Sat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    public function projects(Request $request)
    {
        $year = $request->get('year');
        $modalityId = $request->get('modality_id');
        $departmentId = $request->get('department_id');

        $projectQuery = Project::query();

        if ($year) {
            $projectQuery->whereYear('projects.created_at', $year);
        }
        if ($modalityId) {
            $projectQuery->where('modalidad_id', $modalityId);
        }
        if ($departmentId) {
            $projectQuery->where('state_id', $departmentId);
        }

        // 1. Proyectos por Año
        $projectsByYear = Project::select(DB::raw('EXTRACT(YEAR FROM projects.created_at) as year'), DB::raw('count(*) as total'))
            ->groupBy('year')->orderBy('year')->pluck('total', 'year');

        // 2. Proyectos por Modalidad
        $projectsByModality = (clone $projectQuery)
            ->join('modalities', 'projects.modalidad_id', '=', 'modalities.id')
            ->select('modalities.name as label', DB::raw('count(*) as total'))
            ->groupBy('modalities.id', 'modalities.name')->get();

        // 3. Proyectos por Departamento
        $projectsByDept = (clone $projectQuery)
            ->join('departamento', 'projects.state_id', '=', 'departamento.DptoId')
            ->select('departamento.DptoNom as label', DB::raw('count(*) as total'))
            ->groupBy('departamento.DptoId', 'departamento.DptoNom')->get();

        // 4. Proyectos por Estado
        $projectsByStatus = (clone $projectQuery)
            ->join('project_status', function ($join) {
                $join->on('projects.id', '=', 'project_status.project_id')
                    ->whereRaw('project_status.id IN (SELECT MAX(id) FROM project_status GROUP BY project_id)');
            })
            ->join('stages', 'project_status.stage_id', '=', 'stages.id')
            ->select('stages.name as label', DB::raw('count(*) as total'))
            ->groupBy('stages.id', 'stages.name')->get();

        $totalProjects = (clone $projectQuery)->count();

        // 5. Proyectos por SAT
        $satCounts = (clone $projectQuery)
            ->select('sat_id', DB::raw('count(*) as total'))
            ->whereNotNull('sat_id')
            ->where('sat_id', '!=', '')
            ->groupBy('sat_id')
            ->orderBy('total', 'desc')
            ->limit(15)
            ->get();

        $projectsBySat = $satCounts->map(function ($item) {
            $satId = trim($item->sat_id);
            $sat = Sat::where('NucCod', $satId)->first();
            if (!$sat) {
                // Try again without trimming if it failed (sometimes data is weirdly padded)
                $sat = Sat::where('NucCod', $item->sat_id)->first();
            }
            return (object) [
                'label' => $sat ? trim($sat->NucNomSat) : "ID: " . $satId,
                'total' => $item->total
            ];
        });

        $modalities = Modality::all();
        $departments = Departamento::all();
        $years = Project::select(DB::raw('EXTRACT(YEAR FROM projects.created_at) as year'))->distinct()->orderBy('year', 'desc')->pluck('year');

        return view('admin.statistics.projects', compact(
            'projectsByYear',
            'projectsByModality',
            'projectsByDept',
            'projectsByStatus',
            'projectsBySat',
            'totalProjects',
            'modalities',
            'departments',
            'years',
            'year',
            'modalityId',
            'departmentId'
        ));
    }

    public function postulantes(Request $request)
    {
        $year = $request->get('year');
        $modalityId = $request->get('modality_id');
        $departmentId = $request->get('department_id');

        $projectQuery = Project::query();
        if ($year) $projectQuery->whereYear('projects.created_at', $year);
        if ($modalityId) $projectQuery->where('modalidad_id', $modalityId);
        if ($departmentId) $projectQuery->where('state_id', $departmentId);

        $postulanteQuery = Postulante::query();
        if ($year || $modalityId || $departmentId) {
            $filteredProjectIds = $projectQuery->pluck('id');
            $postulanteQuery->whereIn('postulantes.id', function ($query) use ($filteredProjectIds) {
                $query->select('postulante_id')->from('project_has_postulantes')->whereIn('project_id', $filteredProjectIds);
            });
        }

        $totalPostulantes = (clone $postulanteQuery)->count();

        // Sexo
        $postulantesByGender = (clone $postulanteQuery)
            ->select('gender as label', DB::raw('count(*) as total'))
            ->groupBy('gender')->get();

        // Discapacidad
        $postulantesByDisability = (clone $postulanteQuery)
            ->leftJoin('postulante_has_discapacidad', 'postulantes.id', '=', 'postulante_has_discapacidad.postulante_id')
            ->select(DB::raw("CASE WHEN postulante_has_discapacidad.discapacidad_id = 1 OR postulante_has_discapacidad.discapacidad_id IS NULL THEN 'Sin Discapacidad' ELSE 'Con Discapacidad' END as label"), DB::raw('count(*) as total'))
            ->groupBy(DB::raw("CASE WHEN postulante_has_discapacidad.discapacidad_id = 1 OR postulante_has_discapacidad.discapacidad_id IS NULL THEN 'Sin Discapacidad' ELSE 'Con Discapacidad' END"))
            ->get();

        // Edades (Optimized attempt: use DB aggregation)
        // Note: birthdate is a date field, current year - birthyear is a good proxy for age grouping
        $currentYear = date('Y');
        $ageData = (clone $postulanteQuery)
            ->select(DB::raw("
                CASE
                    WHEN EXTRACT(YEAR FROM AGE(CAST(birthdate AS DATE))) < 18 THEN 'Menores (<18)'
                    WHEN EXTRACT(YEAR FROM AGE(CAST(birthdate AS DATE))) BETWEEN 18 AND 35 THEN 'Jovenes (18-35)'
                    WHEN EXTRACT(YEAR FROM AGE(CAST(birthdate AS DATE))) BETWEEN 36 AND 60 THEN 'Adultos (36-60)'
                    WHEN EXTRACT(YEAR FROM AGE(CAST(birthdate AS DATE))) > 60 THEN 'Adultos Mayores (60+)'
                    ELSE 'No especificado'
                END as label
            "), DB::raw('count(*) as total'))
            ->groupBy('label')
            ->get()
            ->pluck('total', 'label');

        $ageRanges = [
            'Menores (<18)' => $ageData['Menores (<18)'] ?? 0,
            'Jovenes (18-35)' => $ageData['Jovenes (18-35)'] ?? 0,
            'Adultos (36-60)' => $ageData['Adultos (36-60)'] ?? 0,
            'Adultos Mayores (60+)' => $ageData['Adultos Mayores (60+)'] ?? 0,
            'No especificado' => $ageData['No especificado'] ?? 0,
        ];

        $modalities = Modality::all();
        $departments = Departamento::all();
        $years = Project::select(DB::raw('EXTRACT(YEAR FROM projects.created_at) as year'))->distinct()->orderBy('year', 'desc')->pluck('year');

        return view('admin.statistics.postulantes', compact(
            'totalPostulantes',
            'postulantesByGender',
            'postulantesByDisability',
            'ageRanges',
            'modalities',
            'departments',
            'years',
            'year',
            'modalityId',
            'departmentId'
        ));
    }
}
