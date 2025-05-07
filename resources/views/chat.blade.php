<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laravel Broadcasting Chat</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.11.3/echo.iife.js"></script>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        #messages {
            list-style: none;
            padding-left: 0;
        }
        #messages li {
            padding: 5px;
            background-color: #f0f0f0;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

<h1>Laravel Broadcasting Chat</h1>

<input type="text" id="username" placeholder="Your name"><br><br>
<input type="text" id="message" placeholder="Type message">
<button id="sendBtn">Send</button>

<ul id="messages"></ul>

<script>
    console.log('JS se učitao');

    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: '{{ env('PUSHER_APP_KEY') }}',
        cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
        forceTLS: true
    });

    console.log('Echo pokrenut.');

    window.Echo.channel('chat')
        .listen('MessageSent', (e) => {
            console.log('Poruka primljena:', e);
            const messageElement = document.createElement('li');
            messageElement.innerText = `${e.username}: ${e.message}`;
            document.getElementById('messages').appendChild(messageElement);
        });;

    function sendMessage() {
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
    }

    document.getElementById('sendBtn').addEventListener('click', sendMessage);
</script>

</body>
</html>

