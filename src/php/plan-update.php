<?php
include 'conn.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    if (!isset($_POST['PlanID']) || !isset($_POST['Title']) || !isset($_POST['Department']) || 
        !isset($_POST['ScheduledDate']) || !isset($_POST['Status']) || !isset($_POST['Description'])) {
        throw new Exception('Missing required fields');
    }

    $id = $_POST['PlanID'];
    $title = $_POST['Title'];
    $dept = $_POST['Department'];
    $date = $_POST['ScheduledDate'];
    $status = $_POST['Status'];
    $desc = $_POST['Description'];

    // Start transaction
    $conn->begin_transaction();

    $stmt = $conn->prepare("UPDATE auditplan SET Title=?, Department=?, ScheduledDate=?, Status=?, Description=? WHERE PlanID=?");
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }

    $stmt->bind_param("sssssi", $title, $dept, $date, $status, $desc, $id);
    if (!$stmt->execute()) {
        throw new Exception('Failed to update plan: ' . $stmt->error);
    }

    if ($stmt->affected_rows === 0) {
        throw new Exception('Plan not found or no changes made');
    }

    // Log the action
    $action = "Update Plan";
    $conductedBy = "System"; // Or use session user if available
    $details = "PlanID $id updated: Title=$title, Department=$dept, ScheduledDate=$date, Status=$status";
    $logStmt = $conn->prepare("INSERT INTO auditlogs (AuditID, Action, ConductedBy, ConductedAt, Details) VALUES (?, ?, ?, NOW(), ?)");
    if (!$logStmt) {
        throw new Exception('Failed to prepare log statement: ' . $conn->error);
    }

    $nullAuditID = null;
    $logStmt->bind_param("isss", $nullAuditID, $action, $conductedBy, $details);
    if (!$logStmt->execute()) {
        throw new Exception('Failed to log action: ' . $logStmt->error);
    }

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Plan updated successfully',
        'data' => [
            'planID' => $id,
            'title' => $title,
            'department' => $dept,
            'scheduledDate' => $date,
            'status' => $status,
            'description' => $desc
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
    if (isset($conn)) $conn->close();
}
?>
