<?php

//This will redirect the user to Google for authentication.
require_once __DIR__ . '/../config/google.php';

$authUrl = $googleClient->createAuthUrl();
header("Location: " . filter_var($authUrl, FILTER_SANITIZE_URL));
exit;
