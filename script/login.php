
<?php
include "connect.php";

function loginEndpoint($postData) {
    global $con, $siteprefix;

    $email = trim($postData['email']);
    $password = trim($postData['password']);

    // Fetch user
    $stmt = $con->prepare("SELECT * FROM {$siteprefix}users WHERE email = ? LIMIT 1");
    if (!$stmt) {
        return ['success' => false, 'error' => "Error preparing statement: " . $con->error];
    }

    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {
        return ['success' => false, 'error' => "Error executing query: " . $stmt->error];
    }

    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        return ['success' => false, 'error' => "Invalid email or password."];
    }

    $row = $result->fetch_assoc();

    // Verify password
    if (!verifyPassword($password, $row['password'])) {
        return ['success' => false, 'error' => "Invalid email or password."];
    }

    $userType = strtolower(trim($row['user_type']));

    // Check status for non-admins
    if ($userType !== 'admin' && $row['status'] !== 'active') {
        return [
            'success' => true,
            'user_type' => $userType,
            'status' => 'inactive',
            'message' => "Your account is not yet verified. Please check your email for the verification link."
        ];
    }

    // ✅ Create user-specific cookie
    $userId = $row['id'];
    $cookieName = match ($userType) {
        'admin' => 'admin_auth',
        'therapist' => 'vendor_auth',
        default => 'user_auth',
    };

    // Clear other cookies (avoid conflicts)
    setcookie('admin_auth', '', time() - 3600, '/');
    setcookie('vendor_auth', '', time() - 3600, '/');
    setcookie('user_auth', '', time() - 3600, '/');

    setcookie($cookieName, $userId, time() + (86400 * 360), "/");

    // Redirect map
    $redirectMap = [
        'admin' => "admin/",
        'therapist' => "therapist/",
        'buyer' => "dashboard.php"
    ];

    $redirectUrl = $redirectMap[$userType] ?? "dashboard.php";

    return [
        'success' => true,
        'user_type' => $userType,
        'status' => 'active',
        'name' => $row['first_name'] . ' ' . $row['last_name'],
        'message' => "Login successful. Redirecting to your dashboard...",
        'redirect' => $redirectUrl
    ];
}



function logoutEndpoint() {
    // Clear authentication cookies
    $cookies = ['admin_auth', 'vendor_auth', 'user_auth', 'authentication'];
    foreach ($cookies as $cookie) {
        setcookie($cookie, '', time() - 3600, '/');
        unset($_COOKIE[$cookie]);
    }

    // Response
    return [
        "success" => true,
        "message" => "Logout successful.",
        "status_code" => 200
    ];
}

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $response = logoutEndpoint();
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] == 'login') {
        $response = loginEndpoint($_POST);
    }


    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}



?>
