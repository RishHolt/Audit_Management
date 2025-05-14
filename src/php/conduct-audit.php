<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $planID = $_POST['PlanID'];
    $conductingBy = $_POST['ConductingBy'];
    $status = 'Pending';
    $conductedAt = date('Y-m-d H:i:s'); // Current date and time

    // Insert into audit table (adjust table name if needed)
    $stmt = $conn->prepare("INSERT INTO audit (PlanID, ConductingBy, ConductedAt, Status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $planID, $conductingBy, $conductedAt, $status);
    $stmt->execute();

    // Get the new AuditID
    $newAuditID = $conn->insert_id;

    // Update auditplan status to Assigned
    $conn->query("UPDATE auditplan SET Status='Assigned' WHERE PlanID=$planID");

    // Log the action
    $action = "Conduct Audit";
    $details = "Audit conducted for PlanID: " . $planID;
    $logStmt = $conn->prepare("INSERT INTO auditlogs (AuditID, Action, ConductedBy, ConductedAt, Details) VALUES (?, ?, ?, NOW(), ?)");
    $logStmt->bind_param("isss", $newAuditID, $action, $conductingBy, $details);
    $logStmt->execute();
    $logStmt->close();

    header("Location: ../audit-conduct.php");
    exit;
}
?>