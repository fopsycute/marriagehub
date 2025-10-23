$(document).ready(function() {
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    const token = urlParams.get('token');
    const action = urlParams.get('action');

    const siteUrl = $('#siteurl').val(); // hidden input holding your base URL
    const ajaxUrl = siteUrl + "script/register.php"; // backend handler path

    // Only trigger verification if all parameters exist
    if (id && token && action === 'verifyemail') {
        $.ajax({
            url: ajaxUrl,
            type: "GET",
            data: { id: id, token: token, action: action },
            dataType: "json",
            beforeSend: function() {
                // Optional loader
                console.log("Verifying your email...");
            },
            success: function(res) {
                alert(res.messages);
                if (res.status === 'success') {
                    // Redirect after success
                    window.location.href = siteUrl + "login.php";
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", status, error);
                alert("An error occurred during verification. Please try again.");
            }
        });
    } else {
        alert("Invalid verification link.");
    }
});
