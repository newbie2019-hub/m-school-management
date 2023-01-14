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
        $userId = auth($this->getAuthUser())->id();
        /**
         *  Check the authenticated user
         *  and then query the recipient.
         */
        if ($this->getAuthUser() == 'teacher') {
            $type = 'student';
        }

        if ($this->getAuthUser() == 'administrator') {
            $type = 'teacher';
        }

        if ($this->getAuthUser() == 'student') {
            $type = 'teacher';
        }

        $conversations = Conversation::with(['latest', 'one_userable', 'two_userable'])
            ->where(function ($query) use ($userId) {
                $query->where('user_one', $userId)
                    ->where('user_one_type', $this->getAuthUser());
            })
            ->orWhere(function ($query) use ($userId) {
                $query->where('user_two', $userId)
                    ->where('user_two_type', $this->getAuthUser());
            })->get();

        return view('messages', [
            'conversations' => $conversations,
            'auth_id' => auth($this->getAuthUser())->id(),
            'auth_type' => $this->getAuthUser(),
            'type' => $type
        ]);
    }

    public function show(Request $request, $id)
    {
        $userId = auth($this->getAuthUser())->id();

        /**
         *  Check the authenticated user
         *  and then query the recipient.
         */
        if ($this->getAuthUser() == 'teacher') {
            $user = Student::where('id', $id)->first();
            $type = 'student';
        }

        if ($this->getAuthUser() == 'administrator') {
            $user = Teacher::where('id', $id)->first();
            $type = 'teacher';
        }

        if ($this->getAuthUser() == 'student') {
            $user = Teacher::where('id', $id)->first();
            $type = 'teacher';
        }

        /**
         *  Check if is has existing conversation
         *  and messages to prevent creating duplicate
         *  conversations for both users.
         */
        $conversation = Conversation::with(['messages'])->where(
            fn ($query) =>
            $query->where('user_one', $userId)
                ->where('user_one_type', $this->getAuthUser())
        )
            ->orWhere(
                fn ($query) =>
                $query->where('user_two', $id)
                    ->where('user_two_type', $type)
            )->orWhere(
                fn ($query) =>
                $query->where('user_one', $id)
                    ->where('user_one_type', $type)
            )->orWhere(
                fn ($query) =>
                $query->where('user_two', $userId)
                    ->where('user_two_type', $userId)
            )->first();

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
            'auth_id' => auth($this->getAuthUser())->id(),
            'auth_type' => $this->getAuthUser()
        ]);
    }

    public function store(Request $request)
    {
        $userId = auth($this->getAuthUser())->id();

        $request->validate([
            'user_two' => 'required',
            'message' => 'required|max:200',
            'user_two_type' => 'required',
        ]);

        //Generate type for morphTo
        if ($this->getAuthUser() === 'student') {
            $morphClass = Student::class;
        }

        if ($this->getAuthUser() === 'teacher') {
            $morphClass = Teacher::class;
        }

        if ($this->getAuthUser() === 'administrator') {
            $morphClass = Administrator::class;
        }

        if ($request->user_two_type === 'student') {
            $morphClassTwo = Student::class;
        }

        if ($request->user_two_type === 'teacher') {
            $morphClassTwo = Teacher::class;
        }

        if ($request->user_two_type === 'administrator') {
            $morphClassTwo = Administrator::class;
        }

        //Store the conversation
        if (!$request->conversation_id) {
            $conversation = Conversation::create([
                'user_one' => auth($this->getAuthUser())->id(),
                'user_one_type' => $this->getAuthUser(),
                'one_userable_id' => auth($this->getAuthUser())->id(),
                'one_userable_type' => $morphClass,
                'two_userable_id' => $request->user_two,
                'two_userable_type' => $morphClassTwo,
                'user_two' => $request->user_two,
                'user_two_type' => $request->user_two_type,
            ]);
        }

        //Store the message
        Message::create([
            'conversation_id' => $request->conversation_id ?? $conversation->id,
            'message' => $request->message,
            'sender_id' => auth($this->getAuthUser())->id(),
            'sender_type' => $this->getAuthUser()
        ]);

        //Emit the event with the conversation id and user type of the recepient user
        ChatEvent::dispatch($request->message, $request->conversation_id ?? $conversation->id, $request->user_two_type);

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
