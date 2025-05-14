<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ActionID'])) {
    $actionID = intval($_POST['ActionID']);

    // Mark the action as Completed
    $stmt = $conn->prepare("UPDATE correctiveactions SET Status='Completed' WHERE ActionID=?");
    $stmt->bind_param("i", $actionID);
    $stmt->execute();

    // Log the action
    $action = "Complete Action";
    $conductedBy = "System"; // Or use session user if available
    $details = "ActionID $actionID marked as completed";
    $logStmt = $conn->prepare("INSERT INTO auditlogs (AuditID, Action, ConductedBy, ConductedAt, Details) VALUES (?, ?, ?, NOW(), ?)");
    $nullAuditID = null;
    $logStmt->bind_param("isss", $nullAuditID, $action, $conductedBy, $details);
    $logStmt->execute();
    $logStmt->close();

    // Get the related FindingID
    $result = $conn->query("SELECT FindingID FROM correctiveactions WHERE ActionID=$actionID");
    if ($result && $row = $result->fetch_assoc()) {
        $findingID = $row['FindingID'];
        // Update the finding's category to Compliant
        $stmt2 = $conn->prepare("UPDATE findings SET Category='Compliant' WHERE FindingID=?");
        $stmt2->bind_param("i", $findingID);
        $stmt2->execute();
    }
}

header("Location: ../audit-actions.php");
exit;
?>