<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Postulante;
use App\Models\PostulanteHasBeneficiary;
use Illuminate\Support\Facades\DB;

class CleanOrphanBeneficiaries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'beneficiaries:clean-orphans {--dry-run : Muestra los registros huérfanos sin aplicar cambios}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Detecta y aplica soft-delete a los registros de postulante_has_beneficiaries cuyo miembro o titular haya sido eliminado lógicamente.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        $this->info("Buscando registros huérfanos en 'postulante_has_beneficiaries'...");

        $trashedIds = Postulante::onlyTrashed()->pluck('id');

        // 1. Huérfanos por miembro eliminado
        $byMember = PostulanteHasBeneficiary::whereNull('deleted_at')
            ->whereIn('miembro_id', $trashedIds)
            ->get();

        // 2. Huérfanos por titular eliminado
        $byTitular = PostulanteHasBeneficiary::whereNull('deleted_at')
            ->whereIn('postulante_id', $trashedIds)
            ->get();

        $totalMember = $byMember->count();
        $totalTitular = $byTitular->count();

        $this->info("Huérfanos por miembro eliminado: {$totalMember}");
        $this->info("Huérfanos por titular eliminado: {$totalTitular}");

        if ($totalMember === 0 && $totalTitular === 0) {
            $this->info("¡No se encontraron registros huérfanos!");
            return 0;
        }

        if ($isDryRun) {
            $this->comment("[DRY RUN] No se realizaron cambios en la base de datos.");
            return 0;
        }

        DB::transaction(function () use ($byMember, $byTitular) {
            foreach ($byMember as $b) {
                $postulante = Postulante::onlyTrashed()->find($b->miembro_id);
                $b->deleted_at = $postulante ? $postulante->deleted_at : now();
                $b->save();
            }

            foreach ($byTitular as $b) {
                $postulante = Postulante::onlyTrashed()->find($b->postulante_id);
                $b->deleted_at = $postulante ? $postulante->deleted_at : now();
                $b->save();
            }
        });

        $this->info("¡Limpieza completada con éxito! Se sincronizaron {$totalMember} relaciones de miembro y {$totalTitular} relaciones de titular.");

        return 0;
    }
}
