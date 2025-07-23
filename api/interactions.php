<?php
// Start the session at the very beginning to access session data.
session_start();

// This file acts as a central API for handling user interactions like likes and comments.
require_once 'auth_check.php';
require_once 'database.php';
require_once 'content.php';

header('Content-Type: application/json');

/**
 * InteractionController Class
 *
 * Handles processing user interactions such as likes and comments by acting
 * as a controller that receives API requests and routes them appropriately.
 */
class InteractionController {
    private $db;
    private $content;
    private $user_id;

    /**
     * Constructor to initialize the controller with a database connection.
     * @param PDO $db The active database connection.
     */
    public function __construct($db) {
        $this->db = $db;
        $this->content = new Content($db);
        // Now that the session has been started, this will be correctly populated.
        $this->user_id = $_SESSION['user_id'] ?? null;
    }

    /**
     * Main handler for all incoming requests.
     * It validates the request and routes it to the appropriate private method.
     */
    public function handleRequest() {
        // Check if the user is logged in.
        if (!$this->user_id) {
            $this->sendResponse(['message' => 'You must be logged in to perform this action.'], 401);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $action = $data['action'] ?? '';
        $content_id = isset($data['content_id']) ? (int)$data['content_id'] : 0;

        if (!$content_id) {
            $this->sendResponse(['message' => 'Invalid content ID.'], 400);
            return;
        }

        switch ($action) {
            case 'toggle_like':
                $this->toggleLike($content_id);
                break;
            case 'add_comment':
                $comment_text = $data['comment_text'] ?? '';
                $this->addComment($content_id, $comment_text);
                break;
            default:
                $this->sendResponse(['message' => 'Invalid action specified.'], 400);
                break;
        }
    }

    /**
     * Handles the logic for liking or unliking a piece of content.
     * @param int $content_id The ID of the content.
     */
    private function toggleLike($content_id) {
        if ($this->content->hasUserLiked($content_id, $this->user_id)) {
            if ($this->content->removeLike($content_id, $this->user_id)) {
                $this->sendResponse(['action' => 'unliked']);
            } else {
                $this->sendResponse(['message' => 'Failed to unlike content.']);
            }
        } else {
            if ($this->content->addLike($content_id, $this->user_id)) {
                $this->sendResponse(['action' => 'liked']);
            } else {
                $this->sendResponse(['message' => 'Failed to like content.']);
            }
        }
    }

    /**
     * Handles the logic for adding a new comment.
     * @param int $content_id The ID of the content.
     * @param string $comment_text The text of the comment.
     */
    private function addComment($content_id, $comment_text) {
        if (empty(trim($comment_text))) {
            $this->sendResponse(['message' => 'Comment cannot be empty.']);
            return;
        }

        if ($this->content->addComment($content_id, $this->user_id, $comment_text)) {
            $this->sendResponse([
                'message' => 'Comment added successfully.',
                'comment' => [
                    'username' => $_SESSION['username'],
                    'comment' => htmlspecialchars($comment_text),
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ]);
        } else {
            $this->sendResponse(['message' => 'Failed to add comment.']);
        }
    }

    /**
     * A helper function to send a JSON response with a status code.
     * @param array $data The data to be encoded as JSON.
     * @param int $statusCode The HTTP status code to send.
     */
    private function sendResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        // Ensure 'status' is set for consistency.
        if (!isset($data['status'])) {
            $data['status'] = ($statusCode >= 200 && $statusCode < 300) ? 'success' : 'error';
        }
        echo json_encode($data);
    }
}

// --- Script Execution ---

// Initialize database connection.
$database = new Database();
$db = $database->connect();

// Create a controller instance and handle the request.
$controller = new InteractionController($db);
$controller->handleRequest();
?>
