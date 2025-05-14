<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['AuditID'])) {
    $auditID = intval($_POST['AuditID']);

    // Update audit status to Completed
    $stmt = $conn->prepare("UPDATE audit SET Status='Completed' WHERE AuditID=?");
    $stmt->bind_param("i", $auditID);
    $stmt->execute();

    // Log the action for audit completion
    $action = "Mark Audit Complete";
    $conductedBy = "System"; // Or use session user if available
    $details = "AuditID $auditID marked as completed";
    $logStmt = $conn->prepare("INSERT INTO auditlogs (AuditID, Action, ConductedBy, ConductedAt, Details) VALUES (?, ?, ?, NOW(), ?)");
    $logStmt->bind_param("isss", $auditID, $action, $conductedBy, $details);
    $logStmt->execute();
    $logStmt->close();

    // Get the related PlanID
    $planResult = $conn->query("SELECT PlanID FROM audit WHERE AuditID=$auditID");
    if ($planResult && $planRow = $planResult->fetch_assoc()) {
        $planID = $planRow['PlanID'];
        // Check if all audits for this plan are completed
        $check = $conn->query("SELECT COUNT(*) as cnt FROM audit WHERE PlanID=$planID AND Status != 'Completed'");
        $row = $check->fetch_assoc();
        if ($row['cnt'] == 0) {
            // All audits completed, mark plan as completed
            $stmt2 = $conn->prepare("UPDATE auditplan SET Status='Completed' WHERE PlanID=?");
            $stmt2->bind_param("i", $planID);
            $stmt2->execute();

            // Log the action for plan completion
            $action2 = "Mark Plan Complete";
            $details2 = "PlanID $planID marked as completed (all audits complete)";
            $logStmt2 = $conn->prepare("INSERT INTO auditlogs (AuditID, Action, ConductedBy, ConductedAt, Details) VALUES (?, ?, ?, NOW(), ?)");
            $nullAuditID = null;
            $logStmt2->bind_param("isss", $nullAuditID, $action2, $conductedBy, $details2);
            $logStmt2->execute();
            $logStmt2->close();
        }
    }
}

header("Location: ../audit-conduct.php");
exit;
?>