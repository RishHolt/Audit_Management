<?php
include 'conn.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['id'])) {
        throw new Exception('Finding ID is required');
    }

    $findingID = intval($_GET['id']);
    
    // Get AuditID for logging
    $auditID = null;
    $result = $conn->query("SELECT AuditID FROM findings WHERE FindingID = $findingID");
    if ($result && $row = $result->fetch_assoc()) {
        $auditID = $row['AuditID'];
    } else {
        throw new Exception('Finding not found');
    }

    // Start transaction
    $conn->begin_transaction();

    $stmt = $conn->prepare("DELETE FROM findings WHERE FindingID = ?");
    if (!$stmt) {
        throw new Exception('Failed to prepare delete statement: ' . $conn->error);
    }

    $stmt->bind_param("i", $findingID);
    if (!$stmt->execute()) {
        throw new Exception('Failed to delete finding: ' . $stmt->error);
    }

    if ($stmt->affected_rows === 0) {
        throw new Exception('Finding not found or already deleted');
    }

    // Log the action
    $action = "Delete Finding";
    $conductedBy = "System"; // Or use session user if available
    $details = "FindingID $findingID deleted";
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
        'message' => 'Finding deleted successfully'
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
    if (isset($conn)) $conn->close();
}
?>