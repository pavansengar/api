<?php

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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

/*URL For Demo Purpose*/

Route::get('/htmltopdf', 'DemoController@htmltopdf');

Route::get('/api_call', 'DemoController@api_call');

Route::get('/generateToken', 'DemoController@generateToken');

Route::get('/googleTrends', 'DemoController@google_trends');



Route::get('/cookie/set','CookieController@setCookie');

Route::get('/cookie/get','CookieController@getCookie');

Route::get('/cookie','DemoController@cookie');

Route::get('/check_login','DemoController@check_login');

Route::get('/getcookie','DemoController@getcookie');

Route::post('/validate_popup_login_demo','DemoController@validate_popup_login_demo');

Route::get('/db_migration','DemoController@db_migration');

Route::get('/si_users_migration','DemoController@si_users_migration');

Route::get('/si_expert_migration','DemoController@si_expert_migration');

Route::get('/si_expert_pending_migration','DemoController@si_expert_pending_migration');
Route::get('/image_cache','DemoController@image_cache');
Route::get('/youtube_chanel','DemoController@youtube_chanel');
Route::get('/google_calendar','DemoController@google_calendar');
Route::get('/google_calendar_php','DemoController@google_calendar_php');
Route::get('/webinar_report','DemoController@webinar_report');
