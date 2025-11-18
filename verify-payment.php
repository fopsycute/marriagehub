<?php
include "script/connect.php";


function bookingPaymentSuccess($con, $siteprefix, $siteurl, $sitecurrency, $escrowfee, $siteName = '', $siteMail = '') {
    if (!isset($_GET['reference']) || !isset($_GET['booking_id'])) {
        die("Invalid payment request.");
    }

    $reference   = mysqli_real_escape_string($con, $_GET['reference']);
    $booking_id  = mysqli_real_escape_string($con, $_GET['booking_id']);
    $currentdatetime = date('Y-m-d H:i:s');
    $date = $currentdatetime;

    // --- Fetch booking details ---
    $bookingQuery = mysqli_query($con, "
        SELECT id, client_name, client_email, therapist_id, amount
        FROM {$siteprefix}bookings
        WHERE reference='$booking_id' LIMIT 1
    ");
    if (!$bookingQuery || mysqli_num_rows($bookingQuery) === 0) {
        die("Booking not found.");
    }
    $booking = mysqli_fetch_assoc($bookingQuery);
    $client_name   = $booking['client_name'];
    $client_email  = $booking['client_email'];
    $therapist_id  = $booking['therapist_id'];
    $amount        = $booking['amount']; // use amount as the booking price

    // --- Fetch therapist info ---
    $therapistQuery = mysqli_query($con, "
        SELECT id, first_name, email, wallet, user_type
        FROM {$siteprefix}users
        WHERE id='$therapist_id' LIMIT 1
    ");
    if (!$therapistQuery || mysqli_num_rows($therapistQuery) === 0) {
        die("Therapist not found.");
    }
    $therapist = mysqli_fetch_assoc($therapistQuery);
    $therapist_name  = $therapist['first_name'];
    $therapist_email = $therapist['email'];
    $therapist_wallet = $therapist['wallet'];
    $user_type       = $therapist['user_type'] ?? 'therapist';

    // --- Compute admin commission using escrowfee ---
    $admin_commission = round($amount * ($escrowfee / 100), 2);
    $therapist_amount = $amount - $admin_commission;

    // --- Update booking as paid + confirmed ---
    mysqli_query($con, "
        UPDATE {$siteprefix}bookings
        SET payment_status='paid', booking_status='completed'
        WHERE reference='$booking_id'
    ");

    // --- Handle admin vs therapist ---
    if ($user_type === 'admin') {
        mysqli_query($con, "
            INSERT INTO {$siteprefix}profits (amount, booking_id, type, date)
            VALUES ('$amount', '$reference', 'Booking (Admin Therapist)', '$date')
        ");
        insertAdminAlert($con, "Full payment of {$sitecurrency}{$amount} added to profits (Admin therapist: {$therapist_name}).", "profits.php", $date, "profits", 0);

        $adminSubject = "New Booking Payment Received";
        $adminMessage = "
            <p>You received a payment of <strong>{$sitecurrency}{$amount}</strong> for therapist <strong>{$therapist_name}</strong>.</p>
            <p>The full amount has been added to profits.</p>
            <p>— {$siteName}</p>
        ";
        sendEmail($siteMail, $siteName, $siteMail, "Admin", $adminMessage, $adminSubject);

    } else {
        // --- Credit therapist wallet after admin commission ---
        if ($admin_commission > 0) {
            mysqli_query($con, "
                INSERT INTO {$siteprefix}profits (amount, booking_id, type, date)
                VALUES ('$admin_commission', '$reference', 'Booking Commission', '$date')
            ");
            insertAdminAlert($con, "Admin earned {$sitecurrency}{$admin_commission} from booking ({$therapist_name})", "profits.php", $date, "profits", 0);

            $adminSubject = "Commission Earned from Booking";
            $adminMessage = "
                <p>You earned a commission of <strong>{$sitecurrency}{$admin_commission}</strong> from a therapist booking.</p>
                <p>Therapist: <strong>{$therapist_name}</strong></p>
                <p>— {$siteName}</p>
            ";
            sendEmail($siteMail, $siteName, $siteMail, "Admin", $adminMessage, $adminSubject);
        }

        mysqli_query($con, "
            UPDATE {$siteprefix}users
            SET wallet = wallet + $therapist_amount
            WHERE id='$therapist_id'
        ");
        insertWallet($con, $therapist_id, $therapist_amount, "credit", "Payment from Booking ({$client_name})", $date);
        insertAlert($con, $therapist_id, "You have received {$sitecurrency}{$therapist_amount} from Booking ({$client_name})", $date, 0);

        $emailSubject = "New Booking Payment Received";
        $emailMessage = "
            <p>You have received <strong>{$sitecurrency}{$therapist_amount}</strong> for a booking from <strong>{$client_name}</strong>.</p>
            <p>An admin commission of <strong>{$sitecurrency}{$admin_commission}</strong> has been deducted.</p>
        ";
        sendEmail($therapist_email, $siteName, $siteMail, $therapist_name, $emailMessage, $emailSubject);
    }

    // --- Email client ---
    $clientSubject = "Booking Payment Successful";
    $clientMessage = "
  
        <p>Your payment of <strong>{$sitecurrency}{$amount}</strong> for your booking has been successfully processed.</p>
        <p>Your therapist, <strong>{$therapist_name}</strong>, has been notified.</p> ";
    sendEmail($client_email, $siteName, $siteMail, $client_name, $clientMessage, $clientSubject);

    // --- Redirect client ---
    echo "<script>
        alert('Payment successful! Your booking has been confirmed.');
        window.location.href='{$siteurl}';
    </script>";
    exit;
}


function paymentsuccess($con, $siteprefix, $siteurl, $sitecurrency, $siteName = '', $siteMail = '') {
    if (!isset($_GET['reference']) || !isset($_GET['group_id']) || !isset($_GET['user_id'])) {
        die("Invalid payment request.");
    }

    $reference = mysqli_real_escape_string($con, $_GET['reference']);
    $group_id = intval($_GET['group_id']);
    $group_name = mysqli_real_escape_string($con, $_GET['group_name'] ?? '');
    $duration = mysqli_real_escape_string($con, $_GET['duration'] ?? '');
    $user_id = intval($_GET['user_id']);
    $amount = floatval($_GET['amount'] ?? 0);
    $currentdatetime = date('Y-m-d H:i:s');
    $date = $currentdatetime;

    if ($group_id <= 0 || $user_id <= 0 || $amount <= 0) {
        die("Invalid payment details received.");
    }

    switch (strtolower(trim($duration))) {
        case '1 month':  $addDuration = '+1 month'; break;
        case '3 months': $addDuration = '+3 months'; break;
        case '6 months': $addDuration = '+6 months'; break;
        case '12 months': $addDuration = '+12 months'; break;
        default: $addDuration = '+1 month'; break;
    }

    // --- Check if user already exists in group ---
    $check = mysqli_query($con, "SELECT expiry_date FROM {$siteprefix}group_members WHERE group_id='$group_id' AND user_id='$user_id' LIMIT 1");

    if (mysqli_num_rows($check) == 0) {
        $expiry_date = date('Y-m-d H:i:s', strtotime($addDuration));
        mysqli_query($con, "INSERT INTO {$siteprefix}group_members (group_id, user_id, role, status, joined_at, expiry_date)
                            VALUES ('$group_id', '$user_id', 'member', 'active', '$currentdatetime', '$expiry_date')");
    } else {
        $row = mysqli_fetch_assoc($check);
        $current_expiry = $row['expiry_date'];

        if (strtotime($current_expiry) > time()) {
            $expiry_date = date('Y-m-d H:i:s', strtotime($addDuration, strtotime($current_expiry)));
        } else {
            $expiry_date = date('Y-m-d H:i:s', strtotime($addDuration));
        }

        // 👇 Set status to pending again until admin approves
        mysqli_query($con, "UPDATE {$siteprefix}group_members 
                            SET expiry_date='$expiry_date', status='pending' 
                            WHERE group_id='$group_id' AND user_id='$user_id'");
    }

    // --- Record payment ---
    $payment_status_text = "success";
    mysqli_query($con, "INSERT INTO {$siteprefix}group_payments (group_id, order_id, user_id, amount, duration, status, date) 
                        VALUES ('$group_id','$reference','$user_id', '$amount', '$duration', '$payment_status_text', '$currentdatetime')");

    // --- Get seller (group owner) + group name ---
    $sellerResult = mysqli_query($con, "
        SELECT g.user_id AS seller_id, g.group_name, u.email AS seller_email, u.first_name AS seller_name 
        FROM {$siteprefix}groups g 
        JOIN {$siteprefix}users u ON g.user_id = u.id 
        WHERE g.id='$group_id' LIMIT 1
    ");

    if ($sellerResult && mysqli_num_rows($sellerResult) > 0) {
        $sellerData = mysqli_fetch_assoc($sellerResult);
        $seller_id = $sellerData['seller_id'];
        $group_name = $sellerData['group_name'];
        $seller_email = $sellerData['seller_email'];
        $seller_name = $sellerData['seller_name'];

           // --- CASE 1: Seller is ADMIN (full payment goes to profits) ---
    if ($seller_type === 'admin') {
        mysqli_query($con, "INSERT INTO {$siteprefix}profits 
            (amount, group_id, order_id, type, date)
            VALUES ('$amount', '$group_id', '$reference', 'Group Subscription (Admin Group)', '$date')");

        $message = "Full payment of {$sitecurrency}{$amount} added to profits (Admin group: {$group_name}).";
        insertadminAlert($con, $message, "profits.php", $date, "profits", 0);

        // ✅ Send email to Admin
        $adminSubject = "New Group Subscription Payment Received";
        $adminMessage = "
            <p>You have received a payment of <strong>{$sitecurrency}{$amount}</strong> for the group <strong>{$group_name}</strong>.</p>
            <p>This full amount has been added to your profits.</p>
            <p>— {$siteName} System</p>
        ";
        sendEmail($siteMail, $siteName, $siteMail, "Admin", $adminMessage, $adminSubject);

    } else {
        $admin_commission = $amount * ($escrowfee / 100);
        // --- CASE 2: Seller is NOT ADMIN (split commission) ---
        if ($admin_commission > 0) {
            mysqli_query($con, "INSERT INTO {$siteprefix}profits 
                (amount, group_id, order_id, type, date)
                VALUES ('$admin_commission', '$group_id', '$reference', 'Group Subscription Commission', '$date')");

            $message = "Admin Commission of {$sitecurrency}{$admin_commission} from Group Subscription ({$group_name})";
            insertadminAlert($con, $message, "profits.php", $date, "profits", 0);

            // ✅ Send email to Admin (commission alert)
            $adminSubject = "Commission Earned from Group Subscription";
            $adminMessage = "
              
                <p>You earned a commission of <strong>{$sitecurrency}{$admin_commission}</strong> from a group subscription.</p>
                <p>Group: <strong>{$group_name}</strong></p>
                <p>— {$siteName} </p>
            ";
            sendEmail($siteMail, $siteName, $siteMail, "Admin", $adminMessage, $adminSubject);
        }

        // --- Credit Seller Wallet ---
        mysqli_query($con, "UPDATE {$siteprefix}users 
            SET wallet = wallet + $seller_amount WHERE id='$seller_id'");
        insertWallet($con, $seller_id, $seller_amount, "credit", "Payment from Group Subscription ($group_name)", $date);
        insertAlert($con, $seller_id, "You have received {$sitecurrency}{$seller_amount} from Group Subscription ($group_name)", $date, 0);

        // --- Send Email to Seller ---
        $emailSubject = "You received a payment for your group ($group_name)";
        $emailMessage = "<p>You have received <strong>{$sitecurrency}{$seller_amount}</strong> from a new subscription to your group <strong>{$group_name}</strong>.</p>
            <p>An admin commission of <strong>{$sitecurrency}{$admin_commission}</strong> has been deducted.</p>
            <p>Thank you for using {$siteName}!</p>";
        sendEmail($seller_email, $siteName, $siteMail, $seller_name, $emailMessage, $emailSubject);
    }
    }

    // --- Redirect to group page (pending approval) ---
    echo "<script>
        alert('Payment successful!You've successfully become a group member.');
        window.location.href='{$siteurl}my-group.php';
    </script>";
    exit;
}

function therapistPaymentSuccess($con, $siteprefix, $siteurl, $sitecurrency, $siteName = '', $siteMail = '') {
    if (!isset($_GET['reference']) || !isset($_GET['booking_id'])) {
        die("Invalid payment request.");
    }

    $reference   = mysqli_real_escape_string($con, $_GET['reference']);
    $booking_id  = intval($_GET['booking_id']);
    $currentdatetime = date('Y-m-d H:i:s');
    $date = $currentdatetime;

    if ($booking_id <= 0) {
        die("Invalid booking ID.");
    }

    // --- Step 1: Fetch booking details ---
    $bookingQuery = mysqli_query($con, "
        SELECT id, client_name, therapist_id, price 
        FROM {$siteprefix}bookings 
        WHERE id='$booking_id' LIMIT 1
    ");
    if (!$bookingQuery || mysqli_num_rows($bookingQuery) === 0) {
        die("Booking not found.");
    }
    $booking = mysqli_fetch_assoc($bookingQuery);
    $client_name  = $booking['client_name'];
    $therapist_id = $booking['therapist_id'];
    $price        = $booking['price'];

    // --- Step 2: Fetch therapist info ---
    $therapistQuery = mysqli_query($con, "
        SELECT id, first_name, email, wallet, user_type 
        FROM {$siteprefix}users 
        WHERE id='$therapist_id' LIMIT 1
    ");
    if (!$therapistQuery || mysqli_num_rows($therapistQuery) === 0) {
        die("Therapist not found.");
    }
    $therapist = mysqli_fetch_assoc($therapistQuery);
    $therapist_name  = $therapist['first_name'];
    $therapist_email = $therapist['email'];
    $wallet          = $therapist['wallet'];
    $user_type       = $therapist['user_type'] ?? 'therapist';

    // --- Step 3: Compute admin commission and therapist amount ---
    $admin_commission_rate = 10; // 10% admin commission
    $admin_commission = round(($admin_commission_rate / 100) * $price, 2);
    $therapist_amount = $price - $admin_commission;

    // --- Step 4: Update booking as paid + confirmed ---
    mysqli_query($con, "
        UPDATE {$siteprefix}bookings 
        SET payment_status='paid', booking_status='confirmed', payment_reference='$reference'
        WHERE id='$booking_id'
    ");

    // --- Step 5: Handle admin and therapist wallet/profits ---
    if ($user_type === 'admin') {
        // Full amount goes to admin profits
        mysqli_query($con, "
            INSERT INTO {$siteprefix}profits (amount, booking_id, type, date)
            VALUES ('$price', '$booking_id', 'Therapist Booking (Admin)', '$date')
        ");
        $message = "Full payment of {$sitecurrency}{$price} added to profits (Admin therapist: {$therapist_name}).";
        insertAdminAlert($con, $message, "profits.php", $date, "profits", 0);

        // Email admin
        $adminSubject = "New Therapist Booking Payment Received";
        $adminMessage = "
            <p>You have received a payment of <strong>{$sitecurrency}{$price}</strong> for therapist <strong>{$therapist_name}</strong>.</p>
            <p>The full amount has been added to profits.</p>
            <p>— {$siteName}</p>
        ";
        sendEmail($siteMail, $siteName, $siteMail, "Admin", $adminMessage, $adminSubject);

    } else {
        // Admin commission
        if ($admin_commission > 0) {
            mysqli_query($con, "
                INSERT INTO {$siteprefix}profits (amount, booking_id, type, date)
                VALUES ('$admin_commission', '$booking_id', 'Therapist Booking Commission', '$date')
            ");
            $message = "Admin earned {$sitecurrency}{$admin_commission} from Therapist Booking ({$therapist_name})";
            insertAdminAlert($con, $message, "profits.php", $date, "profits", 0);

            // Email admin
            $adminSubject = "Commission Earned from Therapist Booking";
            $adminMessage = "
                <p>You earned a commission of <strong>{$sitecurrency}{$admin_commission}</strong> from a therapist booking.</p>
                <p>Therapist: <strong>{$therapist_name}</strong></p>
                <p>— {$siteName}</p>
            ";
            sendEmail($siteMail, $siteName, $siteMail, "Admin", $adminMessage, $adminSubject);
        }

        // Credit therapist wallet
        mysqli_query($con, "
            UPDATE {$siteprefix}users 
            SET wallet = wallet + $therapist_amount
            WHERE id='$therapist_id'
        ");
        insertWallet($con, $therapist_id, $therapist_amount, "credit", "Payment from Booking ({$client_name})", $date);
        insertAlert($con, $therapist_id, "You have received {$sitecurrency}{$therapist_amount} from Booking ({$client_name})", $date, 0);

        // Email therapist
        $emailSubject = "New Booking Payment Received";
        $emailMessage = "
            <p>Hello {$therapist_name},</p>
            <p>You have received <strong>{$sitecurrency}{$therapist_amount}</strong> for a completed booking from <strong>{$client_name}</strong>.</p>
            <p>An admin commission of <strong>{$sitecurrency}{$admin_commission}</strong> has been deducted.</p>
            <p>Thank you for using {$siteName}!</p>
        ";
        sendEmail($therapist_email, $siteName, $siteMail, $therapist_name, $emailMessage, $emailSubject);
    }

    // --- Step 6: Email client ---
    $clientSubject = "Payment Confirmation - Booking Successful";
    $clientMessage = "
        <p>Dear {$client_name},</p>
        <p>Your payment of <strong>{$sitecurrency}{$price}</strong> for your booking has been successfully processed.</p>
        <p>Your therapist, <strong>{$therapist_name}</strong>, has been notified and will reach out to you shortly.</p>
        <p>Thank you for choosing {$siteName}.</p>
    ";
    sendEmail($client_email, $siteName, $siteMail, $client_name, $clientMessage, $clientSubject);

    // --- Step 7: Redirect client ---
    echo "<script>
        alert('Payment successful! Your booking has been confirmed.');
        window.location.href='{$siteurl}my-bookings.php';
    </script>";
    exit;
}



function verifySubscriptionPayment($con, $siteprefix, $siteurl, $sitecurrency, $siteName = '', $siteMail = '') {
    if (!isset($_GET['reference']) || !isset($_GET['plan_id']) || !isset($_GET['user_id'])) {
        die("Invalid payment request.");
    }

    $reference = mysqli_real_escape_string($con, $_GET['reference']);
    $plan_id = intval($_GET['plan_id']);
    $user_id = intval($_GET['user_id']);
    $currentdatetime = date('Y-m-d H:i:s');
    $date = $currentdatetime;

    // --- Fetch plan details ---
    $planQuery = mysqli_query($con, "SELECT name, price, duration_days FROM {$siteprefix}subscriptions WHERE id='$plan_id' LIMIT 1");
    if (!$planQuery || mysqli_num_rows($planQuery) == 0) {
        die("Invalid plan selected.");
    }

    $plan = mysqli_fetch_assoc($planQuery);
    $plan_name = mysqli_real_escape_string($con, $plan['name']);
    $amount = floatval($plan['price']);
    $duration_days = intval($plan['duration_days']);

    // --- Fetch user info ---
    $userQuery = mysqli_query($con, "SELECT email, first_name FROM {$siteprefix}users WHERE id='$user_id' LIMIT 1");
    if (!$userQuery || mysqli_num_rows($userQuery) == 0) {
        die("Invalid user record.");
    }
    $user = mysqli_fetch_assoc($userQuery);
    $userEmail = $user['email'];
    $firstName = $user['first_name'];

    // --- Calculate subscription start and end date ---
    $subscription_start = date('Y-m-d');
    $subscription_end = date('Y-m-d', strtotime("+$duration_days days"));

    // --- Update user subscription details ---
    $updateUser = "
        UPDATE {$siteprefix}users 
        SET subscription_status='$plan_name', 
            subscription_plan_id='$plan_id', 
            subscription_start='$subscription_start',
            subscription_end='$subscription_end'
        WHERE id='$user_id'
    ";
    mysqli_query($con, $updateUser);

    // --- Record payment in payments table ---
    $insertPayment = "
        INSERT INTO {$siteprefix}payments 
        (user_id, plan_id, reference, amount, status, date)
        VALUES ('$user_id', '$plan_id', '$reference', '$amount', 'success', '$currentdatetime')
    ";
    mysqli_query($con, $insertPayment);

    // --- Admin Commission ---
    $admin_commission = $amount; // full amount to admin
    $insertCommission = "
        INSERT INTO {$siteprefix}profits (amount, plan_id, order_id, type, date)
        VALUES ('$admin_commission', '$plan_id', '$reference', 'Subscription Payment', '$currentdatetime')
    ";
    mysqli_query($con, $insertCommission);

    // --- Admin alert ---
    $message = "Admin Commission of $sitecurrency$admin_commission from Subscription Plan ($plan_name)";
    $link = "profits.php";
    $msgtype = "profits";
    insertadminAlert($con, $message, $link, $date, $msgtype, 0);

    // --- User alert ---
    insertAlert($con, $user_id, "You have successfully subscribed to $plan_name Plan", $date, 0);

    // --- Send email confirmation to user ---
    $emailSubject = "Subscription Confirmation - $plan_name Plan";
    $emailMessage = "
        <p>Hi {$firstName},</p>
        <p>Thank you for subscribing to the <strong>{$plan_name}</strong> plan on {$siteName}.</p>
        <p>Your subscription is active from <strong>{$subscription_start}</strong> to <strong>{$subscription_end}</strong>.</p>
        <p>We appreciate your trust in us!</p>
    ";
    sendEmail($userEmail, $siteName, $siteMail, $firstName, $emailMessage, $emailSubject);

    // --- Success redirect ---
    echo "<script>
        alert('Payment successful! Your subscription has been activated.');
        window.location.href='{$siteurl}login';
    </script>";
    exit;
}

function verifyAdvertPaymentSuccess($con, $siteprefix, $siteurl, $sitecurrency, $siteName = '', $siteMail = '') {

    if (!isset($_GET['reference'])) {
        die("Invalid payment request.");
    }

    $reference = mysqli_real_escape_string($con, $_GET['reference']);
    $currentdatetime = date('Y-m-d H:i:s');
    $date = $currentdatetime;

    // 1. FETCH ORDER (only pending allowed)
    $orderQuery = mysqli_query($con, "
        SELECT *
        FROM {$siteprefix}advert_orders
        WHERE reference='$reference'
        LIMIT 1
    ");

    if (!$orderQuery || mysqli_num_rows($orderQuery) === 0) {
        die("Order not found.");
    }

    $order = mysqli_fetch_assoc($orderQuery);

    if ($order['status'] !== 'pending') {
        die("Payment already processed.");
    }

    $advert_id     = $order['advert_id'];
    $user_id       = $order['user_id'];
    $amount        = $order['amount'];
    $start_date    = $order['start_date'];
    $end_date      = $order['end_date'];
    $banner        = $order['banner'];
    $redirect_url  = $order['redirect_url'];

    // Fetch buyer info
    $userQuery = mysqli_query($con, "
        SELECT first_name, email
        FROM {$siteprefix}users
        WHERE id='$user_id' LIMIT 1
    ");
    if (!$userQuery || mysqli_num_rows($userQuery) === 0) {
        die("Buyer not found.");
    }
    $user = mysqli_fetch_assoc($userQuery);
    $buyer_name  = $user['first_name'];
    $buyer_email = $user['email'];

    // 2. MARK ORDER AS PAID
    mysqli_query($con, "
        UPDATE {$siteprefix}advert_orders
        SET status='paid', paid_at='$date'
        WHERE reference='$reference'
    ");

    // 3. INSERT INTO ACTIVE ADVERTS
    mysqli_query($con, "
        INSERT INTO {$siteprefix}active_adverts 
        (advert_id, user_id, banner, redirect_url, start_date, end_date, created_at,status)
        VALUES 
        ('$advert_id', '$user_id', '$banner', '$redirect_url', '$start_date', '$end_date', '$date', 'active')
    ");

    // 4. ADD TO ADMIN PROFITS
    mysqli_query($con, "
        INSERT INTO {$siteprefix}profits (amount, advert_id, type, date)
        VALUES ('$amount', '$advert_id', 'Advert Purchase', '$date')
    ");

    // 5. ADMIN ALERT
    insertAdminAlert(
        $con,
        "A new advert payment of {$sitecurrency}" . number_format($amount, 2) . " was received.",
        "profits.php",
        $date,
        "adverts",
        0
    );

    // 6. BUYER ALERT
    insertAlert(
        $con,
        $user_id,
        "Your advert payment of {$sitecurrency}" . number_format($amount, 2) . " was successful. Your advert is now active.",
        $date,
        0
    );

    // 7. BUYER EMAIL
    $subject = "Advert Payment Successful";
    $message = "
        <p>Your advert payment of <strong>{$sitecurrency}" . number_format($amount, 2) . "</strong> was successful.</p>
        <p>Your advert from <strong>{$start_date}</strong> to <strong>{$end_date}</strong> is now active.</p>
    ";
    sendEmail($buyer_email, $siteName, $siteMail, $buyer_name, $message, $subject);

    // 8. REDIRECT USER
    echo "<script>
        alert('Payment successful! Your advert is now active.');
        window.location.href='{$siteurl}';
    </script>";
    exit;
}



// ✅ Handle verification call
if (isset($_GET['action']) && $_GET['action'] === 'verify_payment') {
    verifySubscriptionPayment($con, $siteprefix, $siteurl, $sitecurrency, $siteName, $siteMail);
}
// ✅ Handle verification action
if (isset($_GET['action']) && $_GET['action'] === 'verify-therapist-payment') {
    bookingPaymentSuccess($con, $siteprefix, $siteurl, $sitecurrency, $escrowfee, $siteName, $siteMail);
}
// ✅ Handle verification action
if (isset($_GET['action']) && $_GET['action'] === 'verify-group-payment') {
    paymentsuccess($con, $siteprefix, $siteurl, $sitecurrency);
}

if (isset($_GET['action']) && $_GET['action'] === 'verify-advert-payment') {
    verifyAdvertPaymentSuccess($con, $siteprefix, $siteurl, $sitecurrency, $siteName, $siteMail);
}
?>
