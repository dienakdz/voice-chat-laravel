$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
}); 
$(document).ready(function () {
    $("#sendBtn").click(function () {
        let text = $("#messageInput").val();
        if (!text) return;

        // Hiển thị message user
        $("#chatBox").append(`<div class="msg user"><b>Bạn:</b> ${text}</div><br>`);
        $("#messageInput").val("");

        // Thêm tạm "Đang nghĩ..."
        let thinking = $('<div class="msg ai"><i>Đang nghĩ...</i></div><br>');
        $("#chatBox").append(thinking);
        $("#chatBox").scrollTop($("#chatBox")[0].scrollHeight);

        // Gửi request lên API
        $.ajax({
            url: "voice/text-chat",
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify({ text: text }),
            success: function (res) {
                thinking.remove(); // xóa "Đang nghĩ..."
let formatted = res.answer.replace(/\n/g, "<br>");
$("#chatBox").append(`<div class="msg ai"><b>AI:</b> ${formatted}</div>`);
                $("#chatBox").scrollTop($("#chatBox")[0].scrollHeight);
            },
            error: function () {
                thinking.remove();
                $("#chatBox").append(`<div class="msg ai"><b>AI:</b> Lỗi kết nối server</div><br>`);
            }
        });
    });
});