<?php
include 'conn.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!isset($_POST['ActionID'])) {
            throw new Exception('Action ID is required');
        }

        $actionID = intval($_POST['ActionID']);
        if ($actionID <= 0) {
            throw new Exception('Invalid Action ID');
        }

        // Start transaction
        $conn->begin_transaction();

        // Get FindingID and AuditID for logging
        $checkStmt = $conn->prepare("SELECT ca.FindingID, f.AuditID 
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
        $findingID = $row['FindingID'];
        $auditID = $row['AuditID'];

        // Delete action
        $stmt = $conn->prepare("DELETE FROM correctiveactions WHERE ActionID = ?");
        $stmt->bind_param("i", $actionID);
        if (!$stmt->execute()) {
            throw new Exception('Failed to delete action');
        }

        if ($stmt->affected_rows === 0) {
            throw new Exception('Action not found or already deleted');
        }

        // Log the action
        $action = "Delete Action";
        $conductedBy = "System";
        $details = "ActionID $actionID deleted (FindingID: $findingID)";
        $logStmt = $conn->prepare("INSERT INTO auditlogs (AuditID, Action, ConductedBy, ConductedAt, Details) VALUES (?, ?, ?, NOW(), ?)");
        $logStmt->bind_param("isss", $auditID, $action, $conductedBy, $details);
        if (!$logStmt->execute()) {
            throw new Exception('Failed to log action deletion');
        }

        // Commit transaction
        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Action deleted successfully'
        ]);

    } catch (Exception $e) {
        if (isset($conn) && $conn->connect_errno === 0) {
            $conn->rollback();
        }
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    } finally {
        if (isset($logStmt)) $logStmt->close();
        if (isset($stmt)) $stmt->close();
        if (isset($checkStmt)) $checkStmt->close();
        if (isset($conn)) $conn->close();
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}