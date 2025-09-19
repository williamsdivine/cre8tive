<?php
require_once __DIR__ . '/../vendor/autoload.php';

$googleClient = new Google_Client();
$googleClient->setClientId("YOUR_GOOGLE_CLIENT_ID");
$googleClient->setClientSecret("YOUR_GOOGLE_CLIENT_SECRET");
$googleClient->setRedirectUri("http://localhost/cre8tive/routes/google-callback.php");

// Ask only for email + profile
$googleClient->addScope("email");
$googleClient->addScope("profile");
?>
