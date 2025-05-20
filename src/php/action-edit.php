<?php
include 'conn.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Validate required fields
    if (!isset($_POST['ActionID']) || !isset($_POST['AssignedTo']) || !isset($_POST['Task']) || 
        !isset($_POST['DueDate']) || !isset($_POST['Status'])) {
        throw new Exception('Missing required fields');
    }

    $actionID = intval($_POST['ActionID']);
    $assignedTo = trim($_POST['AssignedTo']);
    $task = trim($_POST['Task']);
    $dueDate = $_POST['DueDate'];
    $status = $_POST['Status'];

    // Validate data
    if ($actionID <= 0) {
        throw new Exception('Invalid Action ID');
    }
    if (empty($assignedTo)) {
        throw new Exception('Assigned To cannot be empty');
    }
    if (empty($task)) {
        throw new Exception('Task cannot be empty');
    }
    if (empty($dueDate)) {
        throw new Exception('Due Date cannot be empty');
    }

    // Validate date format
    $date = DateTime::createFromFormat('Y-m-d', $dueDate);
    if (!$date || $date->format('Y-m-d') !== $dueDate) {
        throw new Exception('Invalid date format');
    }

    // Validate status
    $validStatuses = ['Pending', 'Under Review', 'Failed'];
    if (!in_array($status, $validStatuses)) {
        throw new Exception('Invalid status');
    }

    // Start transaction
    $conn->begin_transaction();

    // Check if action exists
    $checkStmt = $conn->prepare("SELECT ActionID FROM correctiveactions WHERE ActionID = ?");
    if (!$checkStmt) {
        throw new Exception('Failed to prepare check statement: ' . $conn->error);
    }
    $checkStmt->bind_param("i", $actionID);
    if (!$checkStmt->execute()) {
        throw new Exception('Failed to check action: ' . $checkStmt->error);
    }
    $checkResult = $checkStmt->get_result();
    if ($checkResult->num_rows === 0) {
        throw new Exception('Action not found');
    }
    $checkStmt->close();

    // Update action
    $stmt = $conn->prepare("UPDATE correctiveactions SET AssignedTo=?, Task=?, DueDate=?, Status=? WHERE ActionID=?");
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }
    $stmt->bind_param("ssssi", $assignedTo, $task, $dueDate, $status, $actionID);
    if (!$stmt->execute()) {
        throw new Exception('Failed to update action: ' . $stmt->error);
    }

    if ($stmt->affected_rows === 0) {
        throw new Exception('No changes made to action');
    }

    // Log the action
    $action = "Edit Action";
    $conductedBy = "System"; // Or use session user if available
    $details = "ActionID $actionID updated: AssignedTo=$assignedTo, Task=$task, DueDate=$dueDate, Status=$status";
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
        'message' => 'Action updated successfully',
        'data' => [
            'actionID' => $actionID,
            'assignedTo' => $assignedTo,
            'task' => $task,
            'dueDate' => $dueDate,
            'status' => $status
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