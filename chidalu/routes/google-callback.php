<?php
require_once __DIR__ . '/../config/google.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/user.php';

if (isset($_GET['code'])) {
    $token = $googleClient->fetchAccessTokenWithAuthCode($_GET['code']);
    $googleClient->setAccessToken($token);

    // Get user info
    $googleService = new Google_Service_Oauth2($googleClient);
    $googleUser = $googleService->userinfo->get();

    $email = $googleUser->email;
    $fullname = $googleUser->name;

    // Save or login the user
    $user = new User($conn);

    // Check if user exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $existingUser = $result->fetch_assoc();
    $stmt->close();

    if ($existingUser) {
        // Already registered → log them in
        echo json_encode([
            "statuscode" => 200,
            "message" => "Login successful with Google",
            "fullname" => $existingUser['fullname'],
            "email" => $existingUser['email']
        ]);
    } else {
        // New user → create account (verified automatically)
        $stmt = $conn->prepare("INSERT INTO users (fullname, email, is_verified) VALUES (?, ?, 1)");
        $stmt->bind_param("ss", $fullname, $email);
        $stmt->execute();
        $stmt->close();

        echo json_encode([
            "statuscode" => 200,
            "message" => "Signup successful with Google",
            "fullname" => $fullname,
            "email" => $email
        ]);
    }
} else {
    echo json_encode(["error" => "Google login failed"]);
}
