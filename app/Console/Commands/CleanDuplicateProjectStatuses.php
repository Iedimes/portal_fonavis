<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProjectStatusF;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CleanDuplicateProjectStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:clean-duplicates {--date= : Fecha a revisar (AAAA-MM-DD); por defecto, ayer} {--year= : Año calendario completo a revisar (AAAA)} {--dry-run : Muestra los duplicados detectados sin realizar cambios} {--force : Elimina sin solicitar confirmación}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Detecta duplicados consecutivos de una fecha o año y elimina solo los que no tienen adjuntos.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $force = $this->option('force');
        $date = $this->option('date');
        $year = $this->option('year');

        if ($date && $year) {
            $this->error('Usa --date o --year, no ambos a la vez.');
            return 1;
        }

        if ($year && (!preg_match('/^\d{4}$/', $year) || (int) $year < 1000)) {
            $this->error('El año debe tener el formato AAAA.');
            return 1;
        }

        if (!$year) {
            $date = $date ?: Carbon::yesterday()->toDateString();
            $parsedDate = Carbon::createFromFormat('!Y-m-d', $date);
            if (!$parsedDate || $parsedDate->format('Y-m-d') !== $date) {
                $this->error('La fecha debe tener el formato AAAA-MM-DD y ser válida.');
                return 1;
            }
        }

        $period = $year ? "del año {$year}" : "del {$date}";
        $this->info("Analizando estados duplicados {$period}...");

        $query = ProjectStatusF::with('imagen');
        if ($year) {
            $query->whereYear('created_at', $year);
        } else {
            $query->whereDate('created_at', $date);
        }

        $allStatuses = $query
            ->orderBy('project_id')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();
        $byProject = $allStatuses->groupBy('project_id');

        $toDeleteIds = [];
        $summary = [];

        foreach ($byProject as $projectId => $statuses) {
            $previous = null;
            $firstInRun = null;
            foreach ($statuses as $status) {
                $timeDiff = ($previous && $previous->created_at && $status->created_at)
                    ? $previous->created_at->diffInSeconds($status->created_at)
                    : null;

                $isDuplicate = $previous
                    && $previous->stage_id == $status->stage_id
                    && $timeDiff !== null
                    && $timeDiff < 600;

                if ($isDuplicate) {
                    if (!$firstInRun) {
                        $firstInRun = $previous;
                    }

                    $hasFiles = $status->imagen->isNotEmpty();
                    $action = $hasFiles ? 'Conservar (tiene adjuntos)' : 'Eliminar';

                    if (!$hasFiles) {
                        $toDeleteIds[] = $status->id;
                    }

                    $summary[] = [
                        'Proyecto' => $projectId,
                        'Etapa ID' => $status->stage_id,
                        'Estado duplicado' => $status->id,
                        'Primer estado' => $firstInRun->id,
                        'Acción' => $action,
                        'Adjuntos' => $status->imagen->count(),
                        'Diferencia (seg)' => $timeDiff,
                    ];
                } else {
                    $firstInRun = $status;
                }

                $previous = $status;
            }
        }

        $uniqueDeleteIds = array_values(array_unique($toDeleteIds));
        $totalDuplicates = count($uniqueDeleteIds);

        if (count($summary) === 0) {
            $this->info("No se encontraron duplicados consecutivos {$period}.");
            return 0;
        }

        $this->table(
            ['Proyecto', 'Etapa ID', 'Estado duplicado', 'Primer estado', 'Acción', 'Adjuntos', 'Diferencia (seg)'],
            $summary
        );

        $this->warn("Duplicados sin adjuntos candidatos a eliminar: {$totalDuplicates}");

        if ($isDryRun) {
            $this->info("[DRY RUN] No se realizaron cambios en la base de datos.");
            return 0;
        }

        if ($totalDuplicates === 0) {
            $this->info('No hay registros sin adjuntos para eliminar.');
            return 0;
        }

        if (!$force && !$this->confirm("¿Deseas eliminar permanentemente estos {$totalDuplicates} registros duplicados?")) {
            $this->info("Operación cancelada.");
            return 0;
        }

        $deleted = DB::transaction(function () use ($uniqueDeleteIds) {
            return ProjectStatusF::whereIn('id', $uniqueDeleteIds)
                ->whereDoesntHave('imagen')
                ->delete();
        });

        $this->info("Limpieza completada. Se eliminaron {$deleted} registros sin adjuntos.");

        return 0;
    }
}
