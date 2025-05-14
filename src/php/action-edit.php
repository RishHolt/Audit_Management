<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ActionID'])) {
    $actionID = intval($_POST['ActionID']);
    $assignedTo = trim($_POST['AssignedTo']);
    $task = trim($_POST['Task']);
    $dueDate = $_POST['DueDate'];
    $status = $_POST['Status'];

    $stmt = $conn->prepare("UPDATE correctiveactions SET AssignedTo=?, Task=?, DueDate=?, Status=? WHERE ActionID=?");
    $stmt->bind_param("ssssi", $assignedTo, $task, $dueDate, $status, $actionID);
    $stmt->execute();

    // Log the action
    $action = "Edit Action";
    $conductedBy = "System"; // Or use session user if available
    $details = "ActionID $actionID updated: AssignedTo=$assignedTo, Task=$task, DueDate=$dueDate, Status=$status";
    $logStmt = $conn->prepare("INSERT INTO auditlogs (AuditID, Action, ConductedBy, ConductedAt, Details) VALUES (?, ?, ?, NOW(), ?)");
    $nullAuditID = null;
    $logStmt->bind_param("isss", $nullAuditID, $action, $conductedBy, $details);
    $logStmt->execute();
    $logStmt->close();
}

header("Location: ../audit-actions.php");
exit;
?>