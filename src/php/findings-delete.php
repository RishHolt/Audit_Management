<?php
include 'conn.php';

if (isset($_GET['id'])) {
    $findingID = intval($_GET['id']);
    // Get AuditID for logging
    $auditID = null;
    $result = $conn->query("SELECT AuditID FROM findings WHERE FindingID = $findingID");
    if ($result && $row = $result->fetch_assoc()) {
        $auditID = $row['AuditID'];
    }
    $stmt = $conn->prepare("DELETE FROM findings WHERE FindingID = ?");
    $stmt->bind_param("i", $findingID);
    $stmt->execute();

    // Log the action
    $action = "Delete Finding";
    $conductedBy = "System"; // Or use session user if available
    $details = "FindingID $findingID deleted";
    $logStmt = $conn->prepare("INSERT INTO auditlogs (AuditID, Action, ConductedBy, ConductedAt, Details) VALUES (?, ?, ?, NOW(), ?)");
    $logStmt->bind_param("isss", $auditID, $action, $conductedBy, $details);
    $logStmt->execute();
    $logStmt->close();
}

header("Location: ../audit-findings.php");
exit;
?>