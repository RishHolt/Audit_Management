<?php

include 'conn.php';

// Get POST data
$title = $_POST['Title'];
$department = $_POST['Department'];
$scheduled_date = $_POST['ScheduledDate'];
$description = $_POST['Description'];

// Prepare SQL query
$sql = "INSERT INTO auditplan (Title, Department, ScheduledDate, Description) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $title, $department, $scheduled_date, $description);

// Execute and confirm
if ($stmt->execute()) {
    // Log the action
    $planID = $conn->insert_id;
    $action = "Create Plan";
    $conductedBy = "System"; // Or use session user if available
    $details = "PlanID $planID created: $title ($department) scheduled for $scheduled_date";
    $logStmt = $conn->prepare("INSERT INTO auditlogs (AuditID, Action, ConductedBy, ConductedAt, Details) VALUES (?, ?, ?, NOW(), ?)");
    $nullAuditID = null;
    $logStmt->bind_param("isss", $nullAuditID, $action, $conductedBy, $details);
    $logStmt->execute();
    $logStmt->close();
    echo "Record submitted successfully.";
} else {
    echo "Error: " . $stmt->error;
}

header("Location: ../audit-plan.php");

$stmt->close();
$conn->close();
?>