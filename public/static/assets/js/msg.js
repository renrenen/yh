function tmsg(msg) {
        if ($("div").is("#toastMessage")) {
            $("#toastMessage").remove();
        }
        var msgDiv = '<div id="toastMessage" class="toast-message">' +
            '<span>' + msg + '</span>' +
            '</div>';
        $("body").append(msgDiv);
        var screenHeight = window.innerHeight;
        var toastMessage = $("#toastMessage");
        var toastHeight = toastMessage.height();
        var top = (screenHeight / 2) - (toastHeight / 2) + "px";
        toastMessage.css("top", top);
        setTimeout(function () {
            $("#toastMessage").remove();
        }, 1500);
    }