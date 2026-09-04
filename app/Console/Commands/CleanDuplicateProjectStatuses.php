<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProjectStatusF;
use Illuminate\Support\Facades\DB;

class CleanDuplicateProjectStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:clean-duplicates {--dry-run : Muestra los duplicados detectados sin realizar cambios} {--force : Elimina los duplicados sin solicitar confirmación}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Detecta y elimina registros consecutivos duplicados en el historial de estados de proyectos, conservando los registros con documentos adjuntos.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info("Analizando tabla 'project_status' en busca de duplicados...");

        $allStatuses = ProjectStatusF::with('imagen')->orderBy('project_id')->orderBy('id')->get();
        $byProject = $allStatuses->groupBy('project_id');

        $toDeleteIds = [];
        $summary = [];

        foreach ($byProject as $projectId => $statuses) {
            $prev = null;
            foreach ($statuses as $status) {
                if ($prev !== null) {
                    if ($prev->stage_id == $status->stage_id) {
                        $timeDiff = ($prev->created_at && $status->created_at) ? $prev->created_at->diffInSeconds($status->created_at) : 0;

                        // Duplicados dentro de los 10 minutos o consecutivos
                        if ($timeDiff < 600) {
                            $prevHasFiles = $prev->imagen->count() > 0;
                            $currHasFiles = $status->imagen->count() > 0;

                            if ($prevHasFiles && !$currHasFiles) {
                                $deleteId = $status->id;
                                $keepId = $prev->id;
                            } else if ($currHasFiles && !$prevHasFiles) {
                                $deleteId = $prev->id;
                                $keepId = $status->id;
                                $prev = $status; // Conservamos el actual que tiene archivos
                            } else {
                                $deleteId = $status->id;
                                $keepId = $prev->id;
                            }

                            $toDeleteIds[] = $deleteId;

                            $summary[] = [
                                'Proyecto' => $projectId,
                                'Etapa ID' => $status->stage_id,
                                'Mantener ID' => $keepId,
                                'Eliminar ID' => $deleteId,
                                'Archivos Mantener' => $keepId == $prev->id ? $prev->imagen->count() : $status->imagen->count(),
                                'Archivos Eliminar' => $deleteId == $status->id ? $status->imagen->count() : $prev->imagen->count(),
                                'Diferencia (seg)' => $timeDiff,
                            ];
                            continue;
                        }
                    }
                }
                $prev = $status;
            }
        }

        $uniqueDeleteIds = array_values(array_unique($toDeleteIds));
        $totalDuplicates = count($uniqueDeleteIds);

        if ($totalDuplicates === 0) {
            $this->info("¡No se encontraron registros duplicados!");
            return 0;
        }

        $this->table(
            ['Proyecto', 'Etapa ID', 'Mantener ID', 'Eliminar ID', 'Archivos Mantener', 'Archivos Eliminar', 'Diferencia (seg)'],
            array_slice($summary, 0, 20)
        );

        if (count($summary) > 20) {
            $this->comment("...y " . (count($summary) - 20) . " registros duplicados más.");
        }

        $this->warn("Total de registros duplicados detectados para eliminar: {$totalDuplicates}");

        if ($isDryRun) {
            $this->info("[DRY RUN] No se realizaron cambios en la base de datos.");
            return 0;
        }

        if (!$force && !$this->confirm("¿Deseas eliminar permanentemente estos {$totalDuplicates} registros duplicados?")) {
            $this->info("Operación cancelada.");
            return 0;
        }

        DB::transaction(function () use ($uniqueDeleteIds) {
            ProjectStatusF::whereIn('id', $uniqueDeleteIds)->delete();
        });

        $this->info("¡Limpieza completada con éxito! Se eliminaron {$totalDuplicates} registros duplicados de la base de datos.");

        return 0;
    }
}
