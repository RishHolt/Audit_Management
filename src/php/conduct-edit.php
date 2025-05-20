<?php
include 'conn.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    if (!isset($_POST['AuditID']) || !isset($_POST['ConductingBy']) || !isset($_POST['Title']) || !isset($_POST['ConductedAt'])) {
        throw new Exception('Missing required fields');
    }

    $auditID = $_POST['AuditID'];
    $conductingBy = $_POST['ConductingBy'];
    $title = $_POST['Title'];
    $conductedAt = $_POST['ConductedAt'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Update audit table
        $stmt = $conn->prepare("UPDATE audit SET ConductingBy = ?, ConductedAt = ? WHERE AuditID = ?");
        if (!$stmt) {
            throw new Exception("Failed to prepare audit update statement: " . $conn->error);
        }
        
        $stmt->bind_param("ssi", $conductingBy, $conductedAt, $auditID);
        if (!$stmt->execute()) {
            throw new Exception("Failed to update audit: " . $stmt->error);
        }

        // Check if any changes were made to audit table
        $auditChanges = $stmt->affected_rows > 0;

        // Update auditplan title
        $planStmt = $conn->prepare("UPDATE auditplan SET Title = ? WHERE PlanID = (SELECT PlanID FROM audit WHERE AuditID = ?)");
        if (!$planStmt) {
            throw new Exception("Failed to prepare plan update statement: " . $conn->error);
        }
        
        $planStmt->bind_param("si", $title, $auditID);
        if (!$planStmt->execute()) {
            throw new Exception("Failed to update plan title: " . $planStmt->error);
        }

        // Check if any changes were made to plan table
        $planChanges = $planStmt->affected_rows > 0;

        // If no changes were made to either table
        if (!$auditChanges && !$planChanges) {
            throw new Exception("No changes were made to the audit");
        }

        // Log the action
        $action = "Edit Audit";
        $details = "Audit updated for AuditID: " . $auditID;
        $logStmt = $conn->prepare("INSERT INTO auditlogs (AuditID, Action, ConductedBy, ConductedAt, Details) VALUES (?, ?, ?, NOW(), ?)");
        if (!$logStmt) {
            throw new Exception("Failed to prepare log statement: " . $conn->error);
        }
        
        $logStmt->bind_param("isss", $auditID, $action, $conductingBy, $details);
        if (!$logStmt->execute()) {
            throw new Exception("Failed to log action: " . $logStmt->error);
        }

        // Commit transaction
        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Audit updated successfully',
            'data' => [
                'auditID' => $auditID,
                'conductingBy' => $conductingBy,
                'title' => $title,
                'conductedAt' => $conductedAt
            ]
        ]);

    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        throw $e;
    } finally {
        if (isset($stmt)) $stmt->close();
        if (isset($planStmt)) $planStmt->close();
        if (isset($logStmt)) $logStmt->close();
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($conn)) $conn->close();
}
?> 