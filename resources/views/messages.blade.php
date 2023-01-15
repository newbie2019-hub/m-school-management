<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>iLearning - Mayorga National High School</title>

    {{-- fonts --}}
    <link rel="preload" href="{{ asset('font/roboto-v30-latin-regular.woff2') }}" as="font" type="font/woff2"
        crossorigin>
    <link rel="preload" href="{{ asset('font/material-icons-outlined.woff2') }}" as="font" type="font/woff2"
        crossorigin>

    {{-- app style --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    {{-- index style --}}
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <style>
        .chat-avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgb(72, 131, 220);
            border-radius: 50%;
            padding: 4px;
            height: 45px;
            width: 45px;
            color: white;
            cursor: pointer;
            transition: all 250ms ease-in-out;
            color: white !important;
        }

        .message {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .messages-container {
            max-height: 400px;
            overflow-y: auto;
            padding: 16px 30px;
            box-shadow: 0px 0px 5px 1px rgba(0, 0, 0, 0.156);
            border-radius: 6px;
            width: 480px;
            background: white;

        }

        .chat-container {
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: 6px;
            padding: 4px 8px;
            transition: all 250ms ease-in-out;
            cursor: pointer;
        }

        .chat-container:hover {
            background: rgb(228, 228, 228);
            border-radius: 6px;
            padding: 4px 8px;

        }
    </style>
</head>

<body class="message">
    <div class="messages-container" style="line-height: 10px;">
        <p style="font-size: 1.2rem; font-weight: 600; margin-bottom: 0">Messages</p>
        <p style="color:gray; font-size: .9rem">Shown below are your conversations with other users</p>
        @forelse ($conversations as $messages)
            <a
                href="{{ route('messages.show', [
                    'message' => $messages->user_one == $auth_id && $messages->user_one_type == $auth_type ? $messages->user_two : $messages->user_one,
                    'recipient_type' => $messages->user_one_type == $auth_type ? $messages->user_two_type : $messages->user_one_type,
                    'auth_type' => $auth_type
                    ]) }}">
                <div class="chat-container">
                    <div class="chat-avatar">
                        <img src="https://api.dicebear.com/5.x/identicon/svg?scale=70&rowColor=ffffff&seed={{ $messages->user_one == $auth_id && $messages->user_one_type == $auth_type
                            ? $messages->two_userable->id . rand(1, 100)
                            : $messages->one_userable->id . rand(1, 100) }}"
                            alt="">
                        {{-- <p>{{
                        $messages->user_one == $auth_id && $messages->user_one_type == $auth_type ?
                        $messages->two_userable->firstname[0] . $messages->two_userable->lastname[0]?  :
                        $messages->one_userable->firstname[0]? . $messages->one_userable->lastname[0]?
                    }}</p> --}}
                    </div>
                    <div style="">
                        <p style="line-height: 0px; font-size: .9rem; text-transform: uppercase; font-weight: 600;">
                            {{ $messages->user_one == $auth_id && $messages->user_one_type == $auth_type ? ($messages->two_userable->firstname ?? 'Administrator') . ' ' . $messages->two_userable->lastname : ($messages->one_userable->firstname ?? 'Administrator') . ' ' . $messages->one_userable->lastname }}
                        </p>
                        <p
                            style="line-height: 10px; color: gray; width: 280px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap">
                            {{ $messages->latest->message }}</p>
                    </div>
                </div>
            </a>
        @empty
            <p style="text-align: center; color: gray;">No chat messages found.</p>
        @endforelse


    </div>
</body>

</html>
