<?php
include "script/connect.php";

function paymentsuccess($con, $siteprefix, $siteurl, $sitecurrency) {
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
    mysqli_query($con, "INSERT INTO {$siteprefix}group_payments (group_id, user_id, amount, duration, status, date) 
                        VALUES ('$group_id', '$user_id', '$amount', '$duration', '$payment_status_text', '$currentdatetime')");

    // --- Get seller (group owner) ---
    $sellerResult = mysqli_query($con, "SELECT user_id FROM {$siteprefix}groups WHERE id='$group_id' LIMIT 1");
    if ($sellerResult && mysqli_num_rows($sellerResult) > 0) {
        $sellerData = mysqli_fetch_assoc($sellerResult);
        $seller_id = $sellerData['user_id'];

        // --- Admin commission ---
        $admin_commission = 0; // set your % if needed
        $seller_amount = $amount - $admin_commission;

        if ($admin_commission > 0) {
            mysqli_query($con, "INSERT INTO {$siteprefix}profits (amount, group_id, order_id, type, date)
                                VALUES ('$admin_commission', '$group_id', '$group_id', 'Subscription Payment', '$currentdatetime')");
            $message = "Admin Commission of $sitecurrency$admin_commission from Group Subscription";
            insertadminAlert($con, $message, "profits.php", $date, "profits", 0);
        }

        // --- Credit seller ---
        mysqli_query($con, "UPDATE {$siteprefix}users SET wallet = wallet + $seller_amount WHERE id='$seller_id'");
        insertWallet($con, $seller_id, $seller_amount, "credit", "Payment from Group Subscription (Group ID: $group_id)", $date);
        insertAlert($con, $seller_id, "You have received $sitecurrency$seller_amount from Group Subscription (Group ID: $group_id)", $date, 0);
    }

    // --- Redirect to group page (pending approval) ---
    echo "<script>
        alert('Payment successful! Please wait for admin approval before accessing the group.');
        window.location.href='{$siteurl}my-group.php';
    </script>";
    exit;
}

// ✅ Handle verification action
if (isset($_GET['action']) && $_GET['action'] === 'verify-group-payment') {
    paymentsuccess($con, $siteprefix, $siteurl, $sitecurrency);
}
?>
