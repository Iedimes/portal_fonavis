<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Documents;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\Sat;
use App\Models\Departamento;
use App\Models\Distrito;
use App\Models\Modality;
use App\Models\Stage;
use Illuminate\Http\Request;
use ZipArchive;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LegajoMasivoController extends Controller
{
    public function create()
    {
        // Optimización: Solo traer id y name, y usar pluck para evitar hidratación de modelos pesados
        $proyecto = Project::where('created_at', '>=', '2024-07-13')
            ->orWhere('updated_at', '>=', '2024-07-13')
            ->without(['getState', 'getModality', 'getCity', 'getEstado', 'getSat', 'documents', 'statuses'])
            ->orderBy('id', 'desc')
            ->get(['id', 'name']);

        $sats = Sat::whereNotNull('NucRuc')
            ->where('NucEst', 'H')
            ->orderBy('NucNomSat', 'asc')
            ->get(['NucNomSat', 'NucCod']);

        $depIgnored = [18, 19, 20, 21, 999];
        $states = Departamento::whereNotIn('DptoId', $depIgnored)
            ->orderBy('DptoNom', 'asc')
            ->get(['DptoId', 'DptoNom']);

        // Eliminamos el distrito de aquí ya que se carga dinámicamente vía AJAX en la vista
        $distrito = [];

        $modalities = Modality::all(['id', 'name']);
        $estado = Stage::all(['id', 'name']);

        return view('admin.legajo-masivo.create', compact('proyecto', 'sats', 'states', 'distrito', 'modalities', 'estado'));
    }

    public function generar(Request $request)
    {
        $query = Project::query();

        if ($request->filled('inicio') && $request->filled('fin')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->inicio)->startOfDay(),
                Carbon::parse($request->fin)->endOfDay()
            ]);
        }

        if ($request->filled('proyecto_id') && $request->proyecto_id != 0) {
            $query->where('id', $request->proyecto_id);
        }

        if ($request->filled('sat_id') && $request->sat_id != 0) {
            $query->where('sat_id', $request->sat_id);
        }

        if ($request->filled('state_id') && $request->state_id != 0) {
            $query->where('state_id', $request->state_id);
        }

        if ($request->filled('city_id') && $request->city_id != 0) {
            $query->where('city_id', $request->city_id);
        }

        if ($request->filled('modalidad_id') && $request->modalidad_id != 0) {
            $query->where('modalidad_id', $request->modalidad_id);
        }

        // CORRECCIÓN: filtrar por el último estado del proyecto
        if ($request->filled('stage_id') && $request->stage_id != 0) {
            $stage_id = $request->stage_id;
            $query->whereHas('getEstado', function ($q) use ($stage_id) {
                $q->where('stage_id', $stage_id)
                    ->where('id', function ($subQuery) {
                        $subQuery->select('id')
                            ->from('project_status')
                            ->whereColumn('project_status.project_id', 'projects.id')
                            ->orderBy('updated_at', 'desc')
                            ->limit(1);
                    });
            });
        }

        // Optimización: precargar documentos y dictámenes
        $projects = $query->with([
            'documents',
            'statuses.media',
            'statuses.getStage'
        ])->get();

        if ($projects->isEmpty()) {
            return response()->json(['error' => 'No se encontraron proyectos con los filtros seleccionados.'], 404);
        }

        // Preparar ZIP
        $zipFileName = 'legajos_masivos_' . now()->format('Ymd_His') . '.zip';
        $zipDir = storage_path('app/uploads/temp_zips');
        if (!file_exists($zipDir)) mkdir($zipDir, 0755, true);

        $zipFilePath = $zipDir . DIRECTORY_SEPARATOR . $zipFileName;
        $zip = new ZipArchive();
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return redirect()->back()->with('error', 'No se pudo crear el archivo ZIP.');
        }

        $filesAdded = 0;
        $limitFolder = 50;
        $limitFile = 60;

        foreach ($projects as $project) {
            /** @var \App\Models\Project $project */
            $projectFolderName = preg_replace('/[^A-Za-z0-9_\-]/', '_', substr($project->name, 0, $limitFolder)) . '_' . $project->id;

            // -------- Documentos --------
            $filesAdded += $this->agregarLegajoAlZip($zip, $project, $projectFolderName);

            // -------- Dictámenes / Resoluciones --------
            foreach ($project->statuses as $status) {
                $stageName = $status->getStage ? $status->getStage->name : 'Estado_' . $status->stage_id;
                $stageName = preg_replace('/[^A-Za-z0-9_\-]/', '_', substr($stageName, 0, $limitFolder));

                foreach ($status->media as $media) {
                    $path = $media->getPath();
                    if (file_exists($path)) {
                        $extension = pathinfo($media->file_name, PATHINFO_EXTENSION);
                        $filename = pathinfo($media->file_name, PATHINFO_FILENAME);
                        $safeFilename = substr($filename, 0, $limitFile) . '.' . $extension;

                        $zip->addFile(
                            $path,
                            $projectFolderName . '/Informes/' . $stageName . '/' . $safeFilename
                        );
                        $filesAdded++;
                        Log::info("Agregado dictamen: {$projectFolderName}/Informes/{$stageName}/{$safeFilename}");
                    } else {
                        Log::warning("Archivo de media no encontrado: {$path}");
                    }
                }
            }
        }

        $zip->close();

        if ($filesAdded === 0) {
            @unlink($zipFilePath);
            return response()->json(['error' => 'No se encontraron archivos para agregar al ZIP con los filtros seleccionados.'], 404);
        }

        if (ob_get_length()) ob_end_clean();
        return response()->download($zipFilePath, $zipFileName)->deleteFileAfterSend(true);
    }


    private function agregarLegajoAlZip(ZipArchive $zip, Project $project, string $projectFolderName)
    {
        $addedCount = 0;
        $limitFolder = 50;
        $limitFile = 80;
        $baseFolder = storage_path('app' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $project->id);

        if (!is_dir($baseFolder)) {
            Log::warning("Proyecto {$project->id}: carpeta de documentos no existe: {$baseFolder}");
            return $addedCount;
        }

        foreach ($project->documents as $doc) {
            $safeTitle = preg_replace('/[^A-Za-z0-9_\-]/', '_', strtoupper(substr($doc->title, 0, $limitFolder)));
            $folderName = "{$doc->document_id}-{$safeTitle}";

            $docFolder = $baseFolder . '/' . $doc->document_id;
            if (!is_dir($docFolder)) {
                $altDocFolder = $baseFolder . '/' . $doc->id;
                if (is_dir($altDocFolder)) $docFolder = $altDocFolder;
                else {
                    Log::warning("No existe carpeta para doc={$doc->document_id} ni id={$doc->id}");
                    continue;
                }
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($docFolder, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $filePath = $file->getRealPath();
                    // Obtener la ruta relativa original para mantener estructura de carpetas si existe
                    $relative = substr($filePath, strlen($docFolder) + 1);

                    // Separar ruta y archivo para acortar solo el archivo
                    $dir = dirname($relative);
                    $filename = basename($relative);
                    $extension = pathinfo($filename, PATHINFO_EXTENSION);
                    $nameOnly = pathinfo($filename, PATHINFO_FILENAME);

                    $safeFilename = substr($nameOnly, 0, $limitFile) . '.' . $extension;

                    // Reconstruir ruta relativa dentro del zip
                    // Nota: dirname devuelve '.' si no hay directorio padre
                    $zipPath = ($dir === '.' ? '' : $dir . '/') . $safeFilename;

                    $zip->addFile($filePath, $projectFolderName . '/Docs/' . $folderName . '/' . $zipPath);
                    $addedCount++;
                    Log::info("Agregado: {$projectFolderName}/Docs/{$folderName}/{$zipPath}");
                }
            }
        }

        return $addedCount;
    }
}
