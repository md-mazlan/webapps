<?php
// Include the main configuration file to make constants like ROOT_PATH available.
require_once 'config.php';

/**
 * User Class
 *
 * Manages public user data, authentication, and now includes methods
 * for handling the new detailed user profiles and employment history.
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
    public $profile_pic_url;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // --- User Authentication Methods (Unchanged) ---
    public function register()
    {
        $this->username = htmlspecialchars(strip_tags($this->username ?? ''));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->nric = htmlspecialchars(strip_tags($this->nric));
        $this->password = htmlspecialchars(strip_tags($this->password));
        if (empty($this->nric) || !is_numeric($this->nric)) {
            return false;
        }
        if ($this->userExists()) {
            return false;
        }
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $query = "INSERT INTO " . $this->table_name . " (nric, email, username, password) VALUES (:nric, :email, :username, :password)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nric', $this->nric);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':username', $this->username);
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
        $query = "SELECT u.id, u.username, u.password, p.profile_pic_url 
                  FROM " . $this->table_name . " u
                  LEFT JOIN " . $this->profiles_table . " p ON u.id = p.user_id
                  WHERE u.email = :identifier1 OR u.nric = :identifier2 
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':identifier1', $this->login_identifier);
        $stmt->bindParam(':identifier2', $this->login_identifier);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($this->password, $row['password'])) {
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->profile_pic_url = $row['profile_pic_url'];
                return true;
            }
        }
        return false;
    }
    private function userExists()
    {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email OR nric = :nric LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':email' => $this->email, ':nric' => $this->nric]);
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
            $this->profile_pic_url = $row['profile_pic_url'];
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
            SELECT u.username, u.email, u.nric, p.*, e.*
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

    public function insertPersonalInfo($user_id, $data)
    {
        $query = 'INSERT INTO ' . $this->profiles_table . ' (user_id, full_name, gender, ethnic, phone, birthday, address1, address2, area, postal_code, city, state) VALUES (:user_id, :full_name, :gender, :ethnic, :phone, :birthday, :address1, :address2, :area, :postal_code, :city, :state)';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':full_name', $data['full_name']);
        $stmt->bindParam(':gender', $data['gender']);
        $stmt->bindParam(':ethnic', $data['ethnic']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':birthday', $data['birthday']);
        $stmt->bindParam(':address1', $data['address1']);
        $stmt->bindParam(':address2', $data['address2']);
        $stmt->bindParam(':area', $data['area']);
        $stmt->bindParam(':postal_code', $data['postal_code']);
        $stmt->bindParam(':city', $data['city']);
        $stmt->bindParam(':state', $data['state']);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function insertEmploymentInfo($user_id, $data)
    {
        $query = 'INSERT INTO ' . $this->employment_table . ' (user_id, company, job_title, department, start_date, end_date, is_current, responsibilities) VALUES (:user_id, :company, :job_title, :department, :start_date, :end_date, :is_current, :responsibilities)';
        $stmt = $this->conn->prepare($query);
        $is_current = isset($data['is_current']) ? 1 : 0;
        $end_date = $is_current ? null : $data['end_date'];
        $stmt->bindParam(':company', $data['company']);
        $stmt->bindParam(':job_title', $data['job_title']);
        $stmt->bindParam(':department', $data['department']);
        $stmt->bindParam(':start_date', $data['start_date']);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->bindParam(':is_current', $is_current, PDO::PARAM_INT);
        $stmt->bindParam(':responsibilities', $data['responsibilities']);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updatePersonalInfo($user_id, $data)
    {
        $query = "UPDATE " . $this->profiles_table . " SET 
            full_name = :full_name, gender = :gender, ethnic = :ethnic, phone = :phone, 
            birthday = :birthday, address1 = :address1, address2 = :address2, area = :area, 
            postal_code = :postal_code, city = :city, state = :state 
            WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':full_name', $data['full_name']);
        $stmt->bindParam(':gender', $data['gender']);
        $stmt->bindParam(':ethnic', $data['ethnic']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':birthday', $data['birthday']);
        $stmt->bindParam(':address1', $data['address1']);
        $stmt->bindParam(':address2', $data['address2']);
        $stmt->bindParam(':area', $data['area']);
        $stmt->bindParam(':postal_code', $data['postal_code']);
        $stmt->bindParam(':city', $data['city']);
        $stmt->bindParam(':state', $data['state']);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateEmploymentInfo($user_id, $data)
    {
        $query = "UPDATE " . $this->employment_table . " SET 
            company = :company, job_title = :job_title, department = :department, 
            start_date = :start_date, end_date = :end_date, is_current = :is_current, 
            responsibilities = :responsibilities 
            WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $is_current = isset($data['is_current']) ? 1 : 0;
        $end_date = $is_current ? null : $data['end_date'];
        $stmt->bindParam(':company', $data['company']);
        $stmt->bindParam(':job_title', $data['job_title']);
        $stmt->bindParam(':department', $data['department']);
        $stmt->bindParam(':start_date', $data['start_date']);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->bindParam(':is_current', $is_current, PDO::PARAM_INT);
        $stmt->bindParam(':responsibilities', $data['responsibilities']);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateProfilePicture($user_id, $file)
    {
        $relativeUploadDir = 'uploads/profiles/';
        $absoluteUploadDir = ROOT_PATH . '/' . $relativeUploadDir;
        $uploadResult = $this->handleFileUpload($file, $absoluteUploadDir);

        if ($uploadResult['status'] === 'success') {
            $filePathInDb = $relativeUploadDir . basename($uploadResult['filepath']);

            $query = "UPDATE " . $this->profiles_table . " SET profile_pic_url = :profile_pic_url WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':profile_pic_url', $filePathInDb);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            if ($stmt->execute()) {
                $_SESSION['profile_pic_url'] = $filePathInDb;
                return ['status' => 'success'];
            }
        }
        return ['status' => 'error', 'message' => $uploadResult['message']];
    }

    public function changePassword($user_id, $old_password, $new_password)
    {
        $query = "SELECT password FROM " . $this->table_name . " WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return ['status' => 'error', 'message' => 'User not found.'];
        }

        if (!password_verify($old_password, $row['password'])) {
            return ['status' => 'error', 'message' => 'Incorrect current password.'];
        }

        $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);

        $update_query = "UPDATE " . $this->table_name . " SET password = :password WHERE id = :user_id";
        $update_stmt = $this->conn->prepare($update_query);
        $update_stmt->bindParam(':password', $new_password_hash);
        $update_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        if ($update_stmt->execute()) {
            return ['status' => 'success', 'message' => 'Password changed successfully.'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to update password.'];
        }
    }

    public function deleteAccount($user_id, $password)
    {
        $query = "SELECT password FROM " . $this->table_name . " WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || !password_verify($password, $row['password'])) {
            return ['status' => 'error', 'message' => 'Incorrect password. Account not deleted.'];
        }

        $delete_query = "DELETE FROM " . $this->table_name . " WHERE id = :user_id";
        $delete_stmt = $this->conn->prepare($delete_query);
        $delete_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        if ($delete_stmt->execute()) {
            return ['status' => 'success', 'message' => 'Account deleted successfully.'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to delete account.'];
        }
    }

    public function isProfileComplete($user_id)
    {
        $query = "SELECT full_name FROM " . $this->profiles_table . " WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);

        // A profile is considered complete if the full_name is not empty.
        if ($profile && !empty($profile['full_name'])) {
            return true;
        }
        return false;
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
                return ['status' => 'error', 'message' => 'Server error: Could not create upload directory. Check permissions.'];
            }
        }
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return ['status' => 'success', 'filepath' => $destination];
        }
        return ['status' => 'error', 'message' => 'Server error: Could not move uploaded file. Check permissions.'];
    }
}
