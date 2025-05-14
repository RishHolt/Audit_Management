<?php
include 'conn.php';

if (isset($_GET['id'])) {
    $actionID = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM correctiveactions WHERE ActionID=?");
    $stmt->bind_param("i", $actionID);
    $stmt->execute();

    // Log the action
    $action = "Delete Action";
    $conductedBy = "System"; // Or use session user if available
    $details = "ActionID $actionID deleted";
    $logStmt = $conn->prepare("INSERT INTO auditlogs (AuditID, Action, ConductedBy, ConductedAt, Details) VALUES (?, ?, ?, NOW(), ?)");
    $nullAuditID = null;
    $logStmt->bind_param("isss", $nullAuditID, $action, $conductedBy, $details);
    $logStmt->execute();
    $logStmt->close();
}

header("Location: ../audit-actions.php");
exit;
?>