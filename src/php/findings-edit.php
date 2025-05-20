<?php
include 'conn.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->begin_transaction();

        $findingID = $_POST['FindingID'];
        $category = $_POST['Category'];
        $description = $_POST['Description'];

        // Get the AuditID for this finding
        $auditStmt = $conn->prepare("SELECT AuditID FROM findings WHERE FindingID = ?");
        $auditStmt->bind_param("i", $findingID);
        $auditStmt->execute();
        $auditResult = $auditStmt->get_result();
        $auditRow = $auditResult->fetch_assoc();
        $auditID = $auditRow['AuditID'];

        // Update finding
        $stmt = $conn->prepare("UPDATE findings SET Category = ?, Description = ? WHERE FindingID = ?");
        $stmt->bind_param("ssi", $category, $description, $findingID);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to update finding');
        }

        if ($stmt->affected_rows === 0) {
            throw new Exception('No changes made to finding');
        }

        // If category is changed to Non-Compliant or Observation, update audit status to Pending
        if ($category === 'Non-Compliant' || $category === 'Observation') {
            $auditUpdateStmt = $conn->prepare("UPDATE audit SET Status = 'Pending' WHERE AuditID = ?");
            $auditUpdateStmt->bind_param("i", $auditID);
            if (!$auditUpdateStmt->execute()) {
                throw new Exception('Failed to update audit status');
            }

            // Log audit status change
            $action = "Update Audit Status";
            $conductedBy = "System";
            $details = "AuditID $auditID status changed to Pending due to Non-Compliant/Observation finding";
            $logStmt = $conn->prepare("INSERT INTO auditlogs (AuditID, Action, ConductedBy, ConductedAt, Details) VALUES (?, ?, ?, NOW(), ?)");
            $logStmt->bind_param("isss", $auditID, $action, $conductedBy, $details);
            if (!$logStmt->execute()) {
                throw new Exception('Failed to log audit status change');
            }
        }

        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Finding updated successfully'
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
        if (isset($auditUpdateStmt)) $auditUpdateStmt->close();
        if (isset($stmt)) $stmt->close();
        if (isset($auditStmt)) $auditStmt->close();
        if (isset($conn)) $conn->close();
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>