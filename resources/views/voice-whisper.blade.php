<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>OpenAI Whisper API</title>
    <link rel="stylesheet" href="{{ asset('assets/custom.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,700,300">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/material-design-iconic-font/2.1.2/css/material-design-iconic-font.min.css">
    <link rel="stylesheet" href="https://rawgit.com/marvelapp/devices.css/master/assets/devices.min.css">
</head>

<body>
    <div class="page">
        <div class="screen">
            <div class="screen-container">
                <div class="chat">
                    <div class="chat-container">
                        <div class="user-bar" style="background: #e11c1c">
                            <div class="back">
                                <i class="zmdi zmdi-arrow-left"></i>
                            </div>
                            <div class="avatar">
                                <img src="{{ asset('assets/ai-human.png') }}" alt="Avatar">
                            </div>
                            <div class="name" style="width: 30%">
                                <span>OpenAI Whisper API</span>
                                <span class="status">online</span>
                            </div>
                            <div class="actions more">
                                <i class="zmdi zmdi-more-vert"></i>
                            </div>
                            <div class="actions">
                                <i class="zmdi zmdi-phone"></i>
                            </div>
                        </div>
                        <div class="conversation">
                            <div class="conversation-container"></div>
                            <form class="conversation-compose" enctype="multipart/form-data">
                                <input style="margin-left: 10px; padding-left: 15px; border-radius: 8px" class="input-msg" type="audio" name="input" placeholder="Talk with AI ..." readonly
                                    autofocus></input>

                                <button class="send" id="send-voice">
                                    <div class="circle" style="background: #e11c1c">
                                        <i class="zmdi zmdi-mic" style="margin-left: 0" id="voice"></i>
                                    </div>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.13.0/moment.min.js"></script>
    <script src="{{ asset('assets/custom-whisper.js') }}"></script>
</body>

</html>
