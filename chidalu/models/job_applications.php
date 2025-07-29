<?php

class JobApplications {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Apply for a job
    public function applyForJob($job_id, $applicant_id, $cover_letter) {
        if (empty($job_id) || empty($applicant_id) || empty($cover_letter)) {
            return ["error" => "Job ID, applicant ID, and cover letter are required"];
        }
        $stmt = $this->conn->prepare("INSERT INTO job_applications (job_id, applicant_id, cover_letter) VALUES (?, ?, ?)");
        if (!$stmt) {
            return ["error" => "Prepare failed: " . $this->conn->error];
        }
        $stmt->bind_param("iis", $job_id, $applicant_id, $cover_letter);
        $result = $stmt->execute();
        $stmt->close();
        if ($result) {
            return ["statuscode" => 201, "message" => "Application submitted successfully"];
        } else {
            return ["error" => "Failed to submit application"];
        }
    }

    // List all applications for a job
    public function listApplications($job_id) {
        if (empty($job_id)) {
            return ["error" => "Job ID is required"];
        }
        $stmt = $this->conn->prepare("SELECT a.*, u.fullname as applicant_name, u.email as applicant_email FROM job_applications a JOIN users u ON a.applicant_id = u.id WHERE a.job_id = ? ORDER BY a.created_at DESC");
        if (!$stmt) {
            return ["error" => "Prepare failed: " . $this->conn->error];
        }
        $stmt->bind_param("i", $job_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $applications = [];
        while ($row = $result->fetch_assoc()) {
            $applications[] = $row;
        }
        $stmt->close();
        return ["statuscode" => 200, "applications" => $applications];
    }
} 