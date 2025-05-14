<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auditID = $_POST['AuditID'];
    $category = $_POST['Category'];
    $description = $_POST['Description'];
    $loggedAt = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO findings (AuditID, Category, Description, LoggedAt) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $auditID, $category, $description, $loggedAt);
    $stmt->execute();

    // Log the action
    $action = "Log Finding";
    $conductedBy = "System"; // Or use session user if available
    $details = "Finding logged for AuditID: $auditID, Category: $category";
    $logStmt = $conn->prepare("INSERT INTO auditlogs (AuditID, Action, ConductedBy, ConductedAt, Details) VALUES (?, ?, ?, NOW(), ?)");
    $logStmt->bind_param("isss", $auditID, $action, $conductedBy, $details);
    $logStmt->execute();
    $logStmt->close();

    header("Location: ../audit-findings.php");
    exit;
}
?>