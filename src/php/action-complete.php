<?php
include 'conn.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ActionID'])) {
    try {
        $actionID = intval($_POST['ActionID']);
        
        // Start transaction
        $conn->begin_transaction();
        
        // Check if action exists and is in Under Review status
        $checkStmt = $conn->prepare("SELECT ca.Status, ca.FindingID, f.AuditID 
            FROM correctiveactions ca 
            JOIN findings f ON ca.FindingID = f.FindingID 
            WHERE ca.ActionID = ?");
        $checkStmt->bind_param("i", $actionID);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('Action not found');
        }
        
        $row = $result->fetch_assoc();
        if ($row['Status'] !== 'Under Review') {
            throw new Exception('Action must be Under Review to be completed');
        }

        $findingID = $row['FindingID'];
        $auditID = $row['AuditID'];
        
        // Update action status to Completed
        $stmt = $conn->prepare("UPDATE correctiveactions SET Status = 'Completed' WHERE ActionID = ?");
        $stmt->bind_param("i", $actionID);
        if (!$stmt->execute()) {
            throw new Exception('Failed to update action status');
        }
        
        // Update finding category to Compliant
        $findingStmt = $conn->prepare("UPDATE findings SET Category = 'Compliant' WHERE FindingID = ?");
        $findingStmt->bind_param("i", $findingID);
        if (!$findingStmt->execute()) {
            throw new Exception('Failed to update finding category');
        }
        
        // Update audit status to Under Review
        $auditStmt = $conn->prepare("UPDATE audit SET Status = 'Under Review' WHERE AuditID = ?");
        $auditStmt->bind_param("i", $auditID);
        if (!$auditStmt->execute()) {
            throw new Exception('Failed to update audit status');
        }

        // Log the actions
        $conductedBy = "System"; // Or use session user if available
        
        // Log action completion
        $action1 = "Complete Action";
        $details1 = "ActionID $actionID marked as completed";
        $logStmt1 = $conn->prepare("INSERT INTO auditlogs (AuditID, Action, ConductedBy, ConductedAt, Details) VALUES (?, ?, ?, NOW(), ?)");
        $logStmt1->bind_param("isss", $auditID, $action1, $conductedBy, $details1);
        if (!$logStmt1->execute()) {
            throw new Exception('Failed to log action completion');
        }
        
        // Log finding update
        $action2 = "Update Finding";
        $details2 = "FindingID $findingID category updated to Compliant";
        $logStmt2 = $conn->prepare("INSERT INTO auditlogs (AuditID, Action, ConductedBy, ConductedAt, Details) VALUES (?, ?, ?, NOW(), ?)");
        $logStmt2->bind_param("isss", $auditID, $action2, $conductedBy, $details2);
        if (!$logStmt2->execute()) {
            throw new Exception('Failed to log finding update');
        }
        
        // Log audit update
        $action3 = "Update Audit";
        $details3 = "AuditID $auditID status updated to Under Review";
        $logStmt3 = $conn->prepare("INSERT INTO auditlogs (AuditID, Action, ConductedBy, ConductedAt, Details) VALUES (?, ?, ?, NOW(), ?)");
        $logStmt3->bind_param("isss", $auditID, $action3, $conductedBy, $details3);
        if (!$logStmt3->execute()) {
            throw new Exception('Failed to log audit update');
        }

        // Commit transaction
        $conn->commit();
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Action marked as completed successfully'
        ]);

    } catch (Exception $e) {
        if (isset($conn) && $conn->connect_errno === 0) {
            $conn->rollback();
        }
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    } finally {
        if (isset($logStmt1)) $logStmt1->close();
        if (isset($logStmt2)) $logStmt2->close();
        if (isset($logStmt3)) $logStmt3->close();
        if (isset($auditStmt)) $auditStmt->close();
        if (isset($findingStmt)) $findingStmt->close();
        if (isset($stmt)) $stmt->close();
        if (isset($checkStmt)) $checkStmt->close();
        if (isset($conn)) $conn->close();
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request'
    ]);
}
?>