<?php
/**
 * Admin Class
 *
 * Manages admin data and authentication logic.
 * The table name used is 'admins'.
 */
class Admin {
    private $conn;
    // Table names
    private $table_name = 'admins';
    private $tokens_table = 'auth_tokens';

    public $id;
    public $username;
    public $email;
    public $password;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Registers a new admin.
     * @return bool True on success, false otherwise.
     */
    public function register() {
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));

        if ($this->adminExists()) {
            return false;
        }

        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $query = "INSERT INTO " . $this->table_name . " (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $password_hash);

        return $stmt->execute();
    }

    /**
     * Authenticates an admin.
     * @return bool True on success, false otherwise.
     */
    public function login() {
        $this->username = htmlspecialchars(strip_tags($this->username));
        $query = "SELECT id, username, password FROM " . $this->table_name . " WHERE username = :username LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $this->username);
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
    
    /**
     * Checks if an admin with the given username or email already exists.
     * @return bool True if the admin exists, false otherwise.
     */
    private function adminExists() {
        $query = "SELECT id FROM " . $this->table_name . " WHERE username = :username OR email = :email LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Sets a "Remember Me" token for the admin.
     * @return string|false The token on success, false on failure.
     */
    public function setRememberToken() {
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
        if ($stmt->execute()) { return $token; }
        return false;
    }

    /**
     * Validates a "Remember Me" token.
     * @param string $token The token to validate.
     * @return bool True if valid, false otherwise.
     */
    public function validateRememberToken($token) {
        if (empty($token)) { return false; }
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
    
    /**
     * Clears a "Remember Me" token upon logout.
     * @param string $token The token to clear.
     * @return bool True on success, false otherwise.
     */
    public function clearRememberToken($token) {
        if (empty($token)) { return false; }
        $token_hash = hash('sha256', $token);
        $query = "DELETE FROM " . $this->tokens_table . " WHERE token = :token";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token_hash);
        return $stmt->execute();
    }

    /**
     * Retrieves all active sessions for the current admin.
     * @param string $currentToken The current session's token.
     * @return array A list of active sessions.
     */
    public function getActiveSessions($currentToken = '') {
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
}
?>
