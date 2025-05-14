<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['PlanID'];
    $title = $_POST['Title'];
    $dept = $_POST['Department'];
    $date = $_POST['ScheduledDate'];
    $status = $_POST['Status'];
    $desc = $_POST['Description'];

    $stmt = $conn->prepare("UPDATE auditplan SET Title=?, Department=?, ScheduledDate=?, Status=?, Description=? WHERE PlanID=?");
    $stmt->bind_param("sssssi", $title, $dept, $date, $status, $desc, $id);

    if ($stmt->execute()) {
        // Log the action
        $action = "Update Plan";
        $conductedBy = "System"; // Or use session user if available
        $details = "PlanID $id updated: Title=$title, Department=$dept, ScheduledDate=$date, Status=$status";
        $logStmt = $conn->prepare("INSERT INTO auditlogs (AuditID, Action, ConductedBy, ConductedAt, Details) VALUES (?, ?, ?, NOW(), ?)");
        $nullAuditID = null;
        $logStmt->bind_param("isss", $nullAuditID, $action, $conductedBy, $details);
        $logStmt->execute();
        $logStmt->close();

        header("Location: ../audit-plan.php?success=1");
    } else {
        echo "Update failed: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
