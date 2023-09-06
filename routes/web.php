<?php

use App\Events\ChatEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatMessageController;

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

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::group(['middleware' => 'auth:web'], function () {
    Route::get('messages', [ChatMessageController::class,'index'])->name('chat');
    Route::get('fetch-messages', [ChatMessageController::class, 'fetchMessages'])->name('fetch-message');
    // Route::post('send-message', [ChatMessageController::class,'store'])->name('send-message');
    Route::post('send-message',function (Request $request){
        event(new ChatEvent($request->username, $request->message));
        return ['success' => true];
    });
    Route::get('all',[ChatMessageController::class, 'all'])->name('all');
    Route::post('broadcast',[ChatMessageController::class, 'broadcast'])->name('broadcast');
    Route::post('receive',[ChatMessageController::class, 'receive'])->name('receive');
});
