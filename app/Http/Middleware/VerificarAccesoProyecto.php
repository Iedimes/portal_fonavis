<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;

class VerificarAccesoProyecto
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Usuarios sin restricción para ver todos los proyectos
        $usuariosSinRestriccion = [
            367,
            368,
        ];

        // Si el usuario está liberado, pasa sin restricción
        if (in_array($user->id, $usuariosSinRestriccion)) {
            return $next($request);
        }

        // Obtener ID del proyecto de la ruta
        $projectId = $request->route('id') ?? $request->route('project') ?? $request->route('project_id');

        if (!$projectId) {
            return abort(400, 'Proyecto no especificado.');
        }

        $project = Project::find($projectId);

        if (!$project) {
            return abort(404, 'Proyecto no encontrado.');
        }

        // Validación de acceso por SAT
        if (trim($project->sat_id) !== trim($user->sat_ruc)) {
            return abort(403, 'No tienes acceso a este proyecto.');
        }

        return $next($request);
    }
}
