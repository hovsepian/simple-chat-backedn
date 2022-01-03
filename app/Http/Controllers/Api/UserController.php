<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Auth;

class UserController extends Controller
{
    public function users(Request $request){
        $query = $request->input('query');
        $auth = Auth::guard('api')->id();

        $users = User::where('id', '!=', $auth);

        if(!empty($query)){
            $users->where('name', 'like', '%'.$query.'%');
        }
        $users = $users->get();

        return response()->json($users, 200);
    }

    public function startChat(Request $request){
        $user = User::find($request->user_id);
        $auth = Auth::guard('api')->user();

        $chat = $auth->chats()->whereNull('name')->whereIn('key', $user->chats()->pluck('key')->toArray())->first();

        if(!$chat){
            $chat = new Chat();
            $chat->key = strtotime('now');
            $chat->save();

            $chat->users()->attach([$auth->id, $user->id]);
        }


        return response()->json($chat, 200);
    }

    public function loadChats(Request $request){
        $auth = Auth::guard('api')->user();
        $chats = $auth->chats()->whereNull('name')->with('users', function($q) use ($auth){ $q->where('user_id', '!=', $auth->id); })->get();
        $rooms = $auth->chats()->whereNotNull('name')->get();

        return response()->json([
            'chats' => $chats,
            'rooms' => $rooms
        ]);
    }

    public function loadChat(Request $request){
        $auth = Auth::guard('api')->user();
        $chat = Chat::where('key', $request->key)->with('users')->with('messages', function($q){
            $q->with('sender');
        })->first();

        foreach($chat->messages as $message){
            if($message->sender_id != $auth->id){
                $message->seen = 1;
                $message->save();
            }
        }

        return response()->json($chat);
    }

    public function sendMessage(Request $request){
        $key = $request->key;
        $body = $request->message;
        $auth = Auth::guard('api')->user();

        $chat = Chat::where('key', $key)->first();

        if($chat){
            $message = ChatMessage::create(['chat_id' => $chat->id, 'message' => $body, 'sender_id' => $auth->id]);

            $messages = $chat->messages()->where(function($q) use ($auth){
                $q->where('seen', 0);
                $q->where('sender_id', '!=', $auth->id);
            })->orWhere('id', $message->id)->with('sender')->get();
        }else{
            $messages = null;
        }

        return response()->json($messages);

    }

    public function createRoom(Request $request){
        $name = $request->name;
        $auth = Auth::guard('api')->user();

        $chat = Chat::create(['key' => strtotime('now'), 'name' => $name]);

        $chat->users()->attach([$auth->id]);

        return response()->json($chat);
    }

    public function addRoomUser(Request $request){
        $key = $request->key;
        $user_id = $request->user_id;

        $chat = Chat::where('key', $key)->first();

        $checkUser = $chat->users()->where('user_id', $user_id)->first();

        $exists = true;

        if(!$checkUser){
            $chat->users()->attach([$user_id]);
            $exists = false;
        }

        return response()->json([
            'exists' => $exists,
            'users' => $chat->users()->get()
        ]);
    }
}
