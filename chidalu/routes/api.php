<?php
// Set response headers
// endpoints: signup, login, verify-otp, update-profile
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

    switch ($endpoint) {
        case 'signup':
            if (!isset($data['fullname']) || !isset($data['email'])) {
                http_response_code(400);
                echo json_encode(["error" => "Fullname and email are required"]);
                exit;
            }
            echo json_encode($user->signup($data['fullname'], $data['email'], ""));
            break;
        case 'login':
            if (!isset($data['email'])) {
                http_response_code(400);
                echo json_encode(["error" => "Email is required"]);
                exit;
            }
            echo json_encode($user->login($data['email'], ""));
            break;
        case 'verify-otp':
            if (!isset($data['email']) || !isset($data['otp'])) {
                http_response_code(400);
                echo json_encode(["error" => "Email and OTP are required"]);
                exit;
            }
            echo json_encode($user->verifyOtp($data['email'], $data['otp']));
            break;
        case 'update-profile':
            if (!isset($data['email']) || !isset($data['fullname']) || !isset($data['description'])) {
                http_response_code(400);
                echo json_encode(["error" => "Email, fullname, and description are required"]);
                exit;
            }
            echo json_encode($user->updateProfile($data['email'], $data['fullname'], $data['description']));
            break;
        default:
            http_response_code(404);
            echo json_encode(["error" => "Invalid endpoint"]);
            break;
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
}
?>
