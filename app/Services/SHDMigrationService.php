<?php

namespace App\Services;

use App\Models\BAMPER;
use App\Models\IVMSOL;
use App\Models\IVMSOL2;
use App\Models\Land;
use App\Models\POSSVS;
use App\Models\POSSVS1;
use App\Models\Postulante;
use App\Models\Project;
use App\Models\ProjectHasExpediente;
use App\Models\ProjectHasPostulantes;
use App\Models\SIG005;
use App\Models\SIG005L1;
use App\Models\SIG006;
use Illuminate\Support\Facades\Log;

class SHDMigrationService
{
    public function migrate(Project $project, string $planilla, string $expedienteNumber, Land $tipoterreno, string $perUser): array
    {
        $planilla = trim($planilla);

        if ($planilla === '') {
            return [
                'success' => false,
                'error' => 'Debe ingresar el número de planilla para migrar a SHD.',
            ];
        }

        $reg = POSSVS::where('PsvCod', $planilla)->first();

        if (!$reg) {
            return [
                'success' => false,
                'error' => 'No se encontró planilla SHD con ese número.',
            ];
        }

        $totalBamper = 0;
        $processedBamper = 0;
        $errorsBamper = 0;

        $totalSol = 0;
        $processedSol = 0;
        $errorsSol = 0;

        $totalShd = 0;
        $processedShd = 0;
        $errorsShd = 0;

        Log::info("=== INICIO MIGRACION SHD - Proyecto {$project->id} - Planilla {$planilla} ===");

        // Step 1: Migrate personas to BAMPER (create or update)
        Log::info("Step 1: Migrando personas a BAMPER...");
        $bamperResult = $this->migratePersonas($project, $perUser);
        $processedBamper = $bamperResult['processed'];
        $totalBamper = $bamperResult['total'];
        $errorsBamper = $bamperResult['errors'];
        Log::info("BAMPER: {$processedBamper}/{$totalBamper} procesados, {$errorsBamper} errores");

        // Step 2: Migrate solicitantes to IVMSOL/IVMSOL2 (create or update)
        Log::info("Step 2: Migrando solicitantes a IVMSOL/IVMSOL2...");
        $solResult = $this->migrateSolicitantes($project, $expedienteNumber, $perUser);
        $processedSol = $solResult['processed'];
        $totalSol = $solResult['total'];
        $errorsSol = $solResult['errors'];
        Log::info("SOLICITANTES: {$processedSol}/{$totalSol} procesados, {$errorsSol} errores");

        // Step 3: Update POSSVS header
        Log::info("Step 3: Actualizando POSSVS header...");
        $this->updateMainRecord($reg, $project);

        // Step 4: Migrate to POSSVS1 (create or update)
        Log::info("Step 4: Migrando a POSSVS1...");
        $shdResult = $this->processPostulantes($project->id, $planilla, $expedienteNumber, $tipoterreno, $perUser);

        Log::info("=== FIN MIGRACION SHD ===");
        $processedShd = $shdResult['processed'];
        $totalShd = $shdResult['total'];
        $errorsShd = $shdResult['errors'];

        $totalErrors = $errorsBamper + $errorsSol + $errorsShd;
        $totalProcessed = $processedBamper + $processedSol + $processedShd;

        return [
            'success' => $totalErrors === 0,
            'processed' => $totalProcessed,
            'total' => $totalBamper + $totalSol + $totalShd,
            'errors' => $totalErrors,
            'details' => [
                'bamper' => ['processed' => $processedBamper, 'total' => $totalBamper, 'errors' => $errorsBamper],
                'solicitantes' => ['processed' => $processedSol, 'total' => $totalSol, 'errors' => $errorsSol],
                'shd' => ['processed' => $processedShd, 'total' => $totalShd, 'errors' => $errorsShd],
            ],
        ];
    }

    public function syncCalificaSIG006(Project $project, string $username, string $dependencia, string $nombreusuario): array
    {
        $postulantes = ProjectHasPostulantes::where('project_id', $project->id)
            ->whereNull('deleted_at')
            ->with('getPostulante')
            ->get();

        $processed = 0;
        $errors = 0;
        $total = $postulantes->count();

        foreach ($postulantes as $php) {
            $titular = $php->getPostulante;
            if (!$titular || !$titular->califica) {
                continue;
            }

            try {
                $nexp = trim($titular->nexp);
                $sig005 = null;

                if ($nexp) {
                    $sig005 = SIG005::where('NroExp', $nexp)
                        ->where('TexCod', 118)
                        ->first();
                }

                if (!$sig005) {
                    $sig005 = SIG005::where('NroExpPer', $titular->cedula)
                        ->where('TexCod', 118)
                        ->orderBy('NroExp', 'desc')
                        ->first();
                }

                if (!$sig005) {
                    continue;
                }

                $nroExp = $sig005->NroExp;
                $detalles = SIG006::where('NroExp', $nroExp)
                    ->orderBy('DENroLin', 'desc')
                    ->get();

                $detalle = $detalles->first();
                $estadoActual = $detalle ? trim($detalle->DEExpEst) : null;

                $nuevoEstado = $titular->califica === 'N' ? 'N' : 'K';

                if ($nuevoEstado && $estadoActual !== $nuevoEstado) {
                    $nroLin = $detalle ? $detalle->DENroLin + 1 : 1;
                    $date = new \DateTime();
                    $deExpAcc = $titular->califica === 'N' ? ($titular->motivo ?? '') : '';

                    SIG006::create([
                        'NroExp' => $nroExp,
                        'NroExpS' => 'A',
                        'DENroLin' => $nroLin,
                        'DEExpEst' => $nuevoEstado,
                        'DEFecDis' => date_format($date, 'Ymd H:i:s'),
                        'UsuRcp' => $username,
                        'DEUnOrHa' => $dependencia,
                        'DEUnOrDe' => $dependencia,
                        'DERcpChk' => 1,
                        'DERcpNam' => $nombreusuario,
                        'DEExpAcc' => $deExpAcc,
                    ]);

                    Log::info("SIG006 sincronizado para {$titular->cedula}: estado {$nuevoEstado}");
                }

                $processed++;
            } catch (\Exception $e) {
                $errors++;
                Log::error("Error al sincronizar SIG006 para {$titular->cedula}: {$e->getMessage()}");
            }
        }

        return compact('processed', 'errors', 'total');
    }

    private function updateMainRecord(POSSVS $reg, Project $project): void
    {
        POSSVS::where('PsvCod', $reg->PsvCod)->update([
            'PsvModDes' => trim($project->name),
            'NucCod' => trim($project->sat_id),
            'PsvDptoId' => $project->state_id,
            'PsvCiudId' => $project->city_id,
        ]);
    }

    // ======================== STEP 1: BAMPER (Personas) ========================

    private function migratePersonas(Project $project, string $perUser): array
    {
        $postulantes = ProjectHasPostulantes::where('project_id', $project->id)
            ->whereNull('deleted_at')
            ->with(['getPostulante', 'getMembers.getPostulante'])
            ->get();

        $processed = 0;
        $errors = 0;
        $total = 0;

        $estciv = [
            'SO' => 1, 'CA' => 2, 'SE' => 3, 'DI' => 4, 'VI' => 6, 'ME' => 7,
        ];
        $relpar = [
            'SO' => 1, 'CA' => 2, 'SE' => 3, 'DI' => 4, 'VI' => 6, 'ME' => 7,
        ];

        $date = new \DateTime();

        foreach ($postulantes as $php) {
            // Process main postulant
            $titular = $php->getPostulante;
            if ($titular) {
                $total++;
                if ($this->processPersona($titular, $estciv, $relpar, $perUser, $date)) {
                    $processed++;
                } else {
                    $errors++;
                }
            }

            // Process family members
            if ($php->getMembers && $php->getMembers->count() > 0) {
                foreach ($php->getMembers as $member) {
                    $persona = $member->getPostulante;
                    if ($persona) {
                        $total++;
                        if ($this->processPersona($persona, $estciv, $relpar, $perUser, $date)) {
                            $processed++;
                        } else {
                            $errors++;
                        }
                    }
                }
            }
        }

        return compact('processed', 'errors', 'total');
    }

    private function processPersona(Postulante $persona, array $estciv, array $relpar, string $perUser, \DateTime $date): bool
    {
        try {
            $maritalStatus = $persona->marital_status;

            // Default to 'SO' (Single) for null/empty/unrecognized marital status (children, etc.)
            if (!$maritalStatus || !isset($estciv[$maritalStatus])) {
                $maritalStatus = 'SO';
            }

            $exists = BAMPER::where('PerCod', $persona->cedula)->first();
            $data = $this->prepareBamperData($persona, $estciv, $relpar, $perUser, $date, $maritalStatus);

            if ($exists) {
                BAMPER::where('PerCod', $persona->cedula)->update($data);
                Log::info("BAMPER actualizado: {$persona->cedula}");
            } else {
                BAMPER::create($data);
                Log::info("BAMPER insertado: {$persona->cedula}");
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Error al procesar BAMPER para {$persona->cedula}: {$e->getMessage()}");
            return false;
        }
    }

    private function prepareBamperData(Postulante $persona, array $estciv, array $relpar, string $perUser, \DateTime $date, string $maritalStatus): array
    {
        $nombre = $persona->last_name . ' ' . $persona->first_name;
        $nac = new \DateTime($persona->birthdate);
        $f = date_format($nac, 'Ymd');

        $nomseg = strpos($persona->first_name, ' ') !== false
            ? substr($persona->first_name, strpos($persona->first_name, " ") + 1)
            : '';
        $apeseg = strpos($persona->last_name, ' ') !== false
            ? substr($persona->last_name, strpos($persona->last_name, " ") + 1)
            : '';

        $apepri = strtok($persona->last_name, ' ');
        $nompri = strtok($persona->first_name, ' ');

        return [
            'PerCod' => $persona->cedula,
            'PerNom' => $nombre,
            'PerApePri' => $apepri,
            'PerNomPri' => $nompri,
            'PerApeSeg' => $apeseg,
            'PerNomSeg' => $nomseg,
            'PerDomic' => substr($persona->address ?? '', 0, 60),
            'PerTel1' => $persona->phone,
            'PerTel2' => $persona->mobile,
            'PerEstCiv' => $estciv[$maritalStatus],
            'PerTpDoc' => 'CID',
            'PerFchNac' => $f,
            'PerSexo' => $persona->gender,
            'ProCod' => 58,
            'ActCod' => 7,
            'PerNac' => 1,
            'DptoId' => 11,
            'CiuId' => 179,
            'PerRelPar' => $relpar[$maritalStatus],
            'PerFUM' => date_format($date, 'Ymd H:i:s'),
            'PerUser' => $perUser,
        ];
    }

    // ======================== STEP 2: IVMSOL / IVMSOL2 (Solicitantes) ========================

    private function migrateSolicitantes(Project $project, string $expedienteNumber, string $perUser): array
    {
        $exp = ProjectHasExpediente::where('project_id', $project->id)->first();
        if (!$exp) {
            return ['processed' => 0, 'errors' => 0, 'total' => 0];
        }

        $postulantes = ProjectHasPostulantes::where('project_id', $project->id)
            ->whereNull('deleted_at')
            ->with(['getPostulante.discapacidad', 'getMembers.getPostulante', 'getMembers.getParentesco'])
            ->get();

        $processed = 0;
        $errors = 0;
        $total = $postulantes->count();

        $parent = [
            1 => 1,   // Esposo/a
            2 => 3,   // Hermano/a
            3 => 2,   // Hijo/a
            4 => 4,   // Padre/Madre
            7 => 9,   // Sobrino/a
            8 => 1,   // Concubino/a
            9 => 5,   // Abuelo/a
            10 => 6,  // Tío/a
            11 => 5,  // Nieto/a
            14 => 10, // Yerno/Nuera
        ];

        $date = new \DateTime();

        foreach ($postulantes as $postulante) {
            try {
                $solPerCod = $postulante->getPostulante->cedula;

                $mesa = SIG005L1::where('ExpDPerCod', $solPerCod)
                    ->where('NroExp', $expedienteNumber)
                    ->first();

                if (!$mesa) {
                    Log::warning("Mesa no encontrada para {$solPerCod} - Exp: {$expedienteNumber}");
                    continue;
                }

                // Process IVMSOL (solicitante)
                $this->processIVMSOL($postulante, $mesa, $exp, $perUser, $date);

                // Collect current member cedulas for cleanup
                $currentMemberCedulas = collect();

                // Process IVMSOL2 for main postulant (ParCod = 8)
                $this->processGroupFamily($postulante->getPostulante, $solPerCod, $mesa, 8, $perUser, $date);
                $currentMemberCedulas->push($solPerCod);

                // Process IVMSOL2 for each family member
                if ($postulante->getMembers && $postulante->getMembers->count() > 0) {
                    foreach ($postulante->getMembers as $member) {
                        $miembro = $member->getPostulante;
                        if (!$miembro) {
                            continue;
                        }
                        $parentescoId = $member->parentesco_id;
                        $parentCod = $parent[$parentescoId] ?? 1;
                        $this->processGroupFamily($miembro, $solPerCod, $mesa, $parentCod, $perUser, $date);
                        $currentMemberCedulas->push($miembro->cedula);
                    }
                }

                // Delete IVMSOL2 records for members no longer in the family group
                $deleted = IVMSOL2::where('SolPerCod', $solPerCod)
                    ->whereNotIn('GfsCod', $currentMemberCedulas->toArray())
                    ->delete();
                if ($deleted > 0) {
                    Log::info("IVMSOL2 limpieza: {$deleted} registro(s) eliminado(s) para solicitante {$solPerCod}");
                }

                $processed++;
            } catch (\Exception $e) {
                $errors++;
                Log::error("Error al procesar solicitante {$postulante->getPostulante->cedula}: {$e->getMessage()}");
            }
        }

        return compact('processed', 'errors', 'total');
    }

    private function processIVMSOL($postulante, $mesa, $exp, string $perUser, \DateTime $date): void
    {
        $expfec = new \DateTime($mesa->ExpDFec);

        // Get spouse cedula from members (parentesco 1 or 8)
        $conyuge = $postulante->getMembers
            ? $postulante->getMembers->firstWhere('parentesco_id', 1)
            ?? $postulante->getMembers->firstWhere('parentesco_id', 8)
            : null;
        $solpercge = $conyuge && $conyuge->getPostulante ? $conyuge->getPostulante->cedula : '';

        $data = [
            'SolSer' => substr($mesa->ExpDNro, -2),
            'SolNro' => substr($mesa->ExpDNro, 0, -2),
            'SolFch' => date_format($expfec, 'Ymd H:i:s'),
            'SolTieUni' => '',
            'SolAuto' => 'N',
            'SolEquipo' => 'N',
            'SolMaquin' => 'N',
            'SolAnimal' => 'N',
            'SolOtros' => '',
            'SolTipo' => 12,
            'SolInscri' => $perUser,
            'SolComent' => "Exp. Social: " . $exp->exp . " Codigo de Proyecto: " . $exp->project_id,
            'SolPerCge' => $solpercge,
            'SolHabViv' => '',
            'SolFum' => date_format($date, 'Ymd H:i:s'),
            'SolEtapa' => 'S',
            'SolReFecAd' => null,
            'SolReNroAd' => null,
            'SolCodObra' => null,
        ];

        $exists = IVMSOL::where('SolPerCod', $postulante->getPostulante->cedula)->first();

        if ($exists) {
            IVMSOL::where('SolPerCod', $postulante->getPostulante->cedula)->update($data);
            Log::info("IVMSOL actualizado: {$postulante->getPostulante->cedula}");
        } else {
            $data['SolPerCod'] = $postulante->getPostulante->cedula;
            IVMSOL::create($data);
            Log::info("IVMSOL insertado: {$postulante->getPostulante->cedula}");
        }
    }

    private function processGroupFamily($persona, string $solPerCod, $mesa, int $parentCod, string $perUser, \DateTime $date): void
    {
        if (!$persona) {
            return;
        }

        // Check that the person exists in BAMPER
        $personaBamper = BAMPER::where('PerCod', $persona->cedula)->first();
        if (!$personaBamper) {
            Log::warning("Persona no encontrada en BAMPER: {$persona->cedula}");
            return;
        }

        $datecalc = new \DateTime($personaBamper->PerFchNac);
        $now = new \DateTime($mesa->ExpDFec);
        $interval = $now->diff($datecalc);

        $dis = $this->determinarDiscapacidad($persona);
        $montoProcesado = $this->procesarMonto($persona->ingreso);

        $data = [
            'GfsEdad' => $interval->y,
            'ParCod' => $parentCod,
            'GfsDis' => $dis,
            'GfsImpSue' => $montoProcesado,
            'GfsImpApo' => 0.00,
            'GfsUsuCod' => $perUser,
            'GfsFecAlta' => date_format($date, 'Ymd H:i:s'),
            'GfsPEC' => 'N',
        ];

        $exists = IVMSOL2::where('SolPerCod', $solPerCod)
            ->where('GfsCod', $persona->cedula)
            ->first();

        if ($exists) {
            IVMSOL2::where('SolPerCod', $solPerCod)
                ->where('GfsCod', $persona->cedula)
                ->update($data);
            Log::info("IVMSOL2 actualizado: {$persona->cedula} para solicitante: {$solPerCod}");
        } else {
            $data['SolPerCod'] = $solPerCod;
            $data['GfsCod'] = $persona->cedula;
            IVMSOL2::create($data);
            Log::info("IVMSOL2 insertado: {$persona->cedula} para solicitante: {$solPerCod}");
        }
    }

    private function determinarDiscapacidad($persona): string
    {
        if (!isset($persona->discapacidad) ||
            empty($persona->discapacidad->discapacidad_id)) {
            return 'N';
        }
        return $persona->discapacidad->discapacidad_id == 1 ? 'N' : 'S';
    }

    private function procesarMonto($monto): float
    {
        if (is_null($monto) || $monto === '' || $monto === 0 || $monto === '0,00' || $monto === '0.00') {
            return 0.00;
        }

        if (is_string($monto)) {
            if (preg_match('/^[\d.]+,\d{2}$/', $monto)) {
                $monto = str_replace('.', '', $monto);
                $monto = str_replace(',', '.', $monto);
            } elseif (strpos($monto, ',') !== false) {
                $monto = str_replace(',', '.', $monto);
            }
        }

        return (float) number_format((float) $monto, 2, '.', '');
    }

    // ======================== STEP 3: POSSVS / POSSVS1 (SHD) ========================

    public function refreshPostgresData(Project $project, Land $tipoterreno): array
    {
        $postulantes = ProjectHasPostulantes::with([
            'getPostulante.discapacidad',
            'getMembers.getPostulante',
        ])
            ->where('project_id', $project->id)
            ->whereNull('deleted_at')
            ->get();

        $processed = 0;
        $errors = 0;
        $total = $postulantes->count();
        $expedienteNumber = $this->getExpedienteNumberForProject($project->id);

        foreach ($postulantes as $postulante) {
            try {
                if ($this->processIndividualPostulantePostgresOnly($postulante, $tipoterreno, $expedienteNumber)) {
                    $processed++;
                }
            } catch (\Exception $e) {
                $errors++;
                $titular = $postulante->getPostulante;
                Log::error("Error al actualizar Postgres para postulante {$postulante->id}: {$e->getMessage()}", [
                    'postulante_id' => $postulante->id,
                    'cedula' => $titular->cedula ?? 'N/A',
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        return compact('processed', 'errors', 'total');
    }

    private function getExpedienteNumberForProject(int $projectId): ?string
    {
        $expediente = ProjectHasExpediente::where('project_id', $projectId)->first();

        return $expediente ? $expediente->exp : null;
    }

    private function processIndividualPostulantePostgresOnly($postulante, Land $tipoterreno, ?string $expedienteNumber): bool
    {
        $titular = $postulante->getPostulante;
        if (!$titular) {
            Log::warning("No se encontró el titular para el registro ProjectHasPostulantes ID {$postulante->id}");
            return false;
        }

        // Look up individual expedient number from SIG005L1
        $individualExp = null;
        $mesa = SIG005L1::where('ExpDPerCod', $titular->cedula)
            ->where('NroExp', $expedienteNumber)
            ->first();
        if ($mesa) {
            $individualExp = $mesa->ExpDNro;
        }

        $conyugeData = $this->prepareConyugeData($postulante);
        $postulanteData = $this->preparePostgresDataOnly($postulante, $conyugeData, $individualExp ?? $expedienteNumber);

        $pgRecord = $titular;
        $pgRecord->ingreso = $postulanteData['ingreso'];
        $pgRecord->ingreso_familiar = $postulanteData['ingreso_familiar'];
        $pgRecord->hijo_sosten = $postulanteData['hijo_sosten'];
        $pgRecord->discapacidad = $postulanteData['discapacidad'];
        $pgRecord->tercera_edad = $postulanteData['tercera_edad'];
        $pgRecord->cantidad_hijos = $postulanteData['cantidad_hijos'];
        $pgRecord->nexp = $postulanteData['nexp'];
        $pgRecord->otra_persona_a_cargo = $postulanteData['otra_persona_a_cargo'];
        $pgRecord->nivel = $postulanteData['nivel'];
        $pgRecord->composicion_del_grupo = $postulanteData['composicion_del_grupo'];
        $pgRecord->observacion_de_consideracion = $postulanteData['observacion_de_consideracion'];
        $pgRecord->save();

        Log::info("Actualizado en Postgres postulante ID {$pgRecord->id} - Cedula {$pgRecord->cedula}");

        return true;
    }

    private function preparePostgresDataOnly($postulante, array $conyugeData, ?string $expedienteNumber): array
    {
        $persona = $postulante->getPostulante;
        $discapacidadId = optional($persona->discapacidad)->discapacidad_id ?? 1;
        $tieneDiscapacidad = $discapacidadId == 1 ? 'N' : 'S';
        $ingresoTitular = $persona->ingreso ?? 0;
        $ingresoConyuge = $conyugeData['ingreso'];

        return [
            'ingreso' => $ingresoTitular,
            'ingreso_familiar' => $ingresoTitular + $ingresoConyuge,
            'hijo_sosten' => $persona->hijo_sosten,
            'discapacidad' => $tieneDiscapacidad,
            'tercera_edad' => 'N',
            'cantidad_hijos' => $postulante->childrens_count ?? 0,
            'nexp' => $expedienteNumber ?? $persona->nexp,
            'otra_persona_a_cargo' => 'N',
            'nivel' => ProjectHasPostulantes::getNivel($persona->id),
            'composicion_del_grupo' => $persona->composicion_del_grupo ?? $persona->composicion_del_grupo,
            'observacion_de_consideracion' => $persona->hijo_sosten,
        ];
    }

    private function processPostulantes(int $projectId, string $planilla, ?string $expedienteNumber, Land $tipoterreno, string $perUser): array
    {
        $postulantes = ProjectHasPostulantes::with([
            'getPostulante.discapacidad',
            'getMembers.getPostulante',
        ])
            ->where('project_id', $projectId)
            ->whereNull('deleted_at')
            ->get();

        $processed = 0;
        $errors = 0;
        $total = $postulantes->count();

        foreach ($postulantes as $key => $postulante) {
            try {
                if ($this->processIndividualPostulante($postulante, $key, $planilla, $expedienteNumber, $tipoterreno, $perUser)) {
                    $processed++;
                }
            } catch (\Exception $e) {
                $errors++;
                $titular = $postulante->getPostulante;
                Log::error("Error al procesar postulante {$postulante->id}: {$e->getMessage()}", [
                    'postulante_id' => $postulante->id,
                    'cedula' => $titular->cedula ?? 'N/A',
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        return compact('processed', 'errors', 'total');
    }

    private function processIndividualPostulante($postulante, int $key, string $planilla, ?string $expedienteNumber, Land $tipoterreno, string $perUser): bool
    {
        Log::info("Procesando postulante ID {$postulante->id}");

        $titular = $postulante->getPostulante;
        if (!$titular) {
            Log::warning("No se encontró el titular para el registro ProjectHasPostulantes ID {$postulante->id}");
            return false;
        }

        $mesa = SIG005L1::where('ExpDPerCod', $titular->cedula)
            ->where('NroExp', $expedienteNumber)
            ->first();

        if (!$mesa) {
            Log::warning("No se encontró 'mesa' para {$titular->cedula}");
            return false;
        }

        $conyugeData = $this->prepareConyugeData($postulante);
        $postulanteData = $this->preparePostulanteData(
            $postulante,
            $key,
            $planilla,
            $mesa,
            $conyugeData,
            $tipoterreno,
            $perUser
        );

        $existingUser = POSSVS1::where('PsvCedTit', $titular->cedula)
            ->where('PsvCod', $planilla)
            ->first();

        if ($existingUser) {
            Log::info("Postulante ya existe en SQL: {$titular->cedula} - Actualizando...");
            POSSVS1::where('PsvCedTit', $titular->cedula)
                ->where('PsvCod', $planilla)
                ->update($postulanteData);
            Log::info("Actualizado exitosamente en SQL: {$titular->cedula}");
        } else {
            Log::info("Insertando nuevo postulante en SQL: {$titular->cedula}");
            POSSVS1::create($postulanteData);
            Log::info("Insertado exitosamente en SQL: {$titular->cedula}");
        }

        $pgRecord = $titular;
        $pgRecord->ingreso = $postulanteData['PsvIngTit'];
        $pgRecord->ingreso_familiar = $postulanteData['PsvIngFam'];
        $pgRecord->hijo_sosten = $postulanteData['PsvObsSost'] ?? $postulanteData['PsvObs'] ?? null;
        $pgRecord->discapacidad = $postulanteData['PsvDiscap'];
        $pgRecord->tercera_edad = $postulanteData['PsvTerEdad'];
        $pgRecord->cantidad_hijos = $postulanteData['PsvCanHij'];
        $pgRecord->nexp = $postulanteData['PsvExpNro'];
        $pgRecord->otra_persona_a_cargo = 'N';
        $pgRecord->nivel = $postulanteData['PsvNivel'];
        $pgRecord->composicion_del_grupo = $postulanteData['PsvObs2'] ?? $pgRecord->composicion_del_grupo;
        $pgRecord->observacion_de_consideracion = $postulanteData['PsvObs'];
        $pgRecord->save();

        Log::info("Actualizado en Postgres postulante ID {$pgRecord->id} - Cedula {$pgRecord->cedula}");

        return true;
    }

    private function prepareConyugeData($projectHasPostulante): array
    {
        $conyuge = $projectHasPostulante->getMembers
            ? $projectHasPostulante->getMembers->firstWhere('parentesco_id', 1)
            ?? $projectHasPostulante->getMembers->firstWhere('parentesco_id', 8)
            : null;

        if (!$conyuge || !$conyuge->getPostulante) {
            return [
                'cedula' => '',
                'nombre' => '',
                'ingreso' => 0,
                'fecha_nacimiento' => null,
            ];
        }

        $miembro = $conyuge->getPostulante;

        return [
            'cedula' => $miembro->cedula ?? '',
            'nombre' => trim(($miembro->last_name ?? '') . ', ' . ($miembro->first_name ?? '')),
            'ingreso' => $miembro->ingreso ?? 0,
            'fecha_nacimiento' => $miembro->birthdate ? date_format(new \DateTime($miembro->birthdate), 'Ymd') : null,
        ];
    }

    private function preparePostulanteData($postulante, int $key, string $planilla, $mesa, $conyugeData, Land $tipoterreno, string $perUser): array
    {
        $persona = $postulante->getPostulante;
        $date = new \DateTime();

        $discapacidadId = optional($persona->discapacidad)->discapacidad_id ?? 1;
        $tieneDiscapacidad = $discapacidadId == 1 ? 'N' : 'S';

        $fechaNacimiento = $persona->birthdate ? date_format(new \DateTime($persona->birthdate), 'Ymd') : null;
        $direccion = substr($persona->address ?? '', 0, 60);
        $nombreCompleto = trim($persona->last_name . ', ' . $persona->first_name);
        $ingresoTitular = $persona->ingreso ?? 0;
        $ingresoConyuge = $conyugeData['ingreso'];
        $ingresoFamiliar = $ingresoTitular + $ingresoConyuge;
        $nivel = ProjectHasPostulantes::getNivel($persona->id);

        return [
            'PsvCod' => $planilla,
            'Psvord' => $key + 1,
            'PsvBibNro' => 0,
            'PsvExpNro' => $mesa->ExpDNro,
            'PsvExpS' => 'A',
            'PsvTDPos' => 'C',
            'PsvTDPosM' => '',
            'PsvCedTit' => $persona->cedula,
            'PsvNomTit' => $nombreCompleto,
            'PsvTDCge' => 'C',
            'PsvTDCgeM' => '',
            'PsvCedCge' => $conyugeData['cedula'],
            'PsvNomCge' => $conyugeData['nombre'],
            'PsvNivel' => $nivel,
            'PsvCanHij' => $postulante->childrens_count ?? 0,
            'PsvDiscap' => $tieneDiscapacidad,
            'PsvTerEdad' => 'N',
            'PsvSosten' => !empty($persona->hijo_sosten) ? 'S' : 'N',
            'PsvAporte' => 0,
            'PsvIfac' => '',
            'PsvDomi' => trim($direccion),
            'PsvObs' => $persona->hijo_sosten,
            'PsvRegCon' => 'S',
            'PsvUsuIng' => $perUser,
            'PsvFecIng' => date_format($date, 'Ymd H:i:s'),
            'PsvIngTit' => $ingresoTitular,
            'PsvIngCge' => $ingresoConyuge,
            'PsvIngOtr' => 0,
            'PsvIngFam' => $ingresoFamiliar,
            'PsvNomSos' => '',
            'PsvCgeFNac' => $conyugeData['fecha_nacimiento'],
            'PsvTitFNac' => $fechaNacimiento,
            'PsvTerreno' => trim($tipoterreno->name),
            'PsvObs2' => $persona->composicion_del_grupo ?? null,
            'PsvObsSost' => $persona->hijo_sosten,
        ];
    }
}
