<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/messages.php';

$method = $_SERVER['REQUEST_METHOD'];
$endpoint = $_GET['endpoint'] ?? '';
//endpoints for messages
//send-message
//get-messages

$messages = new Messages($conn);

if ($method === 'POST') {
    switch ($endpoint) {
        case 'send-message':
            $data = $_POST ?: json_decode(file_get_contents("php://input"), true);
            if (!isset($data['sender_id']) || !isset($data['receiver_id']) || !isset($data['job_id']) || !isset($data['message'])) {
                http_response_code(400);
                echo json_encode(["error" => "Sender ID, receiver ID, job ID, and message are required"]);
                exit;
            }
            echo json_encode($messages->sendMessage(
                $data['sender_id'],
                $data['receiver_id'],
                $data['job_id'],
                $data['message']
            ));
            break;

        default:
            http_response_code(404);
            echo json_encode(["error" => "Invalid endpoint"]);
            break;
    }
} elseif ($method === 'GET') {
    switch ($endpoint) {
        case 'get-messages':
            if (!isset($_GET['job_id']) || !isset($_GET['user1_id']) || !isset($_GET['user2_id'])) {
                http_response_code(400);
                echo json_encode(["error" => "Job ID and both user IDs are required"]);
                exit;
            }
            echo json_encode($messages->getMessages(
                $_GET['job_id'],
                $_GET['user1_id'],
                $_GET['user2_id']
            ));
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
