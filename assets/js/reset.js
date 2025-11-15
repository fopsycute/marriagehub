$(document).ready(function() {
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    const token = urlParams.get('token');
    const action = urlParams.get('action');

    const siteUrl = $('#siteurl').val(); 
    const ajaxUrl = siteUrl + "script/register.php"; 

    if (id && token && action === 'verifyemail') {
        $.ajax({
            url: ajaxUrl,
            type: "GET",
            data: { id: id, token: token, action: action },
            dataType: "json",
            beforeSend: function() {
                console.log("Verifying your email...");
            },
            success: function(res) {
                alert(res.messages);

                if (res.status === 'success') {
                    // Use the redirect returned from PHP
                    if (res.redirect) {
                        window.location.href = siteUrl + res.redirect;
                    }
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
