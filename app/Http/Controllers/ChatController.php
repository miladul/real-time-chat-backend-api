<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ChatController extends Controller
{
    // fetch conversation with a user
    public function conversation(User $user, Request $request)
    {
        $me = $request->user()->id;

        $messages = Message::query()
            ->where(function ($q) use ($me, $user) {
                $q->where('sender_id', $me)->where('receiver_id', $user->id);
            })
            ->orWhere(function ($q) use ($me, $user) {
                $q->where('sender_id', $user->id)->where('receiver_id', $me);
            })
            ->orderBy('created_at')
            ->get();

        // mark as read (messages to me)
        Message::where('sender_id', $user->id)
            ->where('receiver_id', $me)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return $messages;
    }

    // send message
    public function send(Request $request)
    {
        $data = $request->validate([
            'receiver_id' => ['required', Rule::exists('users','id')->withoutTrashed()],
            'body'        => ['required','string','max:5000'],
        ]);

        if ((int)$data['receiver_id'] === (int)$request->user()->id) {
            return response()->json(['message'=>'Cannot message yourself'], 422);
        }

        $message = Message::create([
            'sender_id'   => $request->user()->id,
            'receiver_id' => $data['receiver_id'],
            'body'        => $data['body'],
        ]);

        // Fire Pusher event
        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message, 201);
    }
}

