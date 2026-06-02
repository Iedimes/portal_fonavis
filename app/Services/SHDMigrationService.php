<?php

namespace App\Services;

use App\Models\Land;
use App\Models\POSSVS;
use App\Models\POSSVS1;
use App\Models\Project;
use App\Models\ProjectHasPostulantes;
use App\Models\SIG005L1;
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

        $this->updateMainRecord($reg, $project);

        $result = $this->processPostulantes($project->id, trim($planilla), $expedienteNumber, $tipoterreno, $perUser);

        return [
            'success' => $result['errors'] === 0,
            'processed' => $result['processed'],
            'total' => $result['total'],
            'errors' => $result['errors'],
            'message' => $result['errors'] === 0
                ? 'Migración completada correctamente.'
                : 'Migración completada con errores. Revisa los logs.',
        ];
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
