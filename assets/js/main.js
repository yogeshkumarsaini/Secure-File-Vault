// Auto-dismiss flash alerts after a few seconds.
document.addEventListener('DOMContentLoaded', function () {
    var alerts = document.querySelectorAll('.alert');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(function () { alert.remove(); }, 500);
        }, 6000);
    });

    // Simple client-side upload size warning (server still enforces the real limit).
    var fileInput = document.querySelector('input[type="file"][name="uploaded_file"]');
    if (fileInput) {
        fileInput.addEventListener('change', function () {
            var maxBytes = 25 * 1024 * 1024; // keep in sync with MAX_UPLOAD_SIZE
            if (fileInput.files[0] && fileInput.files[0].size > maxBytes) {
                alert('This file is larger than the allowed upload size.');
                fileInput.value = '';
            }
        });
    }
});
