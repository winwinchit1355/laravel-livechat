<?php

use App\Events\ChatEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PusherChatMessageController;

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
    Route::get('messages', [PusherChatMessageController::class,'index'])->name('chat');
    Route::get('fetch-messages', [PusherChatMessageController::class, 'fetchMessages'])->name('fetch-message');
    // Route::post('send-message', [PusherChatMessageController::class,'store'])->name('send-message');
    Route::post('send-message',function (Request $request){
        event(new ChatEvent($request->username, $request->message));
        return ['success' => true];
    });
    Route::get('pusher-chat',[PusherChatMessageController::class, 'pusherChat'])->name('pusher-chat');
    Route::post('broadcast',[PusherChatMessageController::class, 'broadcast'])->name('broadcast');
    Route::post('receive',[PusherChatMessageController::class, 'receive'])->name('receive');
});
