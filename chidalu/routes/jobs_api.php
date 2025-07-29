<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/jobs.php';
//endpoints for jobs
//add-job
//list-jobs
//get-job

$method = $_SERVER['REQUEST_METHOD'];
$endpoint = $_GET['endpoint'] ?? '';

$jobs = new Jobs($conn);

if ($method === 'POST') {
    switch ($endpoint) {
        case 'add-job':
            // Accept form-data or JSON
            $data = $_POST ?: json_decode(file_get_contents("php://input"), true);
            if (!isset($data['user_id']) || !isset($data['title']) || !isset($data['description'])) {
                http_response_code(400);
                echo json_encode(["error" => "User ID, title, and description are required"]);
                exit;
            }
            $budget = $data['budget'] ?? null;
            $deadline = $data['deadline'] ?? null;
            echo json_encode($jobs->addJob(
                $data['user_id'],
                $data['title'],
                $data['description'],
                $budget,
                $deadline
            ));
            break;

        default:
            http_response_code(404);
            echo json_encode(["error" => "Invalid endpoint"]);
            break;
    }
} elseif ($method === 'GET') {
    switch ($endpoint) {
        case 'list-jobs':
            echo json_encode($jobs->listJobs());
            break;

        case 'get-job':
            if (!isset($_GET['job_id'])) {
                http_response_code(400);
                echo json_encode(["error" => "Job ID is required"]);
                exit;
            }
            echo json_encode($jobs->getJob($_GET['job_id']));
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
