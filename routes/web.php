<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;

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
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('admin-users')->name('admin-users/')->group(static function() {
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
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::get('/profile',                                      'ProfileController@editProfile')->name('edit-profile');
        Route::post('/profile',                                     'ProfileController@updateProfile')->name('update-profile');
        Route::get('/password',                                     'ProfileController@editPassword')->name('edit-password');
        Route::post('/password',                                    'ProfileController@updatePassword')->name('update-password');
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('modalities')->name('modalities/')->group(static function() {
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
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('lands')->name('lands/')->group(static function() {
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
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('documents')->name('documents/')->group(static function() {
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
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('categories')->name('categories/')->group(static function() {
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
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('project-types')->name('project-types/')->group(static function() {
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
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('stages')->name('stages/')->group(static function() {
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
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('typologies')->name('typologies/')->group(static function() {
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
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('parentescos')->name('parentescos/')->group(static function() {
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
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('discapacidads')->name('discapacidads/')->group(static function() {
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
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('modality-has-lands')->name('modality-has-lands/')->group(static function() {
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
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('land-has-project-types')->name('land-has-project-types/')->group(static function() {
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
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('assignments')->name('assignments/')->group(static function() {
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
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('project-type-has-typologies')->name('project-type-has-typologies/')->group(static function() {
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

Route::resource('projects', ProjectController::class);
Route::get('projects/send/{id}', [App\Http\Controllers\ProjectController::class, 'send']);

Route::get('generate-pdf/{id}',[App\Http\Controllers\ProjectController::class,'generatePDF']);

Route::get('projects/ajax/{state_id?}/cities',[App\Http\Controllers\ProjectController::class,'distrito']);
Route::get('projects/ajax/{state_id?}/lands',[App\Http\Controllers\ProjectController::class,'lands']);
Route::get('projects/ajax/{state_id?}/typology',[App\Http\Controllers\ProjectController::class,'typology']);
Route::get('projects/ajax/{state_id?}/local',[App\Http\Controllers\ProjectController::class,'distrito']);

//Postulantes
Route::get('projects/{id}/postulantes', [App\Http\Controllers\PostulantesController::class,'index']);
Route::post('projects/{id}/postulantes/create', [App\Http\Controllers\PostulantesController::class,'create']);
Route::post('projects/{id}/postulantes/{x}/createmiembro', [App\Http\Controllers\PostulantesController::class,'createmiembro']);
Route::post('postulantes/destroy', [App\Http\Controllers\PostulantesController::class,'destroy']);
Route::post('savepostulante', [App\Http\Controllers\PostulantesController::class, 'store']);
Route::post('savemiembro', [App\Http\Controllers\PostulantesController::class, 'storemiembro']);
Route::get('imprimir/{id}','App\Http\Controllers\PostulantesController@generatePDF')->name('imprimir');


/*Route::get('projects/{id}/postulantes/{idpostulante}', 'PostulantesController@show');
Route::get('projects/{id}/postulantes/{idpostulante}/edit', 'PostulantesController@edit');
Route::post('editpostulante', 'PostulantesController@update');
Route::post('postulantes/upload', 'PostulantesController@upload');
Route::post('postulantes/destroyfile', 'PostulantesController@destroyfile');

Route::post('postulantes/destroy', 'PostulantesController@destroy');
Route::post('postulantes/destroymiembro', 'PostulantesController@destroymiembro');*/


//validacion qr
Route::get('/{key}',[App\Http\Controllers\HomeController::class,'verification']);

//
Route::get('projects/ajax/{id}/checkdocuments/{project_id}/{sheets}',[App\Http\Controllers\ProjectController::class,'checkdocuments']);


/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('users')->name('users/')->group(static function() {
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
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('projects')->name('projects/')->group(static function() {
            Route::get('/',                                             'ProjectsController@index')->name('index');
            Route::get('/create',                                       'ProjectsController@create')->name('create');
            Route::post('/',                                            'ProjectsController@store')->name('store');
            Route::get('/{project}/show',                               'ProjectsController@show');
            Route::get('/{project}/transition',                         'ProjectsController@transition');
            Route::get('/{project}/edit',                               'ProjectsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'ProjectsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{project}',                                   'ProjectsController@update')->name('update');
            Route::delete('/{project}',                                 'ProjectsController@destroy')->name('destroy');
        });
    });
});


/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('document-checks')->name('document-checks/')->group(static function() {
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
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('project-statuses')->name('project-statuses/')->group(static function() {
            Route::get('/',                                             'ProjectStatusController@index')->name('index');
            Route::get('/create',                                       'ProjectStatusController@create')->name('create');
            Route::post('/',                                            'ProjectStatusController@store')->name('store');
            Route::get('/{projectStatus}/edit',                         'ProjectStatusController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'ProjectStatusController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{projectStatus}',                             'ProjectStatusController@update')->name('update');
            Route::delete('/{projectStatus}',                           'ProjectStatusController@destroy')->name('destroy');
        });
    });
});
