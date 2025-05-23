<?php
include 'conn.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    if (!isset($_POST['ConductingBy'])) {
        throw new Exception('Missing conductor information');
    }

    $conductingBy = $_POST['ConductingBy'];
    $status = 'Pending';
    $conductedAt = date('Y-m-d H:i:s');

    // Start transaction
    $conn->begin_transaction();

    try {
        if (isset($_POST['isCustom'])) {
            // Handle custom audit
            if (!isset($_POST['Title']) || !isset($_POST['Description']) || !isset($_POST['Department'])) {
                throw new Exception('Missing title, description, or department for custom audit');
            }

            $title = $_POST['Title'];
            $description = $_POST['Description'];
            $department = $_POST['Department'];
            $planID = null; // Custom audits don't need a plan ID

            // Insert custom audit with title, description, department
            $stmt = $conn->prepare("INSERT INTO audit (PlanID, Title, Description, Department, ConductingBy, ConductedAt, Status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Failed to prepare audit statement: " . $conn->error);
            }
            $stmt->bind_param("issssss", $planID, $title, $description, $department, $conductingBy, $conductedAt, $status);
        } else {
            // Handle plan-based audit
            if (!isset($_POST['PlanID'])) {
                throw new Exception('Missing plan ID');
            }
            $planID = $_POST['PlanID'];
            // Insert plan-based audit (no custom title/desc/department)
            $stmt = $conn->prepare("INSERT INTO audit (PlanID, ConductingBy, ConductedAt, Status) VALUES (?, ?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Failed to prepare audit statement: " . $conn->error);
            }
            $stmt->bind_param("isss", $planID, $conductingBy, $conductedAt, $status);
        }

        if (!$stmt->execute()) {
            throw new Exception("Failed to create audit: " . $stmt->error);
        }

        // Get the new AuditID
        $newAuditID = $conn->insert_id;

        // Update auditplan status to Assigned if it's not a custom audit
        if (!isset($_POST['isCustom'])) {
            $updateStmt = $conn->prepare("UPDATE auditplan SET Status='Assigned' WHERE PlanID=?");
            if (!$updateStmt) {
                throw new Exception("Failed to prepare update statement: " . $conn->error);
            }
            
            $updateStmt->bind_param("i", $planID);
            if (!$updateStmt->execute()) {
                throw new Exception("Failed to update plan status: " . $updateStmt->error);
            }
        }

        // Log the action
        $action = "Conduct Audit";
        $details = isset($_POST['isCustom']) 
            ? "Custom audit created: " . $title 
            : "Audit conducted for PlanID: " . $planID;
        $logStmt = $conn->prepare("INSERT INTO auditlogs (AuditID, Action, ConductedBy, ConductedAt, Details) VALUES (?, ?, ?, NOW(), ?)");
        if (!$logStmt) {
            throw new Exception("Failed to prepare log statement: " . $conn->error);
        }
        
        $logStmt->bind_param("isss", $newAuditID, $action, $conductingBy, $details);
        if (!$logStmt->execute()) {
            throw new Exception("Failed to log action: " . $logStmt->error);
        }

        // Commit transaction
        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Audit started successfully',
            'data' => [
                'auditID' => $newAuditID,
                'planID' => $planID,
                'conductingBy' => $conductingBy,
                'conductedAt' => $conductedAt,
                'status' => $status
            ]
        ]);

    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        throw $e;
    } finally {
        if (isset($stmt)) $stmt->close();
        if (isset($updateStmt)) $updateStmt->close();
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