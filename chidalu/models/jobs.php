<?php
//model method for jobs (add, list all, get)
class Jobs {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Add a new job
    public function addJob($user_id, $title, $description, $budget = null, $deadline = null) {
        if (empty($user_id) || empty($title) || empty($description)) {
            return ["error" => "User ID, title, and description are required"];
        }
        $stmt = $this->conn->prepare("INSERT INTO jobs (user_id, title, description, budget, deadline) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            return ["error" => "Prepare failed: " . $this->conn->error];
        }
        // If budget or deadline are empty, set to null
        $budget = ($budget === '' || is_null($budget)) ? null : $budget;
        $deadline = ($deadline === '' || is_null($deadline)) ? null : $deadline;
        $stmt->bind_param("issds", $user_id, $title, $description, $budget, $deadline);
        $result = $stmt->execute();
        $stmt->close();
        if ($result) {
            return ["statuscode" => 201, "message" => "Job posted successfully"];
        } else {
            return ["error" => "Failed to post job"];
        }
    }

    // List all jobs
    public function listJobs() {
        $stmt = $this->conn->prepare("SELECT j.*, u.fullname as user_name FROM jobs j JOIN users u ON j.user_id = u.id ORDER BY j.created_at DESC");
        if (!$stmt) {
            return ["error" => "Prepare failed: " . $this->conn->error];
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $jobs = [];
        while ($row = $result->fetch_assoc()) {
            $jobs[] = $row;
        }
        $stmt->close();
        return ["statuscode" => 200, "jobs" => $jobs];
    }

    // Get a single job by ID
    public function getJob($job_id) {
        if (empty($job_id)) {
            return ["error" => "Job ID is required"];
        }
        $stmt = $this->conn->prepare("SELECT j.*, u.fullname as user_name FROM jobs j JOIN users u ON j.user_id = u.id WHERE j.id = ?");
        if (!$stmt) {
            return ["error" => "Prepare failed: " . $this->conn->error];
        }
        $stmt->bind_param("i", $job_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $job = $result->fetch_assoc();
        $stmt->close();
        if ($job) {
            return ["statuscode" => 200, "job" => $job];
        } else {
            return ["error" => "Job not found"];
        }
    }
}
