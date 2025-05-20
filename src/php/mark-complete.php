<?php
include 'conn.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['AuditID'])) {
    try {
        $auditID = intval($_POST['AuditID']);
        
        // Start transaction
        $conn->begin_transaction();

        // Check if all findings are compliant
        $checkFindings = $conn->prepare("SELECT COUNT(*) as count FROM findings WHERE AuditID = ? AND Category != 'Compliant'");
        $checkFindings->bind_param("i", $auditID);
        $checkFindings->execute();
        $result = $checkFindings->get_result();
        $row = $result->fetch_assoc();
        
        if ($row['count'] > 0) {
            throw new Exception('Cannot complete audit: Some findings are not compliant');
        }

        // Update audit status to Completed
        $stmt = $conn->prepare("UPDATE audit SET Status = 'Completed' WHERE AuditID = ?");
        $stmt->bind_param("i", $auditID);
        if (!$stmt->execute()) {
            throw new Exception('Failed to update audit status');
        }

        // Get the PlanID for this audit
        $planResult = $conn->query("SELECT PlanID FROM audit WHERE AuditID = $auditID");
        if ($planRow = $planResult->fetch_assoc()) {
            $planID = $planRow['PlanID'];
            
            // Check if all audits for this plan are completed
            $check = $conn->query("SELECT COUNT(*) as cnt FROM audit WHERE PlanID = $planID AND Status != 'Completed'");
            $row = $check->fetch_assoc();
            
            if ($row['cnt'] == 0) {
                // All audits completed, mark plan as completed
                $planStmt = $conn->prepare("UPDATE auditplan SET Status = 'Completed' WHERE PlanID = ?");
                $planStmt->bind_param("i", $planID);
                if (!$planStmt->execute()) {
                    throw new Exception('Failed to update plan status');
                }

                // Log plan completion
                $action2 = "Complete Plan";
                $conductedBy = "System";
                $details2 = "PlanID $planID marked as completed (all audits complete)";
                $logStmt2 = $conn->prepare("INSERT INTO auditlogs (AuditID, Action, ConductedBy, ConductedAt, Details) VALUES (?, ?, ?, NOW(), ?)");
                $nullAuditID = null;
                $logStmt2->bind_param("isss", $nullAuditID, $action2, $conductedBy, $details2);
                if (!$logStmt2->execute()) {
                    throw new Exception('Failed to log plan completion');
                }
            }
        }

        // Log audit completion
        $action1 = "Complete Audit";
        $conductedBy = "System";
        $details1 = "AuditID $auditID marked as completed";
        $logStmt1 = $conn->prepare("INSERT INTO auditlogs (AuditID, Action, ConductedBy, ConductedAt, Details) VALUES (?, ?, ?, NOW(), ?)");
        $logStmt1->bind_param("isss", $auditID, $action1, $conductedBy, $details1);
        if (!$logStmt1->execute()) {
            throw new Exception('Failed to log audit completion');
        }

        // Commit transaction
        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Audit marked as completed successfully'
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
        if (isset($logStmt1)) $logStmt1->close();
        if (isset($logStmt2)) $logStmt2->close();
        if (isset($planStmt)) $planStmt->close();
        if (isset($stmt)) $stmt->close();
        if (isset($checkFindings)) $checkFindings->close();
        if (isset($conn)) $conn->close();
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
}
?>