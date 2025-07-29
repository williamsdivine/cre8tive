<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/job_applications.php';

$method = $_SERVER['REQUEST_METHOD'];
$endpoint = $_GET['endpoint'] ?? '';
//endpoints for job applications
//apply-for-job
//list-applications

$applications = new JobApplications($conn);

if ($method === 'POST') {
    switch ($endpoint) {
        case 'apply-for-job':
            $data = $_POST ?: json_decode(file_get_contents("php://input"), true);
            if (!isset($data['job_id']) || !isset($data['applicant_id']) || !isset($data['cover_letter'])) {
                http_response_code(400);
                echo json_encode(["error" => "Job ID, applicant ID, and cover letter are required"]);
                exit;
            }
            echo json_encode($applications->applyForJob(
                $data['job_id'],
                $data['applicant_id'],
                $data['cover_letter']
            ));
            break;

        default:
            http_response_code(404);
            echo json_encode(["error" => "Invalid endpoint"]);
            break;
    }
} elseif ($method === 'GET') {
    switch ($endpoint) {
        case 'list-applications':
            if (!isset($_GET['job_id'])) {
                http_response_code(400);
                echo json_encode(["error" => "Job ID is required"]);
                exit;
            }
            echo json_encode($applications->listApplications($_GET['job_id']));
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
