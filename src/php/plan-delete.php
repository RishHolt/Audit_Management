<?php
include 'conn.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'No plan ID provided'
    ]);
    exit;
}

$id = intval($_GET['id']);

// First check if the plan exists
$checkPlan = $conn->query("SELECT PlanID FROM auditplan WHERE PlanID = $id");
if (!$checkPlan || $checkPlan->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Plan not found',
        'alreadyDeleted' => true
    ]);
    exit;
}

// Check if this is just a check request
if (!isset($_GET['force']) && !isset($_GET['confirm'])) {
    // Check if the plan has any associated audits
    $checkAudits = $conn->query("SELECT COUNT(*) as count FROM audit WHERE PlanID = $id");
    $hasAudits = false;
    if ($checkAudits && $auditCount = $checkAudits->fetch_assoc()) {
        $hasAudits = ($auditCount['count'] > 0);
    }

    if ($hasAudits) {
        echo json_encode([
            'success' => false,
            'message' => 'This plan has associated audits',
            'requireForce' => true,
            'auditCount' => $auditCount['count']
        ]);
        exit;
    }
}

try {
    // Start transaction
    $conn->begin_transaction();

    // If force parameter is set, delete associated audits first
    if (isset($_GET['force'])) {
        // Get all audit IDs for this plan
        $auditIdsResult = $conn->query("SELECT AuditID FROM audit WHERE PlanID = $id");
        if ($auditIdsResult) {
            while ($row = $auditIdsResult->fetch_assoc()) {
                $auditId = $row['AuditID'];
                
                // Delete audit logs first
                $stmt = $conn->prepare("DELETE FROM auditlogs WHERE AuditID = ?");
                $stmt->bind_param("i", $auditId);
                $stmt->execute();
                $stmt->close();
                
                // Delete findings (this should cascade to corrective actions)
                $stmt = $conn->prepare("DELETE FROM findings WHERE AuditID = ?");
                $stmt->bind_param("i", $auditId);
                $stmt->execute();
                $stmt->close();
            }
        }

        // Now delete all audits for this plan
        $stmt = $conn->prepare("DELETE FROM audit WHERE PlanID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }

    // Delete the plan
    $stmt = $conn->prepare("DELETE FROM auditplan WHERE PlanID = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $id);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    if ($stmt->affected_rows === 0) {
        throw new Exception("Plan not found or already deleted");
    }

    // Log the action (with null AuditID since we're deleting the plan)
    $action = "Delete Plan";
    $conductedBy = "System";
    $details = isset($_GET['force']) ? 
        "PlanID $id deleted with all associated audits and findings" : 
        "PlanID $id deleted";
    
    $logStmt = $conn->prepare("INSERT INTO auditlogs (AuditID, Action, ConductedBy, ConductedAt, Details) VALUES (?, ?, ?, NOW(), ?)");
    if (!$logStmt) {
        throw new Exception("Log prepare failed: " . $conn->error);
    }

    $nullAuditID = null;
    $logStmt->bind_param("isss", $nullAuditID, $action, $conductedBy, $details);
    if (!$logStmt->execute()) {
        throw new Exception("Log execute failed: " . $logStmt->error);
    }

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => isset($_GET['force']) ? 
            'Plan and all associated audits deleted successfully' : 
            'Plan deleted successfully'
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($conn) && $conn->connect_errno === 0) {
        $conn->rollback();
    }
    
    echo json_encode([
        'success' => false,
        'message' => 'Error deleting plan: ' . $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($logStmt)) $logStmt->close();
    $conn->close();
}
?>
