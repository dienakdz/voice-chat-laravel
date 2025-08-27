$(document).ready(function () {
    var $conversation = $('.conversation-container');
    var chatContext = [];
    var mediaRecorder;
    var audioChunks = [];

    function buildMessage(text, type = 'ai') {
        var $element = $('<div></div>');
        $element.addClass(type === 'ai' ? 'message received' : 'message sent');
        $element.html(
            text.replace(/\n/g, '<br>') +
            '<span class="metadata">' +
            '<span class="time">' + moment().format('h:mm A') + '</span>' +
            '</span>'
        );
        return $element;
    }

    function speakText(text) {
        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel();
            let utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = "vi-VN";
            utterance.rate = 1;
            utterance.pitch = 1;
            window.speechSynthesis.speak(utterance);
        }
    }

    $('#send-voice').on('click',async function(e) {
        e.preventDefault();
        var $icon = $("#send-voice #voice");

        if ($icon.hasClass('recording')) {
            // Dừng ghi âm
            mediaRecorder.stop();
            $icon.removeClass('recording zmdi-mic-off').addClass('zmdi-mic');
        } else {
            // Bắt đầu ghi âm
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            mediaRecorder = new MediaRecorder(stream);

            mediaRecorder.ondataavailable = e => audioChunks.push(e.data);

            mediaRecorder.onstop = async () => {
                const audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
                audioChunks = [];

                // Preview (tùy chọn)
                const audioUrl = URL.createObjectURL(audioBlob);
                const audioElem = document.createElement('audio');
                audioElem.src = audioUrl;
                audioElem.controls = true;
                $conversation.append(audioElem);

                // Upload audio → backend
                const token = $('meta[name="csrf-token"]').attr('content');
                const formData = new FormData();
                formData.append('audio', audioBlob, 'audio.wav');
                formData.append('context', JSON.stringify(chatContext.slice(-16)));
                formData.append('_token', token);

                const res = await fetch('/voice-whisper/voice-chat', { method: 'POST', body: formData });
                const data = await res.json();

                // Hiển thị AI text + TTS
                var $message_ai = buildMessage(data.ai_text, 'ai');
                $conversation.append($message_ai);
                chatContext.push({ role: 'assistant', content: data.ai_text });
                speakText(data.ai_text);
                $conversation.scrollTop($conversation[0].scrollHeight);
            };

            mediaRecorder.start();
            $icon.addClass('recording zmdi-mic-off').removeClass('zmdi-mic');
        }
    });
});
