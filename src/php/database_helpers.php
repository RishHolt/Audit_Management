<?php

/**
 * Database helper functions for the Audit Management System
 * This file contains all the shared database functionality
 */

// Helper Functions
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function getPaginatedResults($query, $page = 1, $perPage = 10, $database = 'main') {
    global $conn, $connFinancials;
    $connection = $database === 'financials' ? $connFinancials : $conn;
    $offset = ($page - 1) * $perPage;
    
    // Get total count
    $countQuery = "SELECT COUNT(*) as total FROM (" . $query . ") as subquery";
    $result = executeQuery($countQuery, $database);
    $total = $result->fetch_assoc()['total'];
    
    // Get paginated results
    $result = executeQuery($query . " LIMIT $perPage OFFSET $offset", $database);
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
            jsonResponse([
                'success' => false,
                'message' => 'Invalid CSRF token'
            ], 403);
        }
    }
}

function jsonResponse($data, $status = 200) {
    header('Content-Type: application/json');
    http_response_code($status);
    echo json_encode($data);
    exit;
}

/**
 * Execute a query that may involve both databases
 * @param string $query The SQL query to execute
 * @param string $database Which database to use ('main' or 'financials')
 * @return mysqli_result|bool The query result
 */
function executeQuery($query, $database = 'main') {
    global $conn, $connFinancials;
    
    try {
        $connection = $database === 'financials' ? $connFinancials : $conn;
        if (!$connection) {
            throw new Exception("Database connection not available");
        }

        $result = $connection->query($query);
        if ($result === false) {
            throw new Exception("Query failed: " . $connection->error);
        }
        
        return $result;
    } catch (Exception $e) {
        error_log("Query execution error: " . $e->getMessage());
        if (isAjaxRequest()) {
            jsonResponse([
                'success' => false,
                'message' => 'Database query error. Please try again later.'
            ], 500);
        }
        throw $e;
    }
}

function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

function initializeFinancialTables() {
    try {
        // Create financial_audit_gl table in financials database
        $createTableQuery = "CREATE TABLE IF NOT EXISTS financial_audit_gl (
            AuditID INT AUTO_INCREMENT PRIMARY KEY,
            EntryID INT NOT NULL,
            ReviewedBy VARCHAR(100),
            AuditDate DATE DEFAULT CURRENT_DATE,
            Findings TEXT,
            Status VARCHAR(20) DEFAULT 'Pending',
            Notes TEXT,
            CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UpdatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_entry (EntryID),
            INDEX idx_date (AuditDate),
            INDEX idx_status (Status)
        )";
        
        executeQuery($createTableQuery, 'financials');
        return true;
    } catch (Exception $e) {
        error_log("Failed to initialize financial tables: " . $e->getMessage());
        return false;    }
}

// Initialize tables when this file is included
initializeFinancialTables();
