<?php

/**
 * User Class
 *
 * Manages public user data and authentication for interactions like
 * commenting and liking. This is separate from the Admin class and uses
 * the 'users' and 'auth_tokens' tables.
 */
class User
{
    private $conn;
    private $table_name = 'users';
    private $tokens_table = 'auth_tokens';
    private $profiles_table = 'user_profiles';
    private $employment_table = 'user_employment';

    public $id;
    public $username;
    public $email;
    public $nric;
    public $password;
    public $login_identifier;
    public $profile_pic_url; // Public property to hold the profile picture URL

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // --- User Authentication Methods ---
    public function register()
    {
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->nric = htmlspecialchars(strip_tags($this->nric ?? ''));
        $this->password = htmlspecialchars(strip_tags($this->password));
        if ($this->userExists()) {
            return false;
        }
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $query = "INSERT INTO " . $this->table_name . " (username, email, nric, password) VALUES (:username, :email, :nric, :password)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':nric', $this->nric);
        $stmt->bindParam(':password', $password_hash);
        if ($stmt->execute()) {
            $user_id = $this->conn->lastInsertId();
            $this->createEmptyProfile($user_id);
            $this->createEmptyEmployment($user_id);
            return true;
        }
        return false;
    }
    public function login()
    {
        $this->login_identifier = htmlspecialchars(strip_tags($this->login_identifier));
        $query = "SELECT id, username, password FROM " . $this->table_name . " WHERE email = :login_identifier OR nric = :login_identifier LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':login_identifier', $this->login_identifier);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($this->password, $row['password'])) {
                $this->id = $row['id'];
                $this->username = $row['username'];
                return true;
            }
        }
        return false;
    }
    private function userExists()
    {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email OR (nric = :nric AND nric IS NOT NULL AND nric != '') LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':nric', $this->nric);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
    public function setRememberToken()
    {
        $token = bin2hex(random_bytes(32));
        $token_hash = hash('sha256', $token);
        $expires = new DateTime();
        $expires->add(new DateInterval('P30D'));
        $expires_at = $expires->format('Y-m-d H:i:s');
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        $device_info = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';
        $query = "INSERT INTO " . $this->tokens_table . " (user_id, token, expires_at, ip_address, device_info, last_used_at) VALUES (:user_id, :token, :expires_at, :ip_address, :device_info, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->id);
        $stmt->bindParam(':token', $token_hash);
        $stmt->bindParam(':expires_at', $expires_at);
        $stmt->bindParam(':ip_address', $ip_address);
        $stmt->bindParam(':device_info', $device_info);
        if ($stmt->execute()) {
            return $token;
        }
        return false;
    }
    public function validateRememberToken($token)
    {
        if (empty($token)) {
            return false;
        }
        $token_hash = hash('sha256', $token);
        $query = "SELECT u.id, u.username, t.id as token_id, p.profile_pic_url 
                  FROM " . $this->tokens_table . " t 
                  JOIN " . $this->table_name . " u ON t.user_id = u.id
                  LEFT JOIN " . $this->profiles_table . " p ON u.id = p.user_id
                  WHERE t.token = :token AND t.expires_at > NOW() LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token_hash);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->profile_pic_url = $row['profile_pic_url']; // Set the public property
            $update_query = "UPDATE " . $this->tokens_table . " SET last_used_at = NOW() WHERE id = :token_id";
            $update_stmt = $this->conn->prepare($update_query);
            $update_stmt->bindParam(':token_id', $row['token_id']);
            $update_stmt->execute();
            return true;
        }
        return false;
    }
    public function clearRememberToken($token)
    {
        if (empty($token)) {
            return false;
        }
        $token_hash = hash('sha256', $token);
        $query = "DELETE FROM " . $this->tokens_table . " WHERE token = :token";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token_hash);
        return $stmt->execute();
    }

    // --- Profile Management Methods ---
    public function getProfile($user_id)
    {
        $query = "
            SELECT u.username, u.email, u.nric, p.full_name, p.bio, p.profile_pic_url, e.company, e.job_title, e.start_date, e.end_date, e.is_current
            FROM users u
            LEFT JOIN user_profiles p ON u.id = p.user_id
            LEFT JOIN user_employment e ON u.id = e.user_id
            WHERE u.id = :user_id
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function updateProfile($user_id, $data)
    {
        $this->conn->beginTransaction();
        try {
            $query_profile = "UPDATE " . $this->profiles_table . " SET full_name = :full_name, bio = :bio WHERE user_id = :user_id";
            $stmt_profile = $this->conn->prepare($query_profile);
            $stmt_profile->bindParam(':full_name', $data['full_name']);
            $stmt_profile->bindParam(':bio', $data['bio']);
            $stmt_profile->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt_profile->execute();
            $query_employment = "UPDATE " . $this->employment_table . " SET company = :company, job_title = :job_title, start_date = :start_date, end_date = :end_date, is_current = :is_current WHERE user_id = :user_id";
            $stmt_employment = $this->conn->prepare($query_employment);
            $is_current = isset($data['is_current']) ? 1 : 0;
            $end_date = $is_current ? null : $data['end_date'];
            $stmt_employment->bindParam(':company', $data['company']);
            $stmt_employment->bindParam(':job_title', $data['job_title']);
            $stmt_employment->bindParam(':start_date', $data['start_date']);
            $stmt_employment->bindParam(':end_date', $end_date);
            $stmt_employment->bindParam(':is_current', $is_current, PDO::PARAM_INT);
            $stmt_employment->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt_employment->execute();
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
    public function updateProfilePicture($user_id, $file)
    {
        $uploadDir = 'uploads/profiles/';
        $uploadResult = $this->handleFileUpload($file, $uploadDir);
        if ($uploadResult['status'] === 'success') {
            $filePath = $uploadResult['filepath'];
            $query = "UPDATE " . $this->profiles_table . " SET profile_pic_url = :profile_pic_url WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':profile_pic_url', $filePath);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            if ($stmt->execute()) {
                return ['status' => 'success'];
            }
        }
        return ['status' => 'error', 'message' => $uploadResult['message']];
    }
    private function createEmptyProfile($user_id)
    {
        $query = "INSERT INTO " . $this->profiles_table . " (user_id) VALUES (:user_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
    }
    private function createEmptyEmployment($user_id)
    {
        $query = "INSERT INTO " . $this->employment_table . " (user_id) VALUES (:user_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
    }
    private function handleFileUpload($file, $uploadDir)
    {
        if ($file['size'] > 2 * 1024 * 1024) {
            return ['status' => 'error', 'message' => 'File is too large. Maximum size is 2MB.'];
        }
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($finfo->file($file['tmp_name']), $allowedTypes)) {
            return ['status' => 'error', 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.'];
        }
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $uniqueName = bin2hex(random_bytes(16)) . '.' . $fileExtension;
        $destination = $uploadDir . $uniqueName;
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                return ['status' => 'error', 'message' => 'Server error: Could not create upload directory.'];
            }
        }
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return ['status' => 'success', 'filepath' => $destination];
        }
        return ['status' => 'error', 'message' => 'Server error: Could not move uploaded file.'];
    }
}
