
<?php
include "script/connect.php";


// --- Logout API URL ---
$url = $siteurl . "script/login?action=logout";

// --- Call the logout endpoint ---
$responseData = curl_get_contents($url);

if ($responseData !== false) {
    $data = json_decode($responseData, true);

    // --- Clear authentication cookies ---
    $cookies = ['admin_auth', 'vendor_auth', 'user_auth', 'authentication'];
    foreach ($cookies as $cookie) {
        setcookie($cookie, '', time() - 3600, '/');
        unset($_COOKIE[$cookie]);
    }

    // --- Redirect user ---
    header("Location: " . $siteurl . "login.php");
    exit();
} else {
    echo "Request to logout endpoint failed.";
}
?>
