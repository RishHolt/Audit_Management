<?php
include 'conn.php';

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'redirect' => '../pages/financial-audit-gl.php'
];

try {
    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        throw new Exception('Invalid request method');
    }

    // Remove or comment out CSRF validation for compatibility
    // validateCSRF();

    // Validate required fields
    $requiredFields = ['EntryID', 'ReviewedBy', 'AuditDate', 'Status'];
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("$field is required");
        }
    }

    // Check if the journal entry exists in the financial database
    $entryId = filter_var($_POST['EntryID'], FILTER_VALIDATE_INT);
    $checkEntry = executeQuery("SELECT EntryID FROM journalentries WHERE EntryID = $entryId", 'financials');
    if ($checkEntry->num_rows === 0) {
        throw new Exception('Invalid journal entry ID');
    }

    // Sanitize and validate inputs
    $entryId = filter_var($_POST['EntryID'], FILTER_VALIDATE_INT);
    if ($entryId === false) {
        throw new Exception('Invalid Entry ID');
    }

    $reviewedBy = $conn->real_escape_string($_POST['ReviewedBy']);
    $auditDate = $conn->real_escape_string($_POST['AuditDate']);
    $status = $conn->real_escape_string($_POST['Status']);
    if (!in_array($status, ['Not Audited', 'Pending', 'Reviewed', 'Flagged'])) {
        throw new Exception('Invalid status value');
    }

    // Make Findings optional
    $findings = isset($_POST['Findings']) ? $conn->real_escape_string($_POST['Findings']) : '';
    $notes = isset($_POST['Notes']) ? $conn->real_escape_string($_POST['Notes']) : '';

    // Prepare the SQL statement based on whether we're updating or inserting
    if (isset($_POST['AuditID']) && !empty($_POST['AuditID'])) {
        $auditId = filter_var($_POST['AuditID'], FILTER_VALIDATE_INT);
        if ($auditId === false) {
            throw new Exception('Invalid Audit ID');
        }

        $stmt = $conn->prepare("UPDATE financial_audit_gl 
                              SET ReviewedBy = ?, AuditDate = ?, Status = ?, 
                                  Findings = ?, Notes = ?
                              WHERE AuditID = ?");
        $stmt->bind_param("sssssi", $reviewedBy, $auditDate, $status, $findings, $notes, $auditId);
    } else {
        $stmt = $conn->prepare("INSERT INTO financial_audit_gl 
                              (EntryID, ReviewedBy, AuditDate, Status, Findings, Notes)
                              VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $entryId, $reviewedBy, $auditDate, $status, $findings, $notes);
    }

    // Execute the query
    if ($stmt->execute()) {
        // Log the audit action with a timestamp and action description
        $actionDesc = (isset($_POST['AuditID']) && !empty($_POST['AuditID'])) ? 'Updated financial audit' : 'Added financial audit';
        $entryIdForLog = $entryId;
        $conductedBy = isset($_POST['ReviewedBy']) ? $conn->real_escape_string($_POST['ReviewedBy']) : 'System';
        $statusForLog = $status;
        $auditDateForLog = $auditDate;
        $details = "Financial GL EntryID $entryIdForLog, Status: $statusForLog, Date: $auditDateForLog";
        // Insert into auditlogs with details and ConductedBy
        $logStmt = $conn->prepare("INSERT INTO auditlogs (AuditID, Action, ConductedBy, ConductedAt, Details) VALUES (?, ?, ?, NOW(), ?)");
        $nullAuditID = null;
        $logStmt->bind_param("isss", $nullAuditID, $actionDesc, $conductedBy, $details);
        $logStmt->execute();
        $logStmt->close();
        // Set action for message
        $action = (isset($_POST['AuditID']) && !empty($_POST['AuditID'])) ? 'updated' : 'added';
        $response['success'] = true;
        $response['message'] = 'Financial audit ' . strtolower($action) . ' successfully';
    } else {
        throw new Exception('Failed to save audit');
    }

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = 'Error: ' . $e->getMessage();
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
?>
