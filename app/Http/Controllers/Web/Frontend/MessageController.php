<?php

namespace App\Http\Controllers\Web\Frontend;

use App\Events\MessageSendEvent;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller {
    /**
     * Show the chat page.
     */
    public function show($id) {
        $receiver   = User::findOrFail($id);
        $senderId   = Auth::id();
        $receiverId = $id;

        $messages = Message::where(function ($query) use ($senderId, $receiverId) {
            $query->where('sender_id', $senderId)
                ->where('receiver_id', $receiverId);
        })->orWhere(function ($query) use ($senderId, $receiverId) {
            $query->where('sender_id', $receiverId)
                ->where('receiver_id', $senderId);
        })
            ->with('sender:id,first_name,last_name,avatar', 'receiver:id,first_name,last_name,avatar')
            ->get();

        return view('frontend.layouts.chat.index', [
            'receiver' => $receiver,
            'messages' => $messages,
        ]);
    }

    /**
     * Send a new message.
     */
    public function sendMessage(Request $request) {
        $validated = $request->validate([
            'message'     => 'required',
            'receiver_id' => 'required|exists:users,id',
        ]);

        $message              = new Message();
        $message->sender_id   = Auth::id();
        $message->receiver_id = $validated['receiver_id'];
        $message->message     = $validated['message'];
        $message->save();

        // Broadcast to the receiver’s channel
        broadcast(new MessageSendEvent($message))->toOthers();

        // Construct a public URL for the sender’s avatar
        $avatar = $message->sender->avatar
        ? asset($message->sender->avatar)
        : asset('backend/images/default_images/user_1.jpg');

        return response()->json([
            'id'         => $message->id,
            'message'    => $message->message,
            'created_at' => $message->created_at->format('H:i'),
            'sender'     => [
                'id'         => $message->sender->id,
                'first_name' => $message->sender->first_name,
                'avatar'     => $avatar,
            ],
        ]);
    }
}
