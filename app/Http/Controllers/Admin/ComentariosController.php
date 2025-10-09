<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Comentario\BulkDestroyComentario;
use App\Http\Requests\Admin\Comentario\DestroyComentario;
use App\Http\Requests\Admin\Comentario\IndexComentario;
use App\Http\Requests\Admin\Comentario\StoreComentario;
use App\Http\Requests\Admin\Comentario\UpdateComentario;
use App\Models\Comentario;
use App\Models\Postulante;
use App\Models\ProjectHasPostulantes;
use App\Models\PostulanteHasBeneficiary;
use Brackets\AdminListing\Facades\AdminListing;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ComentariosController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexComentario $request
     * @return array|Factory|View
     */
    public function index(IndexComentario $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Comentario::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'postulante_id', 'cedula'],

            // set columns to searchIn
            ['id', 'cedula', 'comentario']
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.comentario.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create($postulante_id, $cedula)
{
    $postulante = Postulante::where('id', $postulante_id)->first();
    return view('admin.comentario.create', compact('postulante_id', 'cedula', 'postulante'));
}

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreComentario $request
     * @return array|RedirectResponse|Redirector
     */

    public function store(StoreComentario $request)
    {
        $sanitized = $request->getSanitized();
        $comentario = Comentario::create($sanitized);

        $postulanteId = $request->input('postulante_id');

        if ($postulanteId) {
            DB::transaction(function () use ($postulanteId) {

                $postulante = Postulante::find($postulanteId);

                if ($postulante) {

                    // Buscar miembros (beneficiarios) vinculados a este postulante
                    $miembros = PostulanteHasBeneficiary::where('postulante_id', $postulanteId)->get();

                    foreach ($miembros as $miembro) {
                        // Buscar el postulante correspondiente al miembro
                        $beneficiario = Postulante::find($miembro->miembro_id);
                        if ($beneficiario && !$beneficiario->deleted_at) {
                            $beneficiario->delete(); // Soft delete del miembro
                        }

                        // Soft delete de la relación
                        if (!$miembro->deleted_at) {
                            $miembro->delete();
                        }
                    }

                    // Eliminar relación con el proyecto (soft delete)
                    ProjectHasPostulantes::where('postulante_id', $postulanteId)->delete();

                    // Soft delete del postulante principal
                    $postulante->delete();
                }
            });
        }

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/postulantes'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded')
            ];
        }

        return redirect('admin/postulantes');
    }


    /**
     * Display the specified resource.
     *
     * @param Comentario $comentario
     * @throws AuthorizationException
     * @return void
     */
    public function show(Comentario $comentario)
    {
        $this->authorize('admin.comentario.show', $comentario);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Comentario $comentario
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Comentario $comentario)
    {
        $this->authorize('admin.comentario.edit', $comentario);


        return view('admin.comentario.edit', [
            'comentario' => $comentario,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateComentario $request
     * @param Comentario $comentario
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateComentario $request, Comentario $comentario)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Comentario
        $comentario->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/comentarios'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/comentarios');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyComentario $request
     * @param Comentario $comentario
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyComentario $request, Comentario $comentario)
    {
        $comentario->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyComentario $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyComentario $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Comentario::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
