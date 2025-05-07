<?php

use App\Models\User;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Notifications\MessageNotification;

Route::get('/', function () {
    return view('chat');
});

Route::post('/send-message', function (Request $request) {
    broadcast(new MessageSent($request->username, $request->message));

    $user = User::find(1);

    if ($user) {
        $user->notify(new MessageNotification($request->username, $request->message));
    }

    return response()->json(['success' => true]);
});

Route::get('/notifications', function () {
    $user = \App\Models\User::find(1);

    return Response::json($user?->notifications ?? []);
});






