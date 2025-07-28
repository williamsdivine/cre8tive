<?php
// Set response headers
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/user.php';

$method = $_SERVER['REQUEST_METHOD'];
$endpoint = $_GET['endpoint'] ?? '';

$user = new User($conn);

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if ($endpoint === 'signup') {
        if (!isset($data['fullname']) || !isset($data['email'])) {
            http_response_code(400);
            echo json_encode(["error" => "Fullname and email are required"]);
            exit;
        }
        echo json_encode($user->signup($data['fullname'], $data['email'], ""));
    } elseif ($endpoint === 'login') {
        if (!isset($data['email'])) {
            http_response_code(400);
            echo json_encode(["error" => "Email is required"]);
            exit;
        }
        echo json_encode($user->login($data['email'], ""));
    } elseif ($endpoint === 'verify-otp') {
        if (!isset($data['email']) || !isset($data['otp'])) {
            http_response_code(400);
            echo json_encode(["error" => "Email and OTP are required"]);
            exit;
        }
        echo json_encode($user->verifyOtp($data['email'], $data['otp']));
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Invalid endpoint"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
}
?>
