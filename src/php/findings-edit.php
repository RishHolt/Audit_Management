<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $findingID = $_POST['FindingID'];
    $category = $_POST['Category'];
    $description = $_POST['Description'];

    $stmt = $conn->prepare("UPDATE findings SET Category=?, Description=? WHERE FindingID=?");
    $stmt->bind_param("ssi", $category, $description, $findingID);
    $stmt->execute();

    // Log the action
    $action = "Edit Finding";
    $conductedBy = "System"; // Or use session user if available
    $details = "FindingID $findingID updated: Category=$category, Description=$description";
    $logStmt = $conn->prepare("INSERT INTO auditlogs (AuditID, Action, ConductedBy, ConductedAt, Details) VALUES (?, ?, ?, NOW(), ?)");
    // Get AuditID for this finding
    $auditID = null;
    $result = $conn->query("SELECT AuditID FROM findings WHERE FindingID=" . intval($findingID));
    if ($result && $row = $result->fetch_assoc()) {
        $auditID = $row['AuditID'];
    }
    $logStmt->bind_param("isss", $auditID, $action, $conductedBy, $details);
    $logStmt->execute();
    $logStmt->close();

    header("Location: ../audit-findings.php");
    exit;
}
?>