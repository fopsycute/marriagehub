
<?php 
session_start(); ob_start();

error_reporting(E_ALL); ini_set('display_errors', 1); ini_set('log_errors', 1);

   
$db_host = "localhost"; 

/*
   
$db_username = "root"; 
$db_pass = ""; 
$db_name = "marriage_hub_ng";
$db_username = "root"; 
$db_pass = ""; 
$db_name = "learnora";
$db_username = "projectr_learnorastore"; 
$db_pass = "Y34GgwK(]h82Yg"; 
$db_name = "projectr_learn";
$db_username = "learnora_learn"; 
$db_pass = "7Ums6HDNrg)03*"; 
$db_name = "learnora_learnorastore";
conne
*/

   
$db_username = "marriage_project"; 
$db_pass = ")s2bF7dP]VB1w3"; 
$db_name = "marriage_hub";

$con = mysqli_connect ("$db_host","$db_username","$db_pass","$db_name");
mysqli_query($con, "SET time_zone = '+01:00'"); // Africa/Lagos
$siteprefix="ma_";
date_default_timezone_set('Africa/Lagos');
$currentdate=date("Y-m-d");
$currentdatetime=date("Y-m-d H:i:s");
$imagePath='uploads/';
$adminlink='admin/';
$adminName='Learnora';
$adminimagePath='../../uploads/';
$admindocumentPath='../../documents/';
$sitecurrency="₦";
$sitecurrencyCode="&#8358;";
$documentPath='documents/';
$affiliateurl='https://learnora.affiliate.projectreporthub.ng/';
$adminurl='https://learnora.admin.projectreporthub.ng/';
/*
$affiliateurl='https://affiliate.learnora.ng/';

*/

$sql = "SELECT * from ".$siteprefix."site_settings";
$sql2 = mysqli_query($con,$sql);
while($row = mysqli_fetch_array($sql2))
{$apikey = $row["paystack_key"]; 
$sitemail = $row["site_mail"];
$sitenumber = $row["site_number"];
$sitename = $row["site_name"]; 
$siteimg= $row["site_logo"];
$siteurl= $row["site_url"];
$tinymce = $row["tinymce"];
$brevokey=$row["brevo_key"];
$escrowfee= $row["commision_fee"];
$affiliate_percentage= $row["affliate_percentage"];
$sitedescription= $row["site_description"];
$siteaccno= $row["account_number"];
$siteaccname= $row["account_name"];
$site_bank= $row["site_bank"];
$sitekeywords= $row["site_keywords"];
//$paymenturl = $row["payment_url"];
//$tinymce = $row["tinymce"];
}

$siteName=$sitename;
$siteMail=$sitemail;

include "functions.php"; 

?>