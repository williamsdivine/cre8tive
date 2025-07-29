<?php

class Messages {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Send a message
    public function sendMessage($sender_id, $receiver_id, $job_id, $message) {
        if (empty($sender_id) || empty($receiver_id) || empty($message)) {
            return ["error" => "Sender ID, receiver ID, and message are required"];
        }
        $stmt = $this->conn->prepare("INSERT INTO messages (sender_id, receiver_id, job_id, message) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            return ["error" => "Prepare failed: " . $this->conn->error];
        }
        $stmt->bind_param("iiis", $sender_id, $receiver_id, $job_id, $message);
        $result = $stmt->execute();
        $stmt->close();
        if ($result) {
            return ["statuscode" => 201, "message" => "Message sent successfully"];
        } else {
            return ["error" => "Failed to send message"];
        }
    }

    // Get messages between two users about a job
    public function getMessages($job_id, $user1_id, $user2_id) {
        if (empty($job_id) || empty($user1_id) || empty($user2_id)) {
            return ["error" => "Job ID and both user IDs are required"];
        }
        $stmt = $this->conn->prepare("SELECT m.*, s.fullname as sender_name, r.fullname as receiver_name FROM messages m JOIN users s ON m.sender_id = s.id JOIN users r ON m.receiver_id = r.id WHERE m.job_id = ? AND ((m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)) ORDER BY m.created_at ASC");
        if (!$stmt) {
            return ["error" => "Prepare failed: " . $this->conn->error];
        }
        $stmt->bind_param("iiiii", $job_id, $user1_id, $user2_id, $user2_id, $user1_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $messages = [];
        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }
        $stmt->close();
        return ["statuscode" => 200, "messages" => $messages];
    }
}
