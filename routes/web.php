<?php

use Illuminate\Support\Facades\Route;

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
Route::get('login',[\App\Http\Controllers\AuthController::class, 'login'])->name('login');
Route::group(['prefix' => 'admin'], function () {


        // Auth
        Route::get('admin/login', [\App\Http\Controllers\AuthController::class,'loginView'])->name('admin.login_form');
        Route::post('admin/login', [\App\Http\Controllers\AuthController::class,'loginSave'])->name('admin.login');

        // Middleware Routes
        Route::group(['prefix' => 'admin', 'middleware' => 'auth.admin'], function () {
            Route::get('admin/logout', [\App\Http\Controllers\AuthController::class,'logout'])->name('admin.logout');
            Route::get('admin/profile', [\App\Http\Controllers\AuthController::class,'profile'])->name('admin.profile');
            Route::post('admin/profile/save', [\App\Http\Controllers\AuthController::class,'profile_save'])->name('admin.profile.save');

            // Dashboard
            Route::get('/', [\App\Http\Controllers\AuthController::class,'showDashboard'])->name('admin.dashboard');

            //Products
            Route::prefix('user')->group(function () {
                Route::get('/', [\App\Http\Controllers\UserController::class,'index'])->name('admin.users');
                Route::get('/matchmakers', [\App\Http\Controllers\UserController::class,'matchmakers'])->name('admin.matchmakers');
                Route::get('/athletes', [\App\Http\Controllers\UserController::class,'athletes'])->name('admin.athletes');
                Route::get('import-page', 'ProductController@getImport')->name('admin.product.import.page');
                Route::post('import', 'ProductController@importProductsFromFile')->name('admin.product.import');
                Route::delete('delete/{id}', [\App\Http\Controllers\UserController::class,'destroy'])->name('admin.user.delete');
                Route::get('fetch-tags-of-user', [\App\Http\Controllers\UserController::class,'user_tags'])->name('fetch.tags.by.user');
                Route::get('user-add-record', [\App\Http\Controllers\UserController::class,'add_record'])->name('user.add.record');
                Route::post('user-save-record', [\App\Http\Controllers\UserController::class,'save_record'])->name('admin.user.record.save');
                Route::get('fetch-user-jobs', [\App\Http\Controllers\UserController::class,'user_jobs'])->name('fetch.user.jobs');
                Route::get('fetch-user-fights', [\App\Http\Controllers\UserController::class,'user_fights'])->name('fetch.user.fights');
                Route::get('fetch-user-arranged-fights', [\App\Http\Controllers\UserController::class,'user_fights_arranged'])->name('fetch.user.arranged.fights');
                Route::get('get_result', [\App\Http\Controllers\UserController::class,'get_result'])->name('get_result');
                Route::get('accept_reject', [\App\Http\Controllers\UserController::class,'accept_reject'])->name('accept_reject.user');
                Route::get('ban_unban', [\App\Http\Controllers\UserController::class,'ban_unban'])->name('ban_unban.user');
            });

            //Category
            Route::prefix('fights')->group(function () {
                Route::get('/sparrings', [\App\Http\Controllers\FightController::class,'sparrings'])->name('admin.sparrings');
                Route::get('/fights', [\App\Http\Controllers\FightController::class,'fights'])->name('admin.fights');
                Route::get('/highlights', [\App\Http\Controllers\FightController::class,'highlights'])->name('admin.highlights');
                Route::delete('highlights-delete/{id}', [\App\Http\Controllers\FightController::class,'destroyHighlights'])->name('admin.highlight.delete');
                Route::delete('sparring-delete/{id}', [\App\Http\Controllers\FightController::class,'destroy'])->name('admin.sparring.delete');
                Route::delete('fight-delete/{id}', [\App\Http\Controllers\FightController::class,'fightDestroy'])->name('admin.fight.delete');
                Route::get('decide_winner', [\App\Http\Controllers\FightController::class,'decide_winner'])->name('decide_winner.user');
                Route::get('k_o', [\App\Http\Controllers\FightController::class,'ko'])->name('ko.user');

                Route::get('add', 'CategoryController@add')->name('admin.category.add');
                Route::get('edit/{id}', 'CategoryController@edit')->name('admin.category.edit');
                Route::post('update', 'CategoryController@update')->name('admin.category.update');
                Route::post('save', 'CategoryController@store')->name('admin.category.save');
                Route::delete('delete/{id}', 'CategoryController@delete')->name('admin.category.delete');

                Route::get('get_sub_categories/{id}', 'CategoryController@get_sub_categories');
                Route::post('sub_edit', 'CategoryController@edit_sub_category')->name('admin.sub.category.edit');
                Route::delete('sub_delete/{id}', 'CategoryController@delete_sub_category')->name('admin.sub.category.delete');
            });

            Route::prefix('list')->group(function () {
                Route::get('/tags', [\App\Http\Controllers\AdminController::class,'tags'])->name('admin.tags');
                Route::get('/edit-tags/{id}', [\App\Http\Controllers\AdminController::class,'edit_tags'])->name('admin.edit.tags');
                Route::post('tag-save', [\App\Http\Controllers\AdminController::class,'tagSave'])->name('admin.tag.save');
                Route::get('/sub_tags/{id}', [\App\Http\Controllers\AdminController::class,'sub_tags'])->name('admin.sub.tags');

                Route::get('/add_sub_tags/{id}', [\App\Http\Controllers\AdminController::class,'add_sub_tags'])->name('admin.add.sub.tags');
                Route::get('/edit_sub_tags/{id}', [\App\Http\Controllers\AdminController::class,'edit_sub_tags'])->name('admin.edit.sub.tags');
                Route::delete('sub_tags-delete/{id}', [\App\Http\Controllers\AdminController::class,'subDestroy'])->name('admin.sub.delete');
                Route::post('sub_tags-save', [\App\Http\Controllers\AdminController::class,'subSave'])->name('admin.sub.save');

                Route::get('/stances', [\App\Http\Controllers\AdminController::class,'stances'])->name('admin.stances');
                Route::get('/add_stance', [\App\Http\Controllers\AdminController::class,'add_stance'])->name('admin.add.stance');
                Route::get('/edit_stance/{id}', [\App\Http\Controllers\AdminController::class,'edit_stance'])->name('admin.edit.stance');
                Route::post('stance-save', [\App\Http\Controllers\AdminController::class,'stanceSave'])->name('admin.stance.save');
                Route::delete('stance-delete/{id}', [\App\Http\Controllers\AdminController::class,'stanceDestroy'])->name('admin.stance.delete');


                Route::get('/events', [\App\Http\Controllers\AdminController::class,'events'])->name('admin.events');
                Route::get('/add_event', [\App\Http\Controllers\AdminController::class,'add_event'])->name('admin.add.event');
                Route::get('/edit_event/{id}', [\App\Http\Controllers\AdminController::class,'edit_event'])->name('admin.edit.event');
                Route::post('event-save', [\App\Http\Controllers\AdminController::class,'eventSave'])->name('admin.event.save');
                Route::delete('event-delete/{id}', [\App\Http\Controllers\AdminController::class,'eventDestroy'])->name('admin.event.delete');


                Route::get('/statuses', [\App\Http\Controllers\AdminController::class,'statuses'])->name('admin.statuses');
                Route::get('/add_status', [\App\Http\Controllers\AdminController::class,'add_status'])->name('admin.add.status');
                Route::get('/edit_status/{id}', [\App\Http\Controllers\AdminController::class,'edit_status'])->name('admin.edit.status');
                Route::post('status-save', [\App\Http\Controllers\AdminController::class,'statusSave'])->name('admin.status.save');

                Route::get('/plans', [\App\Http\Controllers\AdminController::class,'plans'])->name('admin.plans');
                Route::get('/add-plan', [\App\Http\Controllers\AdminController::class,'addPlan'])->name('admin.add.plan');
                Route::post('/save-plan', [\App\Http\Controllers\AdminController::class,'savePlan'])->name('admin.plan.save');
                Route::get('/edit-plan/{id}', [\App\Http\Controllers\AdminController::class,'edit_plan'])->name('admin.edit.plan');
                Route::delete('plan-delete/{id}', [\App\Http\Controllers\AdminController::class,'planDestroy'])->name('admin.plan.delete');



            });
            });



});
