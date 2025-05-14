<?php
include 'conn.php';

if (isset($_GET['id'])) {
    $auditID = intval($_GET['id']);

    // Get PlanID for logging (optional)
    $planID = null;
    $result = $conn->query("SELECT PlanID FROM audit WHERE AuditID = $auditID");
    if ($result && $row = $result->fetch_assoc()) {
        $planID = $row['PlanID'];
    }

    // Only log if the audit exists
    if ($planID !== null) {
        // Log the action to auditlogs, but set AuditID to NULL to avoid foreign key constraint
        $action = "Delete Audit";
        $conductedBy = "System"; // Or use session user if available
        $details = "AuditID $auditID deleted" . ($planID ? " (PlanID $planID)" : "");
        $logStmt = $conn->prepare("INSERT INTO auditlogs (AuditID, Action, ConductedBy, ConductedAt, Details) VALUES (?, ?, ?, NOW(), ?)");
        $nullAuditID = null;
        $logStmt->bind_param("isss", $nullAuditID, $action, $conductedBy, $details);
        $logStmt->execute();
        $logStmt->close();
    }

    // Delete all logs referencing this audit to satisfy the foreign key constraint
    $stmtLogs = $conn->prepare("DELETE FROM auditlogs WHERE AuditID = ?");
    $stmtLogs->bind_param("i", $auditID);
    $stmtLogs->execute();
    $stmtLogs->close();

    $stmt = $conn->prepare("DELETE FROM audit WHERE AuditID = ?");
    $stmt->bind_param("i", $auditID);
    $stmt->execute();
    $stmt->close();
}

header("Location: ../audit-conduct.php");
exit;
?>