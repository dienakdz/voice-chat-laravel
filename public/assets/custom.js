$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$(document).ready(function () {
    /* Time */
    $('.message .time').each(function () {
        $(this).text(moment().format('h:mm A'));
    });

    /* Message */
    var $form = $('.conversation-compose');
    var $conversation = $('.conversation-container');
    $form.find('input[name="input"]').on('keydown', function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            $form.submit();
        }
    });
    function trimContext(chatContext, maxMessages = 16) {
        // get 16 message latest
        return chatContext.slice(-maxMessages);
    }

    var chatContext = [];


    $form.on('submit', function (e) {
        e.preventDefault();

        var $input = $(this).find('input[name="input"]');
        var text = $input.val();

        if (text) {
            var $message_user = buildMessage(text, "user");
            $conversation.append($message_user);

            chatContext.push({ role: 'user', content: text });
            let thinking = buildMessage("Đang nghĩ...", "ai");
            $conversation.append(thinking);

            $.ajax({
                url: "/voice/text-chat",
                method: "POST",
                contentType: "application/json",
                data: JSON.stringify({
                    text: text,
                    context: trimContext(chatContext, 16)
                }),
                success: function (res) {
                    thinking.remove();
                    var aiText = res.ai_text;
                    var $message_ai = buildMessage(aiText, "ai");
                    $conversation.append($message_ai);

                    chatContext.push({ role: 'assistant', content: aiText });

                    // **Text-to-speech**
                    speakText(aiText);
                },
                error: function () {
                    thinking.remove();
                    speakText("Lỗi AI");
                    $conversation.append(buildMessage("Lỗi AI", "ai"));
                }
            });
            $input.val('');
            $conversation.scrollTop($conversation[0].scrollHeight);
        }

    });

    function buildMessage(text, type = 'user') {
        if (type == "user") {
            var $element = $('<div class="message sent"></div>');
        } else {
            var $element = $('<div class="message received"></div>');
        }
        $element.html(
            text.replace(/\n/g, '<br>') +
            '<span class="metadata">' +
            '<span class="time">' + moment().format('h:mm A') + '</span>' +
            '</span>'
        );
        return $element;
    }

    // Check browser Speech Recognition
    if ('SpeechRecognition' in window || 'webkitSpeechRecognition' in window) {
        var recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
        recognition.lang = 'vi-VN'; //en-US  for English
        recognition.continuous = false;
        recognition.interimResults = true;

        var isRecognizing = false;

        $('.photo .zmdi').on('click', function () {
            if (isRecognizing) {
                recognition.stop();
                $(this).removeClass('zmdi-mic-off').addClass('zmdi-mic');
            } else {
                recognition.start();
                $(this).removeClass('zmdi-mic').addClass('zmdi-mic-off');
            }
        });

        recognition.onstart = function () {
            console.log('Speech recognition started');
            isRecognizing = true;
        };

        recognition.onresult = function (event) {
            var transcript = event.results[0][0].transcript;
            $('input[name="input"]').val(transcript);
        };

        recognition.onerror = function (event) {
            console.log('Speech recognition error', event.error);
        };

        recognition.onend = function () {
            console.log('Speech recognition ended');
            $('.photo .zmdi').removeClass('zmdi-mic-off').addClass('zmdi-mic');
            isRecognizing = false;
            $form.submit();
        };

    } else {
        console.log('Speech recognition not supported in this browser.');
    }


    function speakText(text) {

        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel();

            let utterance = new SpeechSynthesisUtterance(text);

            utterance.lang = "en-US"; // 
            utterance.rate = 1;       // speak speed (0.1 ~ 10)
            utterance.pitch = 1;      // voice pitch (0 ~ 2)

            window.speechSynthesis.speak(utterance);
        } else {
            console.log('Speech Synthesis không được hỗ trợ trên trình duyệt này.');
        }
    }

});