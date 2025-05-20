<?php
// Start session for CSRF protection
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'logs2_audit_management');

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Helper Functions
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function getPaginatedResults($query, $page = 1, $perPage = 10) {
    global $conn;
    $offset = ($page - 1) * $perPage;
    
    // Get total count
    $countQuery = "SELECT COUNT(*) as total FROM (" . $query . ") as subquery";
    $totalResult = $conn->query($countQuery);
    $total = $totalResult->fetch_assoc()['total'];
    
    // Get paginated results
    $result = $conn->query($query . " LIMIT $perPage OFFSET $offset");
    $data = $result->fetch_all(MYSQLI_ASSOC);
    
    return [
        'data' => $data,
        'total' => $total,
        'pages' => ceil($total / $perPage),
        'current_page' => $page
    ];
}

function validateCSRF() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            header('Content-Type: application/json');
            http_response_code(403);
            die(json_encode([
                'success' => false,
                'message' => 'Invalid CSRF token'
            ]));
        }
    }
}

function jsonResponse($data, $status = 200) {
    header('Content-Type: application/json');
    http_response_code($status);
    echo json_encode($data);
    exit;
}

// Error handling
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to ensure proper encoding
    if (!$conn->set_charset("utf8mb4")) {
        throw new Exception("Error setting charset: " . $conn->error);
    }

    // Set timezone
    date_default_timezone_set('Asia/Manila');
    
} catch (Exception $e) {
    // Log the error (in production, use proper logging)
    error_log("Database connection error: " . $e->getMessage());
    
    // If it's an AJAX request, return JSON
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        jsonResponse([
            'success' => false,
            'message' => 'Database connection error. Please try again later.'
        ], 500);
    }
    
    // For regular requests, show a user-friendly error
    die("We're experiencing technical difficulties. Please try again later.");
}

// Add CSRF token to all forms automatically
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('X-CSRF-Token: ' . $_SESSION['csrf_token']);
}
?>
