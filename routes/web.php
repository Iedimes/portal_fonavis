<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PostulantesController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('admin-users')->name('admin-users/')->group(static function () {
            Route::get('/',                                             'AdminUsersController@index')->name('index');
            Route::get('/create',                                       'AdminUsersController@create')->name('create');
            Route::post('/',                                            'AdminUsersController@store')->name('store');
            Route::get('/{adminUser}/impersonal-login',                 'AdminUsersController@impersonalLogin')->name('impersonal-login');
            Route::get('/{adminUser}/edit',                             'AdminUsersController@edit')->name('edit');
            Route::post('/{adminUser}',                                 'AdminUsersController@update')->name('update');
            Route::delete('/{adminUser}',                               'AdminUsersController@destroy')->name('destroy');
            Route::get('/{adminUser}/resend-activation',                'AdminUsersController@resendActivationEmail')->name('resendActivationEmail');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::get('/profile',                                      'ProfileController@editProfile')->name('edit-profile');
        Route::post('/profile',                                     'ProfileController@updateProfile')->name('update-profile');
        Route::get('/password',                                     'ProfileController@editPassword')->name('edit-password');
        Route::post('/password',                                    'ProfileController@updatePassword')->name('update-password');
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('modalities')->name('modalities/')->group(static function () {
            Route::get('/',                                             'ModalitiesController@index')->name('index');
            Route::get('/create',                                       'ModalitiesController@create')->name('create');
            Route::post('/',                                            'ModalitiesController@store')->name('store');
            Route::get('/{modality}/edit',                              'ModalitiesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'ModalitiesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{modality}',                                  'ModalitiesController@update')->name('update');
            Route::delete('/{modality}',                                'ModalitiesController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('lands')->name('lands/')->group(static function () {
            Route::get('/',                                             'LandsController@index')->name('index');
            Route::get('/create',                                       'LandsController@create')->name('create');
            Route::post('/',                                            'LandsController@store')->name('store');
            Route::get('/{land}/edit',                                  'LandsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'LandsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{land}',                                      'LandsController@update')->name('update');
            Route::delete('/{land}',                                    'LandsController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('documents')->name('documents/')->group(static function () {
            Route::get('/',                                             'DocumentsController@index')->name('index');
            Route::get('/create',                                       'DocumentsController@create')->name('create');
            Route::post('/',                                            'DocumentsController@store')->name('store');
            Route::get('/{document}/edit',                              'DocumentsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'DocumentsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{document}',                                  'DocumentsController@update')->name('update');
            Route::delete('/{document}',                                'DocumentsController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('categories')->name('categories/')->group(static function () {
            Route::get('/',                                             'CategoriesController@index')->name('index');
            Route::get('/create',                                       'CategoriesController@create')->name('create');
            Route::post('/',                                            'CategoriesController@store')->name('store');
            Route::get('/{category}/edit',                              'CategoriesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'CategoriesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{category}',                                  'CategoriesController@update')->name('update');
            Route::delete('/{category}',                                'CategoriesController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('project-types')->name('project-types/')->group(static function () {
            Route::get('/',                                             'ProjectTypeController@index')->name('index');
            Route::get('/create',                                       'ProjectTypeController@create')->name('create');
            Route::post('/',                                            'ProjectTypeController@store')->name('store');
            Route::get('/{projectType}/edit',                           'ProjectTypeController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'ProjectTypeController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{projectType}',                               'ProjectTypeController@update')->name('update');
            Route::delete('/{projectType}',                             'ProjectTypeController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('stages')->name('stages/')->group(static function () {
            Route::get('/',                                             'StagesController@index')->name('index');
            Route::get('/create',                                       'StagesController@create')->name('create');
            Route::post('/',                                            'StagesController@store')->name('store');
            Route::get('/{stage}/edit',                                 'StagesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'StagesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{stage}',                                     'StagesController@update')->name('update');
            Route::delete('/{stage}',                                   'StagesController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('typologies')->name('typologies/')->group(static function () {
            Route::get('/',                                             'TypologiesController@index')->name('index');
            Route::get('/create',                                       'TypologiesController@create')->name('create');
            Route::post('/',                                            'TypologiesController@store')->name('store');
            Route::get('/{typology}/edit',                              'TypologiesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'TypologiesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{typology}',                                  'TypologiesController@update')->name('update');
            Route::delete('/{typology}',                                'TypologiesController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('parentescos')->name('parentescos/')->group(static function () {
            Route::get('/',                                             'ParentescoController@index')->name('index');
            Route::get('/create',                                       'ParentescoController@create')->name('create');
            Route::post('/',                                            'ParentescoController@store')->name('store');
            Route::get('/{parentesco}/edit',                            'ParentescoController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'ParentescoController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{parentesco}',                                'ParentescoController@update')->name('update');
            Route::delete('/{parentesco}',                              'ParentescoController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('discapacidads')->name('discapacidads/')->group(static function () {
            Route::get('/',                                             'DiscapacidadController@index')->name('index');
            Route::get('/create',                                       'DiscapacidadController@create')->name('create');
            Route::post('/',                                            'DiscapacidadController@store')->name('store');
            Route::get('/{discapacidad}/edit',                          'DiscapacidadController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'DiscapacidadController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{discapacidad}',                              'DiscapacidadController@update')->name('update');
            Route::delete('/{discapacidad}',                            'DiscapacidadController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('modality-has-lands')->name('modality-has-lands/')->group(static function () {
            Route::get('/',                                             'ModalityHasLandsController@index')->name('index');
            Route::get('/create',                                       'ModalityHasLandsController@create')->name('create');
            Route::post('/',                                            'ModalityHasLandsController@store')->name('store');
            Route::get('/{modalityHasLand}/edit',                       'ModalityHasLandsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'ModalityHasLandsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{modalityHasLand}',                           'ModalityHasLandsController@update')->name('update');
            Route::delete('/{modalityHasLand}',                         'ModalityHasLandsController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('land-has-project-types')->name('land-has-project-types/')->group(static function () {
            Route::get('/',                                             'LandHasProjectTypeController@index')->name('index');
            Route::get('/create',                                       'LandHasProjectTypeController@create')->name('create');
            Route::post('/',                                            'LandHasProjectTypeController@store')->name('store');
            Route::get('/{landHasProjectType}/edit',                    'LandHasProjectTypeController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'LandHasProjectTypeController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{landHasProjectType}',                        'LandHasProjectTypeController@update')->name('update');
            Route::delete('/{landHasProjectType}',                      'LandHasProjectTypeController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('assignments')->name('assignments/')->group(static function () {
            Route::get('/',                                             'AssignmentsController@index')->name('index');
            Route::get('/create',                                       'AssignmentsController@create')->name('create');
            Route::post('/',                                            'AssignmentsController@store')->name('store');
            Route::get('/{assignment}/edit',                            'AssignmentsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'AssignmentsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{assignment}',                                'AssignmentsController@update')->name('update');
            Route::delete('/{assignment}',                              'AssignmentsController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('project-type-has-typologies')->name('project-type-has-typologies/')->group(static function () {
            Route::get('/',                                             'ProjectTypeHasTypologiesController@index')->name('index');
            Route::get('/create',                                       'ProjectTypeHasTypologiesController@create')->name('create');
            Route::post('/',                                            'ProjectTypeHasTypologiesController@store')->name('store');
            Route::get('/{projectTypeHasTypology}/edit',                'ProjectTypeHasTypologiesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'ProjectTypeHasTypologiesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{projectTypeHasTypology}',                    'ProjectTypeHasTypologiesController@update')->name('update');
            Route::delete('/{projectTypeHasTypology}',                  'ProjectTypeHasTypologiesController@destroy')->name('destroy');
        });
    });
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\ProjectController::class, 'index'])->name('home');

Route::get('/', [App\Http\Controllers\ProjectController::class, 'index']);

Route::get('download/{project}/{document_id}/{file_name}', [App\Http\Controllers\ProjectController::class, 'downloadFile'])->name('downloadFile');

Route::get('bajarDocumento/{project}/faltantes/{document_id}/{file_name}', [App\Http\Controllers\ProjectController::class, 'bajarDocumento'])->name('bajarDocumento');

// Descargar documentos del lado del ADM
Route::get('descargarDocumento/{project}/faltantes/{document_id}/{file_name}', 'App\Http\Controllers\Admin\ProjectsController@descargarDocumento')->name('descargarDocumento');
Route::get('downloadfileDoc/{project}/{document_id}/{file_name}', 'App\Http\Controllers\Admin\ProjectsController@downloadFile')->name('downloadFileDoc');


Route::resource('projects', ProjectController::class);
Route::get('projects/send/{id}', [App\Http\Controllers\ProjectController::class, 'send']);

Route::get('generate-pdf/{id}', [App\Http\Controllers\ProjectController::class, 'generatePDF'])->name('generate-pdf');

Route::get('projects/ajax/{state_id?}/cities', [App\Http\Controllers\ProjectController::class, 'distrito']);
Route::get('projects/ajax/{state_id?}/lands', [App\Http\Controllers\ProjectController::class, 'lands']);
Route::get('projects/ajax/{state_id?}/typology', [App\Http\Controllers\ProjectController::class, 'typology']);
Route::get('projects/ajax/{state_id?}/local', [App\Http\Controllers\ProjectController::class, 'distrito']);

//Postulantes
Route::get('projects/{id}/postulantes', [App\Http\Controllers\PostulantesController::class, 'index']);
Route::post('projects/{id}/postulantes/create', [App\Http\Controllers\PostulantesController::class, 'create']);
Route::post('projects/{id}/postulantes/{x}/createmiembro', [App\Http\Controllers\PostulantesController::class, 'createmiembro']);
Route::post('postulantes/destroy', [App\Http\Controllers\PostulantesController::class, 'destroy']);
Route::post('postulantes/destroymiembro', [App\Http\Controllers\PostulantesController::class, 'destroymiembro']);
Route::post('savepostulante', [App\Http\Controllers\PostulantesController::class, 'store']);
Route::post('savepostulanteEdit', [App\Http\Controllers\PostulantesController::class, 'storeEditPostulante']);
Route::post('savemiembro', [App\Http\Controllers\PostulantesController::class, 'storemiembro']);
Route::post('savemiembroeditar', [App\Http\Controllers\PostulantesController::class, 'updatemiembro']);
Route::get('imprimir/{id}', 'App\Http\Controllers\PostulantesController@generatePDF')->name('imprimir');


// Route::get('projects/{id}/postulantes/{idpostulante}', 'PostulantesController@show');
// Route::get('projects/{id}/postulantes/{idpostulante}', [App\Http\Controllers\PostulantesController::class,'show']);
Route::get('projects/{id}/postulantes/{idpostulante}', [App\Http\Controllers\PostulantesController::class, 'show'])->name('projects.postulantes.show');
Route::get('/postulantes/edit/{id}/{idpostulante}', [App\Http\Controllers\PostulantesController::class, 'editarPostulante'])->name('postulantes.edit');
Route::get('/miembros/edit/{id}/{idpostulante}', [App\Http\Controllers\PostulantesController::class, 'editmiembro'])->name('miembros.edit');
/*Route::get('projects/{id}/postulantes/{idpostulante}/edit', 'PostulantesController@edit');
Route::post('editpostulante', 'PostulantesController@update');
Route::post('postulantes/upload', 'PostulantesController@upload');
Route::post('postulantes/destroyfile', 'PostulantesController@destroyfile');

Route::post('postulantes/destroy', 'PostulantesController@destroy');
*/

// Route::post('postulantes/destroymiembro', 'PostulantesController@destroymiembro')->name('eliminar-miembro');

//Adjuntar documentos

// Route::post('upload', [App\Http\Controllers\ProjectController::class, 'upload']);
Route::post('levantar', [App\Http\Controllers\ProjectController::class, 'upload']);
Route::post('levantarDocumento', [App\Http\Controllers\ProjectController::class, 'uploadDocumento']);
Route::post('levantarTecnico', [App\Http\Controllers\ProjectController::class, 'uploadTecnico']);
Route::post('levantarNoExcluyente', [App\Http\Controllers\ProjectController::class, 'uploadNoExcluyente']);
Route::get('projectsDoc/{id}', [App\Http\Controllers\ProjectController::class, 'showDoc']);
Route::get('projectsMiembros/{id}', [App\Http\Controllers\ProjectController::class, 'showProyMiembros']);
Route::get('projectsTecnico/{id}', [App\Http\Controllers\ProjectController::class, 'showTecnico']);
Route::get('projectsDocTec/{id}', [App\Http\Controllers\ProjectController::class, 'showDocTec']);
Route::get('projectsDocNoExcluyentes/{id}', [App\Http\Controllers\ProjectController::class, 'showDocNoExcluyentes']);




// Ver documento

// Route::get('/ver/{project}/{document}', [App\Http\Controllers\ProjectController::class, 'ver'])->name('ver');

//Eliminar documentos
// Route::delete('documents/eliminar/{project_id}/{document_id}', [App\Http\Controllers\ProjectController::class, 'eliminar'])->name('eliminar');
Route::get('documents/eliminar/{project_id}/{document_id}', [App\Http\Controllers\ProjectController::class, 'eliminar'])->name('eliminar');
Route::get('documents/eliminardocumento/{project_id}/{document_id}', [App\Http\Controllers\ProjectController::class, 'eliminarDocumento'])->name('eliminarDocumento');
Route::post('/enviar-documentos-faltantes', [App\Http\Controllers\ProjectController::class, 'enviarDocumentosFaltantes'])->name('enviarDocumentosFaltantes');

Route::get('/storage/{folder}/{filename}', function ($folder, $filename) {
    $path = storage_path("app/{$folder}/{$filename}");
    if (file_exists($path)) {
        return response()->file($path);
    } else {
        abort(404);
    }
})->where('filename', '(.*)');


//validacion qr
Route::get('/{key}', [App\Http\Controllers\HomeController::class, 'verification']);

//
Route::get('projects/ajax/{id}/checkdocuments/{project_id}/{sheets}', [App\Http\Controllers\ProjectController::class, 'checkdocuments']);


/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('users')->name('users/')->group(static function () {
            Route::get('/',                                             'UsersController@index')->name('index');
            Route::get('/create',                                       'UsersController@create')->name('create');
            Route::post('/',                                            'UsersController@store')->name('store');
            Route::get('/{user}/edit',                                  'UsersController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'UsersController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{user}',                                      'UsersController@update')->name('update');
            Route::delete('/{user}',                                    'UsersController@destroy')->name('destroy');
        });
    });
});


/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin')->group(static function () {
        Route::prefix('projects')->name('projects')->group(static function () {
            Route::get('/',                                             'ProjectsController@index')->name('index');
            Route::get('/create',                                       'ProjectsController@create')->name('create');
            Route::post('/',                                            'ProjectsController@store')->name('store');
            Route::get('/{project}/show',                               'ProjectsController@show');
            Route::get('/{project}/showDGJN',                           'ProjectsController@showDGJN')->name('DGJN');
            Route::get('/{project}/showDGJNFALTANTE',                   'ProjectsController@showDGJNFALTANTE')->name('DGJNFALTANTE');
            Route::get('/{project}/showFONAVIS',                        'ProjectsController@showFONAVIS')->name('FONAVIS');
            Route::get('/{project}/showFONAVISADJ',                     'ProjectsController@showFONAVISADJ')->name('FONAVISADJ');
            Route::get('/{project}/showFONAVISSOCIAL',                  'ProjectsController@showFONAVISSOCIAL')->name('FONAVISSOCIAL');
            Route::get('/{project}/showFONAVISTECNICO',                 'ProjectsController@showFONAVISTECNICO')->name('FONAVISTECNICO');
            Route::get('/{project}/showFONAVISTECNICODOS',              'ProjectsController@showFONAVISTECNICODOS')->name('FONAVISTECNICODOS');
            Route::get('/{project}/showDGSO',                           'ProjectsController@showDGSO')->name('DGSO');
            Route::get('/{project}/showDIGH',                           'ProjectsController@showDIGH')->name('DIGH');
            Route::get('/{project}/showDSGO',                           'ProjectsController@showDSGO')->name('DSGO');
            Route::get('/{project}/transition',                         'ProjectsController@transition')->name('transition');
            Route::get('/{project}/transitionEliminar',                 'ProjectsController@transitionEliminar')->name('transitionEliminar');
            Route::get('/{project}/edit',                               'ProjectsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'ProjectsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{project}',                                   'ProjectsController@update')->name('update');
            Route::delete('/{project}',                                 'ProjectsController@destroy')->name('destroy');
        });
    });
});


/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('document-checks')->name('document-checks/')->group(static function () {
            Route::get('/',                                             'DocumentChecksController@index')->name('index');
            Route::get('/create',                                       'DocumentChecksController@create')->name('create');
            Route::post('/',                                            'DocumentChecksController@store')->name('store');
            Route::get('/{documentCheck}/edit',                         'DocumentChecksController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'DocumentChecksController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{documentCheck}',                             'DocumentChecksController@update')->name('update');
            Route::delete('/{documentCheck}',                           'DocumentChecksController@destroy')->name('destroy');
        });
    });
});


/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('project-statuses')->name('project-statuses/')->group(static function () {
            Route::get('/',                                             'ProjectStatusController@index')->name('index');
            Route::get('/create',                                       'ProjectStatusController@create')->name('create');
            Route::post('/',                                            'ProjectStatusController@store')->name('store');
            Route::get('/{projectStatus}/edit',                         'ProjectStatusController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'ProjectStatusController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{projectStatus}',                             'ProjectStatusController@update')->name('update');
            Route::delete('/{projectStatus}',                           'ProjectStatusController@destroy')->name('destroy');
            Route::match(['get', 'post'], '/{projectStatus}/eliminar', 'ProjectStatusController@eliminar')->name('eliminar');
        });
    });
});


/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('dependencies')->name('dependencies/')->group(static function() {
            Route::get('/',                                             'DependenciesController@index')->name('index');
            Route::get('/create',                                       'DependenciesController@create')->name('create');
            Route::post('/',                                            'DependenciesController@store')->name('store');
            Route::get('/{dependency}/edit',                            'DependenciesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'DependenciesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{dependency}',                                'DependenciesController@update')->name('update');
            Route::delete('/{dependency}',                              'DependenciesController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('admin-users-dependencies')->name('admin-users-dependencies/')->group(static function() {
            Route::get('/',                                             'AdminUsersDependenciesController@index')->name('index');
            Route::get('/create',                                       'AdminUsersDependenciesController@create')->name('create');
            Route::post('/',                                            'AdminUsersDependenciesController@store')->name('store');
            Route::get('/{adminUsersDependency}/edit',                  'AdminUsersDependenciesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'AdminUsersDependenciesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{adminUsersDependency}',                      'AdminUsersDependenciesController@update')->name('update');
            Route::delete('/{adminUsersDependency}',                    'AdminUsersDependenciesController@destroy')->name('destroy');
        });
    });
});


/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('media')->name('media/')->group(static function() {
            Route::get('/',                                             'MediaController@index')->name('index');
            Route::get('/create',                                       'MediaController@create')->name('create');
            Route::post('/',                                            'MediaController@store')->name('store');
            Route::get('/{medium}/edit',                                'MediaController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'MediaController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{medium}',                                    'MediaController@update')->name('update');
            Route::delete('/{medium}',                                  'MediaController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('admin-users-dependencies')->name('admin-users-dependencies/')->group(static function() {
            Route::get('/',                                             'AdminUsersDependenciesController@index')->name('index');
            Route::get('/create',                                       'AdminUsersDependenciesController@create')->name('create');
            Route::post('/',                                            'AdminUsersDependenciesController@store')->name('store');
            Route::get('/{adminUsersDependency}/edit',                  'AdminUsersDependenciesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'AdminUsersDependenciesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{adminUsersDependency}',                      'AdminUsersDependenciesController@update')->name('update');
            Route::delete('/{adminUsersDependency}',                    'AdminUsersDependenciesController@destroy')->name('destroy');
        });
    });
});


/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('postulantes')->name('postulantes/')->group(static function() {
            Route::get('/',                                             'PostulantesController@index')->name('index');
            Route::get('/create',                                       'PostulantesController@create')->name('create');
            Route::post('/',                                            'PostulantesController@store')->name('store');
            Route::get('/{postulante}/edit',                            'PostulantesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'PostulantesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{postulante}',                                'PostulantesController@update')->name('update');
            Route::delete('/{postulante}',                              'PostulantesController@destroy')->name('destroy');
            Route::get('/{id}/comentario',                              'PostulantesController@comentario')->name('comentario');
            Route::get('imprimir/{id}',                                 'PostulantesController@generatePDF')->name('impreso');
        });
    });
});



/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('comentarios')->name('comentarios/')->group(static function() {
            Route::get('/',                                             'ComentariosController@index')->name('index');
            // Route::get('/create',                                       'ComentariosController@create')->name('create');
            Route::get('{postulante_id}/create/{cedula}', 'ComentariosController@create')->name('comentarios.create');
            Route::post('/',                                            'ComentariosController@store')->name('store');
            Route::get('/{comentario}/edit',                            'ComentariosController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'ComentariosController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{comentario}',                                'ComentariosController@update')->name('update');
            Route::delete('/{comentario}',                              'ComentariosController@destroy')->name('destroy');
        });
    });
});


/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('motivos')->name('motivos/')->group(static function() {
            Route::get('/',                                             'MotivosController@index')->name('index');
            // Route::get('/create',                                       'MotivosController@create')->name('create');
            Route::get('{project_id}/create',                           'MotivosController@create')->name('motivos.create');
            Route::post('/',                                            'MotivosController@store')->name('store');
            Route::get('/{motivo}/edit',                                'MotivosController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'MotivosController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{motivo}',                                    'MotivosController@update')->name('update');
            Route::delete('/{motivo}',                                  'MotivosController@destroy')->name('destroy');
        });
    });
});


/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('admin-users-dependencies')->name('admin-users-dependencies/')->group(static function() {
            Route::get('/',                                             'AdminUsersDependenciesController@index')->name('index');
            Route::get('/create',                                       'AdminUsersDependenciesController@create')->name('create');
            Route::post('/',                                            'AdminUsersDependenciesController@store')->name('store');
            Route::get('/{adminUsersDependency}/edit',                  'AdminUsersDependenciesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'AdminUsersDependenciesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{adminUsersDependency}',                      'AdminUsersDependenciesController@update')->name('update');
            Route::delete('/{adminUsersDependency}',                    'AdminUsersDependenciesController@destroy')->name('destroy');
        });
    });
});
