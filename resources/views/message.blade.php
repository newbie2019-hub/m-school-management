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
        .chat {
            height: 100vh;
            background-color: rgb(243, 243, 243);
            overflow-y: auto;
            display: flex;
            justify-content: flex-end;
            padding-left: 12px;
            padding-right: 12px;
            padding-bottom: 80px;
            padding-top: 10px;
            flex-direction: column;
            width: 100%;
            max-width: 600px;
            row-gap: 10px;
            position: relative;
            max-height: 100vh;
        }

        .chat-header {
            position: absolute;
            top: 0;
            width: 100%;
            max-width: 600px;
            left: 0;
            background-color: white;
            padding-left: 10px;
            padding-right: 10px;
            box-shadow: 0px 0px 5px 4px rgba(71, 71, 71, 0.067);
            z-index: 5;
            display: flex;
            align-items: center
        }

        .chat-message {
            padding: 0px 16px;
            border-radius: 6px;
            max-width: 350px;
            word-wrap: break-word;
        }


        .from {
            background-color: rgb(59, 141, 224);
            color: rgb(255, 255, 255);
            justify-self: end;
            align-self: flex-end;
            position: relative;

        }

        .to {
            background-color: rgb(235, 235, 235);
            align-self: flex-start;
            position: relative;
        }

        .to::before {
            content: "";
            position: absolute;
            background-color: rgb(235, 235, 235);
            width: 20px;
            height: 20px;
            top: 0px;
            left: -10px;
            clip-path: polygon(60% 51%, 0% 100%, 100% 100%);
        }

        .from::before {
            content: "";
            position: absolute;
            background-color: rgb(59, 141, 224);
            width: 20px;
            height: 20px;
            bottom: 0px;
            right: -6px;
            clip-path: polygon(60% 51%, 0% 100%, 100% 100%);
        }

        .message-input {
            position: absolute;
            bottom: 0px;
            background-color: white;
            display: flex;
            justify-content: center;
            background-color: white;
            padding: 10px;
            width: 100%;
            max-width: 600px;
            white-space: nowrap
        }

        .message-input input.message {
            padding: 8px 10px;
            outline: none;
            border: 1px solid grey;
            border-top-left-radius: 6px;
            border-bottom-left-radius: 6px;
            font-size: .9rem;
            width: 100%;
        }

        .message-input .send-btn {
            background-color: rgb(59, 141, 224);
            border-top-right-radius: 6px;
            border-bottom-right-radius: 6px;
            text-transform: uppercase;
            font-size: .9rem;
            color: white;
            outline: none;
            border: 0;
            padding: 6px 14px;
        }
    </style>
</head>

<body>
    <div class="chat">
        <div class="chat-header">
            <a href="{{ url()->previous() }}" id="return-btn">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" style="width: 16px; height: 16px; margin-right: 8px; cursor: pointer">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19.5 12h-15m0 0l6.75 6.75M4.5 12l6.75-6.75" />
                </svg>
            </a>

            <p>
                {{ $user->firstname }}
                {{ $user->lastname }}
                {{-- {{ $messages->student->firstname }} --}}
            <p>
        </div>
        @if ($conversation)
            @foreach ($conversation->messages as $message)
                <div
                    class="chat-message {{ $auth_id === $message->sender_id && $message->sender_type === $auth_type ? 'from' : 'to' }}">
                    <p>{{ $message->message }}</p>
                </div>
            @endforeach
        @else
            <p style="color: rgb(192, 192, 192); text-align: center;">Type Hi and press enter.</p>
        @endif
    </div>
    <div class="message-input">
        <input type="hidden" class="user_two" value="{{ $user_two }}" name="user_two" />
        <input type="hidden" class="user_two_type" value="{{ $user_two_type }}" name="user_two_type" />
        <input type="hidden" class="auth_id" value="{{ $auth_id }}" name="auth_id" />
        <input type="hidden" class="auth_type" value="{{ $auth_type }}" name="auth_type" />
        <input type="hidden" class="conversation_id" value="{{ $conversation?->id }}" name="conversation_id" />
        <input type="text" class="message" name="message" />
        <button id="send" class="send-btn">Send</button>
    </div>
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        $(function() {
            const Http = window.axios;
            const Echo = window.Echo;
            const conversation_id = $('.conversation_id');
            const user_two_type = $('.user_two_type');
            const user_two = $('.user_two');
            const message = $('.message');
            const auth_id = $('.auth_id');
            const auth_type = $('.auth_type');

            $('.message').on('keypress', function(e) {
                if (e.which == 13) {
                    Http.post("{{ url('messages') }}", {
                        'user_two': user_two.val(),
                        'user_two_type': user_two_type.val(),
                        'message': message.val(),
                        'conversation_id': conversation_id.val(),
                    }).then(() => {
                        $('.chat').append(
                            `<div class='chat-message from'> <p>${message.val()}</p></div>`);
                        message.val('')
                    })
                }
            })

            $("button").click(function() {
                Http.post("{{ url('messages') }}", {
                    'user_two': user_two.val(),
                    'user_two_type': user_two_type.val(),
                    'message': message.val(),
                    'conversation_id': conversation_id.val(),
                }).then(() => {
                    $('.chat').append(
                        `<div class='chat-message from'> <p>${message.val()}</p></div>`);
                    message.val('')
                })
            })

            let channel = Echo.channel(`channel-chat-${conversation_id.val()}-${auth_type.val()}`)

            // console.log(`channel-chat-${conversation_id.val()}-${auth_type.val()}`);
            channel.listen('.channel-chat', function(data) {
                $('.chat').append(`<div class='chat-message to'> <p>${data.message}</p></div>`);
            })

        })
    </script>
</body>

</html>
