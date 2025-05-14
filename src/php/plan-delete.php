<?php
include 'conn.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM auditplan WHERE PlanID = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Log the action
        $action = "Delete Plan";
        $conductedBy = "System"; // Or use session user if available
        $details = "PlanID $id deleted";
        $logStmt = $conn->prepare("INSERT INTO auditlogs (AuditID, Action, ConductedBy, ConductedAt, Details) VALUES (?, ?, ?, NOW(), ?)");
        $nullAuditID = null;
        $logStmt->bind_param("isss", $nullAuditID, $action, $conductedBy, $details);
        $logStmt->execute();
        $logStmt->close();

        header("Location: ../audit-plan.php?deleted=1");
    } else {
        echo "Delete failed: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
