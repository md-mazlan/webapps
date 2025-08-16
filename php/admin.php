<?php

/**
 * Admin Class
 *
 * Manages admin data, authentication logic, and provides methods
 * for viewing and managing public users.
 */
class Admin
{
    // --- Admin Approval Methods ---
    public function getInactiveAdmins()
    {
        $query = "SELECT id, username, email, created_at FROM " . $this->table_name . " WHERE active = 0 ORDER BY created_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function activateAdmin($adminId)
    {
        $query = "UPDATE " . $this->table_name . " SET active = 1 WHERE id = :admin_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':admin_id', $adminId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    private $conn;
    // Table names
    private $table_name = 'admins';
    private $tokens_table = 'admin_tokens';
    private $users_table = 'users'; // Reference to the public users table
    private $profiles_table = 'user_profiles'; // Reference to the profiles table
    private $content_table = 'content';
    private $likes_table = 'likes';
    private $comments_table = 'comments';

    public $id;
    public $username;
    public $email;
    public $password;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // --- Admin Authentication Methods (Unchanged) ---
    public function register()
    {
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));
        if ($this->adminExists()) {
            return false;
        }
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        // Set active to 0 (inactive) by default
        $query = "INSERT INTO " . $this->table_name . " (username, email, password, active) VALUES (:username, :email, :password, 0)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $password_hash);
        return $stmt->execute();
    }
    public function login()
    {
        $this->username = htmlspecialchars(strip_tags($this->username));
        // Only allow login if active = 1
        $query = "SELECT id, username, password, active FROM " . $this->table_name . " WHERE username = :username LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $this->username);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row['active'] == 1 && password_verify($this->password, $row['password'])) {
                $this->id = $row['id'];
                $this->username = $row['username'];
                return true;
            }
        }
        return false;
    }
    private function adminExists()
    {
        $query = "SELECT id FROM " . $this->table_name . " WHERE username = :username OR email = :email LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
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
        $query = "SELECT u.id, u.username, t.id as token_id FROM " . $this->tokens_table . " t JOIN " . $this->table_name . " u ON t.user_id = u.id WHERE t.token = :token AND t.expires_at > NOW() LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token_hash);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->username = $row['username'];
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
    public function getActiveSessions($currentToken = '')
    {
        if (empty($this->id)) {
            return [];
        }
        $currentTokenHash = !empty($currentToken) ? hash('sha256', $currentToken) : '';
        $query = "SELECT token, device_info, ip_address, last_used_at, (CASE WHEN token = :current_token_hash THEN 1 ELSE 0 END) as is_current_session FROM " . $this->tokens_table . " WHERE user_id = :user_id AND expires_at > NOW() ORDER BY is_current_session DESC, last_used_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->id);
        $stmt->bindParam(':current_token_hash', $currentTokenHash);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- User Management Methods ---
    public function getAllUsers($limit = 10, $offset = 0, $searchTerm = null)
    {
        $query = "SELECT u.id, u.username, u.email, u.nric, u.created_at, p.profile_pic_url,
                  (SELECT COUNT(*) FROM " . $this->likes_table . " l WHERE l.user_id = u.id) as like_count,
                  (SELECT COUNT(*) FROM " . $this->comments_table . " co WHERE co.user_id = u.id) as comment_count
                  FROM " . $this->users_table . " u
                  LEFT JOIN " . $this->profiles_table . " p ON u.id = p.user_id";
        $whereClause = '';
        if (!empty($searchTerm)) {
            $whereClause = " WHERE u.username LIKE :searchTerm OR u.email LIKE :searchTerm OR u.nric LIKE :searchTerm";
        }
        $query .= $whereClause . " ORDER BY u.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);

        if (!empty($searchTerm)) {
            $searchTerm = "%" . $searchTerm . "%";
            $stmt->bindParam(':searchTerm', $searchTerm);
        }

        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById($user_id)
    {
        $query = "SELECT id, username, email, nric FROM " . $this->users_table . " WHERE id = :user_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateUser($user_id, $data)
    {
        if (empty($user_id) || empty($data)) {
            return false;
        }

        $query = "UPDATE " . $this->users_table . " SET username = :username, email = :email, nric = :nric WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);

        $username = htmlspecialchars(strip_tags($data['username']));
        $email = htmlspecialchars(strip_tags($data['email']));
        $nric = htmlspecialchars(strip_tags($data['nric']));

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':nric', $nric);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getUsersForExport(array $columns = ['id', 'username', 'email', 'created_at'])
    {
        $columnMap = [
            'id' => 'u.id',
            'username' => 'u.username',
            'email' => 'u.email',
            'nric' => 'u.nric',
            'created_at' => 'u.created_at',
            'full_name' => 'p.full_name',
            'bio' => 'p.bio',
            'profile_pic_url' => 'p.profile_pic_url',
            'company' => 'e.company',
            'job_title' => 'e.job_title',
            'start_date' => 'e.start_date',
            'end_date' => 'e.end_date',
            'is_current' => 'e.is_current'
        ];

        $selectedColumnsSql = [];
        foreach ($columns as $col) {
            if (isset($columnMap[$col])) {
                $selectedColumnsSql[] = $columnMap[$col] . ' AS ' . $col;
            }
        }

        if (empty($selectedColumnsSql)) {
            $selectedColumnsSql = ['u.id AS id', 'u.username AS username', 'u.email AS email', 'u.created_at AS created_at'];
        }

        $columnString = implode(', ', $selectedColumnsSql);

        $query = "
            SELECT {$columnString} 
            FROM {$this->users_table} u
            LEFT JOIN user_profiles p ON u.id = p.user_id
            LEFT JOIN user_employment e ON u.id = e.user_id
            ORDER BY u.created_at DESC
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function deleteUser($user_id)
    {
        if (empty($user_id)) {
            return false;
        }
        $query = "DELETE FROM " . $this->users_table . " WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // --- Dashboard Stats Methods ---
    public function getTotalUsersCount($searchTerm = null)
    {
        $query = "SELECT COUNT(*) as count FROM " . $this->users_table;
        $whereClause = '';
        if (!empty($searchTerm)) {
            $whereClause = " WHERE username LIKE :searchTerm OR email LIKE :searchTerm OR nric LIKE :searchTerm";
        }
        $query .= $whereClause;

        $stmt = $this->conn->prepare($query);

        if (!empty($searchTerm)) {
            $searchTerm = "%" . $searchTerm . "%";
            $stmt->bindParam(':searchTerm', $searchTerm);
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['count'] ?? 0);
    }
    public function getTotalContentCount()
    {
        $query = "SELECT COUNT(*) as count FROM " . $this->content_table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['count'] ?? 0);
    }
    public function getTotalLikesCount()
    {
        $query = "SELECT COUNT(*) as count FROM " . $this->likes_table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['count'] ?? 0);
    }
    public function getTotalCommentsCount()
    {
        $query = "SELECT COUNT(*) as count FROM " . $this->comments_table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['count'] ?? 0);
    }
}
