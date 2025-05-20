<?php
include 'conn.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required fields
        $requiredFields = ['FindingID', 'AssignedTo', 'Task', 'DueDate'];
        foreach ($requiredFields as $field) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                throw new Exception("$field is required");
            }
        }

        $findingID = intval($_POST['FindingID']);
        $assignedTo = $_POST['AssignedTo'];
        $task = $_POST['Task'];
        $dueDate = $_POST['DueDate'];
        $status = 'Pending'; // Initial status should be Pending

        // Check if finding exists
        $checkStmt = $conn->prepare("SELECT FindingID FROM findings WHERE FindingID = ?");
        $checkStmt->bind_param("i", $findingID);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('Finding not found');
        }
        $checkStmt->close();

        // Insert new action
        $stmt = $conn->prepare("INSERT INTO correctiveactions (FindingID, AssignedTo, Task, DueDate, Status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $findingID, $assignedTo, $task, $dueDate, $status);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to create action: ' . $stmt->error);
        }

        $newActionId = $stmt->insert_id;
        $stmt->close();

        echo json_encode([
            'success' => true,
            'message' => 'Corrective action created successfully',
            'actionId' => $newActionId
        ]);

    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}

$conn->close();
?>