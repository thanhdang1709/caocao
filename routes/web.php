<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EarnController;
use App\Http\Controllers\WithdrawlController;
use App\Http\Controllers\TicketController;
use App\Models\User;
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
use App\Http\Controllers\VerifyEmailCodeController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('login', function () {
    return response()->json(['error'=> 'Not Authencation']);
})->name('login');

// Route::get('/verify/{email}', [VerifyEmailCodeController::class, 'verify']);

// Route::get('/demo', function(){
//     return view(('mail_send'));
// });

Route::get('/tracking', function () {
    return  redirect('https://amazon.com');;
});


Route::group([
    // 'middleware' => 'auth',
    'prefix' => 'earn'

], function ($router) {
    Route::get('/list', [EarnController::class, 'list']);
    Route::post('/approve_task', [EarnController::class, 'approve_task']);
    Route::post('/reject_task', [EarnController::class, 'reject_task']);

    Route::post('/approve_user', [EarnController::class, 'approve_user']);
    Route::post('/reject_user', [EarnController::class, 'reject_user']);
    
});


Route::group([
    // 'middleware' => 'auth',
    'prefix' => 'withdraw'

], function ($router) {
    Route::get('/list', [WithdrawlController::class, 'list']);
    Route::post('/approve_request', [WithdrawlController::class, 'approve_request']);
    Route::post('/reject_request', [WithdrawlController::class, 'reject_request']);
    
});


Route::group([
    // 'middleware' => 'auth',
    'prefix' => 'ticket'

], function ($router) {
    Route::get('/list', [TicketController::class, 'list']);
    Route::get('/add', [TicketController::class, 'add']);
    Route::post('/sent', [TicketController::class, 'sent']);
});






Route::get('/reset_task', function(){
    \DB::table('user_ptc_task')->truncate();
    echo 'ok!!';
});

Route::get('/reset_token', function(){
    \DB::table('token_requests')->truncate();
    echo 'ok!!';
});

Route::get('/payments/offers/tapjoy', [OfferController::class, 'offer_tapjoy']);


Auth::routes();

Route::get('/home', function() {
    return view('home');
})->name('home')->middleware('auth');
