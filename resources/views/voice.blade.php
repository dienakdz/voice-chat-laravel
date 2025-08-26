<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat with AI</title>
    <link rel="stylesheet" href="{{ asset('assets/custom.css') }}">
</head>

<body>
    <h1>Chat Text Demo (jQuery)</h1>

    <div id="chatBox"></div>

    <input type="text" id="messageInput" placeholder="Nhập tin nhắn..." />
    <button id="sendBtn">Gửi</button>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('assets/custom.js') }}"></script>
</body>

</html>
