<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/creatives.php';

$method = $_SERVER['REQUEST_METHOD'];
$endpoint = $_GET['endpoint'] ?? '';

$creatives = new Creatives($conn);

// File upload handling function
function handleFileUpload($file) {
    $allowed_types = [
        'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp',
        'video/mp4', 'video/avi', 'video/mov', 'video/wmv', 'video/flv',
        'video/webm', 'video/mkv'
    ];
    $max_size = 100 * 1024 * 1024; // 100MB
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ["error" => "File upload failed: " . $file['error']];
    }
    
    if (!in_array($file['type'], $allowed_types)) {
        return ["error" => "File type not allowed. Allowed types: JPG, PNG, GIF, MP4, AVI, MOV, WMV, FLV, WEBM, MKV"];
    }
    
    if ($file['size'] > $max_size) {
        return ["error" => "File too large. Maximum size: 100MB"];
    }
    
    // Create upload directory if it doesn't exist
    $upload_dir = __DIR__ . '/../uploads/creatives/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Generate unique filename
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $file_extension;
    $filepath = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return [
            "success" => true,
            "filepath" => 'uploads/creatives/' . $filename,
            "filename" => $filename,
            "file_type" => $file['type'],
            "file_size" => $file['size']
        ];
    }
    
    return ["error" => "Failed to save file"];
}

if ($method === 'POST') {
    switch ($endpoint) {
        case 'add-creative':
            // Handle multipart form data for file upload
            if (!isset($_POST['user_id']) || !isset($_POST['title'])) {
                http_response_code(400);
                echo json_encode(["error" => "User ID and title are required"]);
                exit;
            }
            
            $user_id = $_POST['user_id'];
            $title = $_POST['title'];
            $description = $_POST['description'] ?? '';
            $media_url = '';
            $file_type = '';
            $file_size = 0;
            
            // Handle file upload if present
            if (isset($_FILES['media']) && $_FILES['media']['error'] !== UPLOAD_ERR_NO_FILE) {
                $upload_result = handleFileUpload($_FILES['media']);
                
                if (isset($upload_result['error'])) {
                    http_response_code(400);
                    echo json_encode($upload_result);
                    exit;
                }
                
                $media_url = $upload_result['filepath'];
                $file_type = $upload_result['file_type'];
                $file_size = $upload_result['file_size'];
            }
            
            echo json_encode($creatives->addCreative(
                $user_id,
                $title,
                $description,
                $media_url,
                $file_type,
                $file_size
            ));
            break;

        case 'delete-creative':
            if (!isset($_POST['creative_id']) || !isset($_POST['user_id'])) {
                http_response_code(400);
                echo json_encode(["error" => "Creative ID and User ID are required"]);
                exit;
            }
            echo json_encode($creatives->deleteCreative(
                $_POST['creative_id'],
                $_POST['user_id']
            ));
            break;

        default:
            http_response_code(404);
            echo json_encode(["error" => "Invalid endpoint"]);
            break;
    }
} elseif ($method === 'GET') {
    switch ($endpoint) {
        case 'list-creatives':
            if (!isset($_GET['user_id'])) {
                http_response_code(400);
                echo json_encode(["error" => "User ID is required"]);
                exit;
            }
            echo json_encode($creatives->listCreatives($_GET['user_id']));
            break;

        case 'get-creative':
            if (!isset($_GET['creative_id'])) {
                http_response_code(400);
                echo json_encode(["error" => "Creative ID is required"]);
                exit;
            }
            echo json_encode($creatives->getCreative($_GET['creative_id']));
            break;

        case 'list-all-creatives':
            // Get all creatives for public viewing
            echo json_encode($creatives->listAllCreatives());
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
