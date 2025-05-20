<?php
header('Content-Type: application/json');
include 'conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get POST data
    $title = $_POST['Title'];
    $department = $_POST['Department'];
    $scheduledDate = $_POST['ScheduledDate'];
    $description = $_POST['Description'] ?? '';
    $status = 'Scheduled';

    // Validate required fields
    if (empty($title) || empty($department) || empty($scheduledDate)) {
        http_response_code(400);
        echo json_encode(['error' => 'All fields are required']);
        exit;
    }

    // Prepare SQL query
    $sql = "INSERT INTO auditplan (Title, Department, ScheduledDate, Status, Description) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $title, $department, $scheduledDate, $status, $description);

    // Execute and confirm
    if ($stmt->execute()) {
        // Log the action
        $planID = $conn->insert_id;
        $action = "Create Plan";
        $conductedBy = "System"; // Or use session user if available
        $details = "PlanID $planID created: $title ($department) scheduled for $scheduledDate";
        $logStmt = $conn->prepare("INSERT INTO auditlogs (AuditID, Action, ConductedBy, ConductedAt, Details) VALUES (?, ?, ?, NOW(), ?)");
        $nullAuditID = null;
        $logStmt->bind_param("isss", $nullAuditID, $action, $conductedBy, $details);
        $logStmt->execute();
        $logStmt->close();
        
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Plan created successfully']);
    } else {
        throw new Exception($stmt->error);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>