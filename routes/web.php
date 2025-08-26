<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PostulantesController;
use App\Http\Controllers\Admin\ProjectsController;
use App\Http\Controllers\HomeController;


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
            Route::get('/{project}/showVERDOCFONAVIS',                  'ProjectsController@showVERDOCFONAVIS')->name('VERDOCFONAVIS');
            Route::get('/{project}/showFONAVISADJ',                     'ProjectsController@showFONAVISADJ')->name('FONAVISADJ');
            Route::get('/{project}/showFONAVISSOCIAL',                  'ProjectsController@showFONAVISSOCIAL')->name('FONAVISSOCIAL');
            Route::get('/{project}/showFONAVISTECNICO',                 'ProjectsController@showFONAVISTECNICO')->name('FONAVISTECNICO');
            Route::get('/{project}/showFONAVISTECNICODOS',              'ProjectsController@showFONAVISTECNICODOS')->name('FONAVISTECNICODOS');
            Route::get('/{project}/showDGSO',                           'ProjectsController@showDGSO')->name('DGSO');
            Route::get('/{project}/showDIGH',                           'ProjectsController@showDIGH')->name('DIGH');
            Route::get('/{project}/showDSGO',                           'ProjectsController@showDSGO')->name('DSGO');
            Route::get('/{project}/transition',                         'ProjectsController@transition')->name('transition');
            Route::get('/{project}/notificar',                          'ProjectsController@notificar')->name('notificar');
            Route::get('/{project}/historial',                          'ProjectsController@historial')->name('historial');
            Route::get('/{project}/transitionEliminar',                 'ProjectsController@transitionEliminar')->name('transitionEliminar');
            Route::get('/{project}/edit',                               'ProjectsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'ProjectsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{project}',                                   'ProjectsController@update')->name('update');
            Route::delete('/{project}',                                 'ProjectsController@destroy')->name('destroy');
            Route::get('/{project}/project',                            'ProjectsController@project')->name('project');
            //DESCARGAR DOCUMENTOS LADO ADM
            Route::get('descargarDocumento/{project}/faltantes/{document_id}/{file_name}', 'ProjectsController@descargarDocumento')->name('descargarDocumento');
            Route::get('downloadfileDoc/{project}/{document_id}/{file_name}', 'ProjectsController@downloadFile')->name('downloadFileDoc');
            Route::post('/{project}/save-digh-observation', 'ProjectsController@saveDIGHObservation')->name('saveDIGHObservation');

            Route::get('ajax/{state_id?}/lands', [ProjectController::class, 'lands']);
            Route::get('ajax/{state_id?}/typology', [ProjectController::class, 'typology']);
            Route::get('ajax/{state_id?}/local', [ProjectController::class, 'distrito']);

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
            Route::post('/guardar',                                     'ProjectStatusController@storeNotificacion')->name('storeNotificacion');
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
        Route::prefix('postulantes')->name('postulantes/')->group(static function() {
            Route::get('/',                                             'PostulantesController@index')->name('index');
            Route::get('/create',                                       'PostulantesController@create')->name('create');
            Route::post('/',                                            'PostulantesController@store')->name('store');
            Route::get('/{postulante}/edit',                            'PostulantesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'PostulantesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{postulante}',                                'PostulantesController@update')->name('update');
            Route::delete('/{postulante}',                              'PostulantesController@destroy')->name('destroy');
            Route::get('/{id}/comentario',                              'PostulantesController@comentario')->name('comentario');
            Route::get('/{postulante}/imprimir',                        'PostulantesController@imprimir')->name('imprimir');
            Route::post('/{id}/actualizar',                             'PostulantesController@actualizar')->name('actualizar');
            Route::get('/export-postulantes',                           'PostulantesController@export')->name('export');
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
        Route::prefix('reportes')->name('reportes/')->group(static function() {
            Route::get('/',                                             'ReporteController@index')->name('index');
            Route::get('/create',                                       'ReporteController@create')->name('create');
            Route::post('/',                                            'ReporteController@store')->name('store');
            Route::get('/{reporte}/edit',                               'ReporteController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'ReporteController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{reporte}',                                   'ReporteController@update')->name('update');
            Route::delete('/{reporte}',                                 'ReporteController@destroy')->name('destroy');
            Route::get('/resultados',                                   'ReporteController@resultados')->name('resultados');
            Route::get('/cities',                                       'ReporteController@getCities')->name('ciudades');
            // Route::get('/exportar',                                     'ReporteController@exportar')->name('exportar');
            // Route::get('/exportar/{inicio?}/{fin?}/{proyecto_id?}/{sat_id?}/{state_id?}/{city_id?}/{modalidad_id?}/{stage_id?}', [ReporteController::class, 'exportar'])->name('exportar');
            Route::get('/exportar-resultados', 'ReporteController@exportarExcel')->name('exportar.excel');

        });
    });
});


/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('project-has-expedientes')->name('project-has-expedientes/')->group(static function() {
            Route::get('/',                                             'ProjectHasExpedientesController@index')->name('index');
            Route::get('/create',                                       'ProjectHasExpedientesController@create')->name('create');
            Route::post('/',                                            'ProjectHasExpedientesController@store')->name('store');
            Route::get('/{projectHasExpediente}/edit',                  'ProjectHasExpedientesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'ProjectHasExpedientesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{projectHasExpediente}',                      'ProjectHasExpedientesController@update')->name('update');
            Route::delete('/{projectHasExpediente}',                    'ProjectHasExpedientesController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('project-olds')->name('project-olds/')->group(static function() {
            Route::get('/',                                             'ProjectOldsController@index')->name('index');
            Route::get('/create',                                       'ProjectOldsController@create')->name('create');
            Route::post('/',                                            'ProjectOldsController@store')->name('store');
            Route::get('/{projectOld}/edit',                            'ProjectOldsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'ProjectOldsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{projectOld}',                                'ProjectOldsController@update')->name('update');
            Route::delete('/{projectOld}',                              'ProjectOldsController@destroy')->name('destroy');
            Route::get('/{project}/project',                            'ProjectOldsController@project')->name('proyecpostulantes');
        });
    });
});

Auth::routes();

    // Página principal
    Route::get('/', [ProjectController::class, 'index']);
    Route::get('/home', [ProjectController::class, 'index'])->name('home');

    // AJAX públicos
    Route::get('projects/ajax/{state_id?}/lands', [ProjectController::class, 'lands']);
    Route::get('projects/ajax/{state_id?}/typology', [ProjectController::class, 'typology']);
    Route::get('projects/ajax/{state_id?}/local', [ProjectController::class, 'distrito']);
    Route::resource('projects', ProjectController::class);




        Route::get('projects/{id}/eliminados', [ProjectController::class, 'showEliminados']);
        Route::get('generate-pdf/{id}', [ProjectController::class, 'generatePDF'])->name('generate-pdf');
        Route::get('projects/ajax/{id}/checkdocuments/{project_id}/{sheets}', [ProjectController::class, 'checkdocuments']);

        // Postulantes
        Route::get('projects/{id}/postulantes', [PostulantesController::class, 'index']);
        Route::get('projects/{id}/postulantes/{idpostulante}', [PostulantesController::class, 'show'])->name('projects.postulantes.show');
        Route::get('/postulantes/edit/{id}/{idpostulante}', [PostulantesController::class, 'editarPostulante'])->name('postulantes.edit');
        Route::get('/miembros/edit/{id}/{idpostulante}', [PostulantesController::class, 'editmiembro'])->name('miembros.edit');

        // Mostrar documentos
        Route::get('projectsDoc/{id}', [ProjectController::class, 'showDoc']);
        Route::get('projectsMiembros/{id}', [ProjectController::class, 'showProyMiembros']);
        Route::get('projectsTecnico/{id}', [ProjectController::class, 'showTecnico']);
        Route::get('projectsDocTec/{id}', [ProjectController::class, 'showDocTec']);
        Route::get('docObservados/{id}', [ProjectController::class, 'DocObservados']);
        Route::get('projectsDocNoExcluyentes/{id}', [ProjectController::class, 'showDocNoExcluyentes']);
        Route::get('projectsDocCondominio/{id}', [ProjectController::class, 'showDocCondominio']);

        // Descargar documentos
        Route::get('download/{project}/{document_id}/{file_name}', [ProjectController::class, 'downloadFile'])->name('downloadFile');
        Route::get('bajarDocumento/{project}/faltantes/{document_id}/{file_name}', [ProjectController::class, 'bajarDocumento'])->name('bajarDocumento');

        // Eliminar documentos
        Route::get('documents/eliminar/{project_id}/{document_id}', [ProjectController::class, 'eliminar'])->name('eliminar');
        Route::get('documents/eliminardocumento/{project_id}/{document_id}', [ProjectController::class, 'eliminarDocumento'])->name('eliminarDocumento');


    // POST públicas
    Route::get('projects/send/{id}', [ProjectController::class, 'send']);
    Route::post('projects/{id}/postulantes/create', [PostulantesController::class, 'create']);
    // Route::post('projects/{id}/postulantes/{x}/createmiembro', [PostulantesController::class, 'createmiembro']);
    Route::get('projects/{id}/postulantes/{x}/createmiembro', [PostulantesController::class, 'createmiembro']);
    Route::post('postulantes/destroy', [PostulantesController::class, 'destroy']);
    Route::post('postulantes/destroymiembro', [PostulantesController::class, 'destroymiembro']);
    Route::post('savepostulante', [PostulantesController::class, 'store']);
    Route::post('savepostulanteEdit', [PostulantesController::class, 'storeEditPostulante']);
    Route::post('savemiembro', [PostulantesController::class, 'storemiembro']);
    Route::post('savemiembroeditar', [PostulantesController::class, 'updatemiembro']);

    // Adjuntar documentos
    Route::post('levantar', [ProjectController::class, 'upload']);
    Route::post('levantarDocumento', [ProjectController::class, 'uploadDocumento']);
    Route::post('levantarTecnico', [ProjectController::class, 'uploadTecnico']);
    Route::post('levantarObs', [ProjectController::class, 'uploadObservado']);
    Route::post('levantarNoExcluyente', [ProjectController::class, 'uploadNoExcluyente']);
    Route::post('levantarCondominio', [ProjectController::class, 'uploadCondominio']);

    // Imprimir
    Route::get('imprimir/{id}', [PostulantesController::class, 'generatePDF'])->name('imprimir');

    // Enviar documentos faltantes
    Route::post('/enviar-documentos-faltantes', [ProjectController::class, 'enviarDocumentosFaltantes'])->name('enviarDocumentosFaltantes');

    // Ver archivos almacenados
    Route::get('/storage/{folder}/{filename}', function ($folder, $filename) {
        $path = storage_path("app/{$folder}/{$filename}");
        if (file_exists($path)) {
            return response()->file($path);
        } else {
            abort(404);
        }
    })->where('filename', '(.*)');

    // Validación de código QR
    Route::get('/{key}', [HomeController::class, 'verification']);
