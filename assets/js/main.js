document.addEventListener('DOMContentLoaded', function () {
    var alerts = document.querySelectorAll('.alert.alert-dismissible:not(.msg-persistent)');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            var closeBtn = alert.querySelector('.btn-close');
            if (closeBtn) closeBtn.click();
        }, 6000);
    });
});