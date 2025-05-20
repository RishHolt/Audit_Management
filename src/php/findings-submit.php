<?php
include 'conn.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    if (!isset($_POST['AuditID']) || !isset($_POST['Category']) || !isset($_POST['Description'])) {
        throw new Exception('Missing required fields');
    }

    $auditID = $_POST['AuditID'];
    $category = $_POST['Category'];
    $description = $_POST['Description'];
    $loggedAt = date('Y-m-d H:i:s');

    // Validate category
    $validCategories = ['Compliant', 'Non-Compliant', 'Observation'];
    if (!in_array($category, $validCategories)) {
        throw new Exception('Invalid category');
    }

    // Start transaction
    $conn->begin_transaction();

    $stmt = $conn->prepare("INSERT INTO findings (AuditID, Category, Description, LoggedAt) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }

    $stmt->bind_param("isss", $auditID, $category, $description, $loggedAt);
    if (!$stmt->execute()) {
        throw new Exception('Failed to insert finding: ' . $stmt->error);
    }
    $findingID = $stmt->insert_id;

    // Log the action
    $action = "Log Finding";
    $conductedBy = "System"; // Or use session user if available
    $details = "Finding logged for AuditID: $auditID, Category: $category";
    $logStmt = $conn->prepare("INSERT INTO auditlogs (AuditID, Action, ConductedBy, ConductedAt, Details) VALUES (?, ?, ?, NOW(), ?)");
    if (!$logStmt) {
        throw new Exception('Failed to prepare log statement: ' . $conn->error);
    }

    $logStmt->bind_param("isss", $auditID, $action, $conductedBy, $details);
    if (!$logStmt->execute()) {
        throw new Exception('Failed to log action: ' . $logStmt->error);
    }

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Finding logged successfully',
        'data' => [
            'findingID' => $findingID,
            'auditID' => $auditID,
            'category' => $category,
            'description' => $description,
            'loggedAt' => $loggedAt
        ]
    ]);

} catch (Exception $e) {
    if (isset($conn) && $conn->connect_errno === 0) {
        $conn->rollback();
    }
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($logStmt)) $logStmt->close();
    if (isset($stmt)) $stmt->close();
}
?>