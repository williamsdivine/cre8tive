<?php

class Creatives {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function addCreative($user_id, $title, $description, $media_url, $file_type = '', $file_size = 0) {
        // Validate required fields
        if (empty($user_id) || empty($title)) {
            return ["error" => "User ID and title are required"];
        }

        $stmt = $this->conn->prepare("INSERT INTO creatives (user_id, title, description, media_url, file_type, file_size) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            return ["error" => "Prepare failed: " . $this->conn->error];
        }
        $stmt->bind_param("issssi", $user_id, $title, $description, $media_url, $file_type, $file_size);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            return ["statuscode" => 201, "message" => "Creative added successfully"];
        } else {
            return ["error" => "Failed to add creative"];
        }
    }

    public function listCreatives($user_id) {
        // Validate required fields
        if (empty($user_id)) {
            return ["error" => "User ID is required"];
        }

        $stmt = $this->conn->prepare("SELECT c.*, u.fullname as user_name FROM creatives c 
                                     JOIN users u ON c.user_id = u.id 
                                     WHERE c.user_id = ? 
                                     ORDER BY c.created_at DESC");
        if (!$stmt) {
            return ["error" => "Prepare failed: " . $this->conn->error];
        }
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $creatives = [];
        while ($row = $result->fetch_assoc()) {
            $creatives[] = $row;
        }
        $stmt->close();

        return ["statuscode" => 200, "creatives" => $creatives];
    }

    public function listAllCreatives() {
        $stmt = $this->conn->prepare("SELECT c.*, u.fullname as user_name FROM creatives c 
                                     JOIN users u ON c.user_id = u.id 
                                     ORDER BY c.created_at DESC");
        if (!$stmt) {
            return ["error" => "Prepare failed: " . $this->conn->error];
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $creatives = [];
        while ($row = $result->fetch_assoc()) {
            $creatives[] = $row;
        }
        $stmt->close();

        return ["statuscode" => 200, "creatives" => $creatives];
    }

    public function getCreative($creative_id) {
        // Validate required fields
        if (empty($creative_id)) {
            return ["error" => "Creative ID is required"];
        }

        $stmt = $this->conn->prepare("SELECT c.*, u.fullname as user_name FROM creatives c 
                                     JOIN users u ON c.user_id = u.id 
                                     WHERE c.id = ?");
        if (!$stmt) {
            return ["error" => "Prepare failed: " . $this->conn->error];
        }
        $stmt->bind_param("i", $creative_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $creative = $result->fetch_assoc();
        $stmt->close();

        if ($creative) {
            return ["statuscode" => 200, "creative" => $creative];
        } else {
            return ["error" => "Creative not found"];
        }
    }

    public function deleteCreative($creative_id, $user_id) {
        // Validate required fields
        if (empty($creative_id) || empty($user_id)) {
            return ["error" => "Creative ID and User ID are required"];
        }

        // First get the creative to delete the file
        $stmt = $this->conn->prepare("SELECT media_url FROM creatives WHERE id = ? AND user_id = ?");
        if (!$stmt) {
            return ["error" => "Prepare failed: " . $this->conn->error];
        }
        $stmt->bind_param("ii", $creative_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $creative = $result->fetch_assoc();
        $stmt->close();

        if ($creative && !empty($creative['media_url'])) {
            // Delete the file from server
            $file_path = __DIR__ . '/../' . $creative['media_url'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }

        // Delete from database
        $stmt = $this->conn->prepare("DELETE FROM creatives WHERE id = ? AND user_id = ?");
        if (!$stmt) {
            return ["error" => "Prepare failed: " . $this->conn->error];
        }
        $stmt->bind_param("ii", $creative_id, $user_id);
        $result = $stmt->execute();
        $stmt->close();

        if ($result && $this->conn->affected_rows > 0) {
            return ["statuscode" => 200, "message" => "Creative deleted successfully"];
        } else {
            return ["error" => "Creative not found or not deleted"];
        }
    }
}   
