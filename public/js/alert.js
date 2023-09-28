document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        var errorMessages = document.querySelectorAll('.msg_error');
        for (var i = 0; i < errorMessages.length; i++) {
            errorMessages[i].style.display = 'none';
        }
    }, 3000);

    setTimeout(function() {
        var successMessages = document.querySelectorAll('.msg_success');
        for (var i = 0; i < successMessages.length; i++) {
            successMessages[i].style.display = 'none';
        }
    }, 3000);
});

 