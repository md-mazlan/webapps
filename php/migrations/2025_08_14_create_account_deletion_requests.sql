-- Migration: Create account_deletion_requests table
CREATE TABLE IF NOT EXISTS account_deletion_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    reason TEXT NOT NULL,
    requested_at DATETIME NOT NULL,
    reviewed TINYINT(1) DEFAULT 0,
    reviewed_at DATETIME DEFAULT NULL,
    admin_id INT DEFAULT NULL,
    decision VARCHAR(20) DEFAULT NULL, -- e.g. 'approved', 'rejected'
    response TEXT DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
