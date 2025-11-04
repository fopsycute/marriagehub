<?php 
session_start();
ob_start();

// ✅ Log errors but don’t display to users in production
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// ✅ Database credentials
$db_host = "localhost";
$db_username = "marriage_project";
$db_pass = ")s2bF7dP]VB1w3";
$db_name = "marriage_hub";

// ✅ Avoid multiple connections
if (!isset($con) || !$con instanceof mysqli || !$con->ping()) {
    $con = mysqli_connect($db_host, $db_username, $db_pass, $db_name);

    if (!$con) {
        error_log("❌ DB Connection failed: " . mysqli_connect_error());
        die("Database connection error. Please try again later.");
    }

    mysqli_set_charset($con, "utf8mb4");
    mysqli_query($con, "SET time_zone = '+01:00'"); // Africa/Lagos
}

// ✅ Automatically close connection when the script ends
register_shutdown_function(function() use ($con) {
    if ($con && $con instanceof mysqli) {
        mysqli_close($con);
    }
});

// ✅ Global configuration
$siteprefix = "ma_";
date_default_timezone_set('Africa/Lagos');
$currentdate = date("Y-m-d");
$currentdatetime = date("Y-m-d H:i:s");
$imagePath = 'uploads/';
$adminlink = 'admin/';
$adminName = 'Marriagehub';
$adminimagePath = '../../uploads/';
$admindocumentPath = '../../documents/';
$sitecurrency = "₦";
$sitecurrencyCode = "&#8358;";
$documentPath = 'documents/';
$adminurl = 'https://admin.marriagehub.ng/';

// ✅ Fetch site settings (single call)
$sql = "SELECT * FROM {$siteprefix}site_settings LIMIT 1";
$sql2 = mysqli_query($con, $sql);

if ($sql2 && mysqli_num_rows($sql2) > 0) {
    $row = mysqli_fetch_array($sql2);
    $apikey = $row["paystack_key"]; 
    $sitemail = $row["site_mail"];
    $sitenumber = $row["site_number"];
    $sitename = $row["site_name"]; 
    $siteimg = $row["site_logo"];
    $siteurl = $row["site_url"];
    $tinymce = $row["tinymce"];
    $escrowfee = $row["commision_fee"];
    $affiliate_percentage = $row["affliate_percentage"];
    $sitedescription = $row["site_description"];
    $siteaccno = $row["account_number"];
    $siteaccname = $row["account_name"];
    $site_bank = $row["site_bank"];
    $sitekeywords = $row["site_keywords"];
}

$siteName = $sitename ?? '';
$siteMail = $sitemail ?? '';

// ✅ Include shared functions
include_once "functions.php"; 
?>
