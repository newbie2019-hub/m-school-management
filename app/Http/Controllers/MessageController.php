<?php

namespace App\Http\Controllers;

use App\Events\ChatEvent;
use App\Models\Administrator;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth($request->auth_type)->id();
        if(!$request->auth_type) {
            abort(404);
        }
        /**
         *  Check the authenticated user
         *  and then query the recipient.
         */
        if ($request->auth_type == 'teacher') {
            $type = 'student';
        }

        if ($request->auth_type == 'administrator') {
            $type = 'teacher';
        }

        if ($request->auth_type == 'student') {
            $type = 'teacher';
        }

        $conversations = Conversation::with(['latest', 'one_userable', 'two_userable'])
            ->where(function ($query) use ($userId, $request) {
                $query->where('user_one', $userId)
                    ->where('user_one_type', $request->auth_type);
            })
            ->orWhere(function ($query) use ($userId, $request) {
                $query->where('user_two', $userId)
                    ->where('user_two_type', $request->auth_type);
            })->get();

        return view('messages', [
            'conversations' => $conversations,
            'auth_id' => auth($request->auth_type)->id(),
            'auth_type' => $request->auth_type,
            'type' => $type
        ]);
    }

    public function show(Request $request, $id)
    {
        $userId = auth($this->getAuthUser())->id();
        if(!$request->recipient_type) {
            abort(404);
        }

        /**
         *  Check the authenticated user
         *  and then query the recipient.
         */
        if ($request->recipient_type == 'teacher') {
            $user = Teacher::where('id', $id)->first();
            $type = 'teacher';
        }

        if ($request->recipient_type == 'student') {
            $user = Student::where('id', $id)->first();
            $type = 'student';
        }

        if ($request->recipient_type == 'administrator') {
            $user = Administrator::where('id', $id)->first();
            $type = 'administrator';
        }

        /**
         *  Check if is has existing conversation
         *  and messages to prevent creating duplicate
         *  conversations for both users.
         */
        $conversation = Conversation::with(['messages'])->where(
            [
                ['user_one', $userId],
                ['user_one_type', $request->auth_type],
                ['user_two', $id],
                ['user_two_type', $request->recipient_type]
            ],
        )->orWhere(
            [
                ['user_one', $id],
                ['user_one_type', $request->recipient_type],
                ['user_two', $userId],
                ['user_two_type', $request->auth_type]
            ]
        )->first();


        if (!$conversation) {
            $conversation = Conversation::create([
                'user_one' => auth($request->auth_type)->id(),
                'user_one_type' => $request->auth_type,
                'one_userable_id' => auth($request->auth_type)->id(),
                'one_userable_type' => $this->modelClass($request->auth_type),
                'two_userable_id' => $id,
                'two_userable_type' => $this->modelClass($request->recipient_type),
                'user_two' => $id,
                'user_two_type' => $request->recipient_type,
            ]);

            $conversation->load(['messages']);
        }

        // dd($type, $userId, $id, $this->getAuthUser());

        /**
         * Will return null conversation
         * if there is no existing converstion
         * yet.
         */
        return view('message', [
            'conversation' => $conversation,
            'user_two' => $user->id,
            'user_two_type' => $type,
            'user' => $user,
            'auth_id' => auth($request->auth_type)->id(),
            'auth_type' => $request->auth_type
        ]);
    }

    public function modelClass($class)
    {
        return match ($class) {
            'administrator' => Administrator::class,
            'student' => Student::class,
            'teacher' => Teacher::class
        };
    }

    public function store(Request $request)
    {
        $userId = auth($this->getAuthUser())->id();

        $request->validate([
            'user_two' => 'required',
            'message' => 'required|max:200',
            'user_two_type' => 'required',
        ]);

        //Store the message
        Message::create([
            'conversation_id' => $request->conversation_id,
            'message' => $request->message,
            'sender_id' => $request->auth_id,
            'sender_type' => $request->auth_type
        ]);

        //Emit the event with the conversation id and user type of the recepient user
        ChatEvent::dispatch($request->message, $request->conversation_id, $request->user_two_type);

        // return back()->with(['status' => 'success']);
    }

    public function getAuthUser()
    {
        if (auth('teacher')->user()) {
            return 'teacher';
        }

        if (auth('student')->user()) {
            return 'student';
        }

        if (auth('administrator')->user()) {
            return 'administrator';
        }
    }
}
