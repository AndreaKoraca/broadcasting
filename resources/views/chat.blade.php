<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laravel Broadcasting Chat</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.11.3/echo.iife.js"></script>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        #messages, #notifications { list-style: none; padding-left: 0; }
        li { padding: 5px; background-color: #f0f0f0; margin-bottom: 5px; }
    </style>
</head>
<body>

<h1>Broadcasting Chat</h1>

<input type="text" id="username" placeholder="Your name"><br><br>
<input type="text" id="message" placeholder="Type message">
<button id="sendBtn">Send</button>

<h2>Poruke:</h2>
<ul id="messages"></ul>

<h2>Notifikacije:</h2>
<ul id="notifications">
    @foreach(\App\Models\User::find(1)?->notifications ?? [] as $notification)
        <li>{{ $notification->data['username'] }}: {{ $notification->data['message'] }}</li>
    @endforeach
</ul>

<script>
    window.Pusher = Pusher;
    window.Pusher.logToConsole = true;

    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: "{{ env('PUSHER_APP_KEY') }}",
        cluster: "{{ env('PUSHER_APP_CLUSTER') }}",
        forceTLS: true
    });

    window.Echo.channel('chat')
        .listen('MessageSent', (e) => {
            const messageElement = document.createElement('li');
            messageElement.innerText = `${e.username}: ${e.message}`;
            document.getElementById('messages').appendChild(messageElement);
        });

    document.getElementById('sendBtn').addEventListener('click', function () {
        const username = document.getElementById('username').value.trim();
        const message = document.getElementById('message').value.trim();

        if (username === '' || message === '') {
            alert('Unesite ime i poruku!');
            return;
        }

        fetch('/send-message', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ username, message })
        });

        document.getElementById('message').value = '';
    });

    function loadNotifications() {
    fetch('/notifications')
        .then(res => res.json())
        .then(data => {
            const list = document.getElementById('notifications');
            list.innerHTML = '';
            data.forEach(notification => {
                const li = document.createElement('li');
                li.textContent = `${notification.data.username}: ${notification.data.message}`;
                list.appendChild(li);
            });
        });
    }


    loadNotifications();


    setInterval(loadNotifications, 5000);

</script>

</body>
</html>
