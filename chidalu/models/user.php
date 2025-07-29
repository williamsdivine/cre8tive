<?php
//methods for each endpoint (user signup, login, verify otp, update profile)
require_once __DIR__."/../config/send_mail.php";
require_once __DIR__."/../models/otp_email_template.php";

class User {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function signup($fullname, $email) {
        if (empty($fullname) || empty($email)) {
            return ["error" => "All fields are required"];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ["error" => "Invalid email format"];
        }

        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->close();
            return ["error" => "Email already registered"];
        }
        $stmt->close();

        $otp = rand(100000, 999999);

        $stmt = $this->conn->prepare("INSERT INTO users (fullname, email, otp) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $fullname, $email, $otp);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            if ($this->sendOtpEmail($email, $fullname, $otp)) {
                return ["statuscode" => 200, "message" => "User registered. OTP sent to email."];
            } else {
                return ["statuscode" => 500, "error" => "Failed to send OTP"];
            }
        }
        return ["error" => "Registration failed"];
    }

    /**
     * 
     */
    public function login($email) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user) {
            if ($user['is_verified']) {
                return ["message" => "Login successful", "fullname" => $user['fullname']];
            } else {
                return ["error" => "Please verify your email with OTP"];
            }
        }
        return ["error" => "Invalid email"];
    }

    public function verifyOtp($email, $otp) {
        $stmt = $this->conn->prepare("SELECT otp FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user && $user['otp'] == $otp) {
            $stmt = $this->conn->prepare("UPDATE users SET is_verified = 1, otp = NULL WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->close();
            return ["message" => "OTP verified. Account activated."];
        }
        return ["error" => "Invalid OTP"];
    }

    /**
     * Update the user's profile information (fullname and description only).
     *
     * @param string $email The user's email address (used to identify the user).
     * @param string $fullname The user's new full name.
     * @param string $description The user's profile description.
     * @return array Result of the update operation.
     */
    public function updateProfile($email, $fullname, $description) {
        // Check for empty fields
        if (empty($fullname) || empty($email) || empty($description)) {
            return ["error" => "All fields are required"];
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ["error" => "Invalid email format"];
        }

        // Update the user's profile (fullname and description only)
        $stmt = $this->conn->prepare("UPDATE users SET fullname = ?, description = ? WHERE email = ?");
        $stmt->bind_param("sss", $fullname, $description, $email);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            return ["statuscode" => 200, "message" => "Profile updated successfully"];
        } else {
            return ["error" => "Failed to update profile"];
        }
    }

    private function sendOtpEmail($email, $fullname, $otp) {
      $html = generateOtpEmailTemplate($email, $fullname, $otp);
      $subject = "Your OTP Code";
      $to = $email;
      $data = [
        "email" => $to,
        "name" => $fullname,
        "subject" => $subject,
        "message" => $html
      ];
      $mail = sendMail($data);
      return $mail;
    }
}
?>
