@props(['authType' => ''])

<a href="{{ route('messages.index', ['auth_type' => $authType]) }}"
    style="position: fixed; right: 50px; bottom: 40px;
    display: flex; align-items:center; justify-content: center;
    background-color: rgb(74, 116, 222); height: 60px; width: 60px;
    border-radius: 50%;">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 30 30" stroke-width="1.5" stroke="white"
        class="w-6 h-6" style="height: 40px; width: 40px; margin-left: 8px; margin-top: 4px">
        <path stroke-linecap="round" stroke-linejoin="round"
            d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
    </svg>

</a>
