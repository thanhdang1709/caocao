<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CaptchaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EarnController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\WithdrawlController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use App\Models\User;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!4
|
*/
use App\Http\Controllers\VerifyEmailCodeController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('login', function () {
    return view('auth.login');
})->name('login');

Route::get('/permission', function () {
    return view('permission');
})->name('permission');

// Route::get('/verify/{email}', [VerifyEmailCodeController::class, 'verify']);

// Route::get('/demo', function(){
//     return view(('mail_send'));
// });

Route::get('/tracking', function () {
    return  redirect('https://amazon.com');;
});


Route::group([
    'middleware' => ['auth', 'is_admin'],
    'prefix' => '/'

], function ($router) {
});


Route::group([
    'middleware' => ['auth', 'is_admin'],
    'prefix' => 'earn'

], function ($router) {
    Route::get('/list', [EarnController::class, 'list']);
    Route::post('/approve_task', [EarnController::class, 'approve_task']);
    Route::post('/reject_task', [EarnController::class, 'reject_task']);

    Route::post('/approve_user', [EarnController::class, 'approve_user']);
    Route::post('/reject_user', [EarnController::class, 'reject_user']);
});


Route::group([
    'middleware' => ['auth', 'is_admin'],
    'prefix' => 'withdraw'

], function ($router) {
    Route::get('/list', [WithdrawlController::class, 'list']);
    Route::post('/approve_request', [WithdrawlController::class, 'approve_request']);
    Route::post('/reject_request', [WithdrawlController::class, 'reject_request']);
});


Route::group([
    'middleware' => ['auth', 'is_admin'],
    'prefix' => 'ticket', 'as' => 'ticket.'

], function ($router) {
    Route::get('/list', [TicketController::class, 'list'])->name('list');
    Route::get('/add', [TicketController::class, 'add'])->name('add');
    Route::get('/sent', [TicketController::class, 'sent'])->name('sent');
    Route::get('/update', [TicketController::class, 'updateStatus'])->name('update');
});


Route::group([
    'middleware' => ['auth', 'is_admin'],
    'prefix' => 'user', 'as' => 'user.'

], function ($router) {
    Route::get('/list', [HomeController::class, 'getUser']);
    Route::get('/ban', [HomeController::class, 'banUser']);
    Route::get('/banned', [HomeController::class, 'getBannedUser']);
});

Route::group([
    'middleware' => ['auth', 'is_admin'],
    'prefix' => 'post', 'as' => 'post.'

], function ($router) {
    //POST
    Route::get('/create', function () {
        return view('post.post');
    });
    Route::post('/create', [PostController::class, 'store'])->name('create');

    Route::get('/list', [PostController::class, 'getList']);
    Route::get('/delete/{id}', [PostController::class, 'deletePost']);
});


Route::get('/payments/offers/tapjoy', [OfferController::class, 'offer_tapjoy']);


//SYSTEM
Route::get('/system', [SystemController::class, 'system'])->name('system');
Route::post('/system', [SystemController::class, 'system_update']);



//HOME
Route::get('/home', function () {
    return view('home');
})->name('home')->middleware(['auth', 'is_admin']);

// Auth::routes();
Auth::routes(['logout' => false]);

Route::get('/logout', [LoginController::class, 'logout'])->name('logout');


// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


// Route::get('/reset_task', function () {
//     \DB::table('user_ptc_task')->truncate();
//     echo 'ok!!';
// });

// Route::get('/reset_token', function () {
//     \DB::table('token_requests')->truncate();
//     echo 'ok!!';
// });