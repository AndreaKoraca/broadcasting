<?php

use Illuminate\Http\Request;
use App\Events\MessageSent;

Route::get('/', fn() => view('chat'));

Route::post('/send-message', function (Request $request) {
    broadcast(new MessageSent($request->username, $request->message));
    return response()->json(['success' => true]);
});



