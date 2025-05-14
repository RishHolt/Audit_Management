<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $findingID = intval($_POST['FindingID']);
    $assignedTo = trim($_POST['AssignedTo']);
    $task = trim($_POST['Task']);
    $dueDate = $_POST['DueDate'];

    $stmt = $conn->prepare("INSERT INTO correctiveactions (FindingID, AssignedTo, Task, DueDate, Status) VALUES (?, ?, ?, ?, 'Pending')");
    $stmt->bind_param("isss", $findingID, $assignedTo, $task, $dueDate);
    $stmt->execute();

    // Log the action
    $action = "Assign Action";
    $conductedBy = "System"; // Or use session user if available
    $details = "Action assigned to $assignedTo for FindingID: $findingID";
    $logStmt = $conn->prepare("INSERT INTO auditlogs (AuditID, Action, ConductedBy, ConductedAt, Details) VALUES (?, ?, ?, NOW(), ?)");
    $nullAuditID = null;
    $logStmt->bind_param("isss", $nullAuditID, $action, $conductedBy, $details);
    $logStmt->execute();
    $logStmt->close();
}

header("Location: ../audit-actions.php");
exit;
?>