<?php 

include '../php/conn.php';
$logs = [];
// Fetch audit logs (main) and financial audit logs (from financial_audit_gl)
$mainLogs = $conn->query("SELECT *, 'main' as Source FROM auditlogs ORDER BY LogID DESC");
if ($mainLogs && $mainLogs->num_rows > 0) {
    while ($log = $mainLogs->fetch_assoc()) {
        $logs[] = $log;
    }
}
// Fetch financial audit logs (GL) directly from financial_audit_gl for display
$finLogsQuery = "SELECT 
    fa.AuditID,
    fa.EntryID,
    fa.Status,
    fa.ReviewedBy,
    fa.AuditDate,
    je.Amount,
    je.EntryType,
    je.Description,
    fa.Notes,
    'financial' as Source
FROM 
    " . DB_NAME_FINANCIALS . ".financial_audit_gl fa
LEFT JOIN " . DB_NAME_FINANCIALS . ".journalentries je ON fa.EntryID = je.EntryID
ORDER BY fa.AuditDate DESC";
$finLogs = $connFinancials->query($finLogsQuery);
if ($finLogs && $finLogs->num_rows > 0) {
    while ($log = $finLogs->fetch_assoc()) {
        $logs[] = $log;
    }
}
// Sort all logs by date/time (ConductedAt or AuditDate)
usort($logs, function($a, $b) {
    $aTime = isset($a['ConductedAt']) ? strtotime($a['ConductedAt']) : (isset($a['AuditDate']) ? strtotime($a['AuditDate']) : 0);
    $bTime = isset($b['ConductedAt']) ? strtotime($b['ConductedAt']) : (isset($b['AuditDate']) ? strtotime($b['AuditDate']) : 0);
    return $bTime <=> $aTime;
});

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Cinzel&display=swap" rel="stylesheet">
	<script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
	<link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
	<link rel="stylesheet" href="../styles/output.css">
	<!-- SweetAlert2 CSS -->
	<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
	<title>Audit Management</title>
</head>
<body class="overflow-hidden">
	<div id="container" class="w-full h-screen flex flex-col">
		<div id="header" class="w-full min-h-20 max-h-20 bg-white border-b-2 border-accent">
			<div class="w-70 h-full flex items-center px-3 py-2 border-r-2 border-accent">
				<img class="size-full" src="../assets/logo.svg" alt="">
			</div>
		</div>
		<div class="flex-1 flex flex-row overflow-hidden">
			<div id="sidebar" class="min-w-70 px-3 py-2 h-full flex flex-col gap-3 bg-white border-r-2 border-accent">
				<span id="header" class="text-2xl font-bold w-full h-fit text-center">Audit Management</span>
				<a href="dashboard.php" class="w-full flex flex-row gap-2 px-3 py-2 rounded-md border-2 border-accent text-[#4E3B2A] hover:bg-accent hover:text-white transition-colors duration-200">
					<box-icon name='dashboard' type='solid' color='#4E3B2A'></box-icon>
					<span>Dashboard</span>
				</a>
				<a href="audit-plan.php" class="w-full flex flex-row gap-2 px-3 py-2 rounded-md border-2 border-accent text-[#4E3B2A] hover:bg-accent hover:text-white transition-colors duration-200">
					<box-icon name='calendar-check' type='solid' color='#4E3B2A'></box-icon>
					<span>Audit Plan</span>
				</a>
				<a href="audit-conduct.php" class="w-full flex flex-row gap-2 px-3 py-2 rounded-md border-2 border-accent text-[#4E3B2A] hover:bg-accent hover:text-white transition-colors duration-200">
					<box-icon name='file-doc' type='solid' color='#4E3B2A'></box-icon>
					<span>Conduct Audit</span>
				</a>
				<a href="financial-audit-gl.php" class="w-full flex flex-row gap-2 px-3 py-2 rounded-md border-2 border-white text-[#4E3B2A] hover:bg-accent hover:text-white transition-colors duration-200">
                    <box-icon name='dollar-circle' type='solid' color='#4E3B2A'></box-icon>
                    <span>Financial Audit (GL)</span>
                </a>
				<a href="audit-findings.php" class="w-full flex flex-row gap-2 px-3 py-2 rounded-md border-2 border-accent text-[#4E3B2A] hover:bg-accent hover:text-white transition-colors duration-200">
					<box-icon name='search-alt-2' type='solid' color='#4E3B2A'></box-icon>
					<span>Findings</span>
				</a>
				<a href="audit-actions.php" class="w-full flex flex-row gap-2 px-3 py-2 rounded-md border-2 border-accent text-[#4E3B2A] hover:bg-accent hover:text-white transition-colors duration-200">
					<box-icon name='check-square' type='solid' color='#4E3B2A'></box-icon>
					<span>Corrective Actions</span>
				</a>
				<a href="audit-logs.php" class="w-full flex flex-row gap-2 px-3 py-2 rounded-md border-2 border-white text-[#4E3B2A] hover:bg-accent hover:text-white transition-colors duration-200">
					<box-icon name='time-five' type='solid' color='#4E3B2A'></box-icon>
					<span>Audit Logs</span>
				</a>
			</div>
			<div id="main" class="flex-1 flex flex-col gap-3 p-6 bg-primary overflow-hidden">
				<span id="header" class="text-2xl font-bold text-[#4E3B2A]">Audit Logs</span>
				<div class="flex-1 overflow-hidden">
					<div class="w-full h-full overflow-y-auto">
						<table class="w-full border-collapse table-auto">
							<thead class="sticky top-0 z-10">
								<tr class="bg-secondary text-white">
									<th class="px-4 py-2 whitespace-nowrap w-[8%]">
										<div class="flex items-center justify-start gap-2">
											<box-icon name='hash' color='white'></box-icon>
											Log ID
										</div>
									</th>
									<th class="px-4 py-2 whitespace-nowrap w-[10%]">
										<div class="flex items-center justify-start gap-2">
											<box-icon name='hash' color='white'></box-icon>
											Audit ID
										</div>
									</th>
									<th class="px-4 py-2 whitespace-nowrap w-[15%]">
										<div class="flex items-center justify-start gap-2">
											<box-icon name='text' color='white'></box-icon>
											Action
										</div>
									</th>
									<th class="px-4 py-2 whitespace-nowrap w-[15%]">
										<div class="flex items-center justify-start gap-2">
											<box-icon name='user' color='white'></box-icon>
											Conducted By
										</div>
									</th>
									<th class="px-4 py-2 whitespace-nowrap w-[15%]">
										<div class="flex items-center justify-start gap-2">
											<box-icon name='time' color='white'></box-icon>
											Timestamp
										</div>
									</th>
									<th class="px-4 py-2 w-[37%]">
										<div class="flex items-center justify-start gap-2">
											<box-icon name='info-circle' color='white'></box-icon>
											Details
										</div>
									</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($logs as $log): ?>
        <tr class="bg-white border-b border-accent hover:bg-primary transition-colors duration-200">
            <td class="px-4 py-2 whitespace-nowrap">
                <?= isset($log['LogID']) ? htmlspecialchars($log['LogID']) : '<span class="text-gray-400">N/A</span>' ?>
            </td>
            <td class="px-4 py-2 whitespace-nowrap">
                <?= isset($log['AuditID']) ? htmlspecialchars($log['AuditID']) : (isset($log['EntryID']) ? 'GL#' . htmlspecialchars($log['EntryID']) : '<span class="text-gray-400">N/A</span>') ?>
            </td>
            <td class="px-4 py-2 whitespace-nowrap">
                <?php if (isset($log['Source']) && $log['Source'] === 'financial'): ?>
                    Financial GL Audit
                <?php else: ?>
                    <?= htmlspecialchars($log['Action']) ?>
                <?php endif; ?>
            </td>
            <td class="px-4 py-2 whitespace-nowrap">
                <?= isset($log['ConductedBy']) ? htmlspecialchars($log['ConductedBy']) : (isset($log['ReviewedBy']) ? htmlspecialchars($log['ReviewedBy']) : '<span class="text-gray-400">N/A</span>') ?>
            </td>
            <td class="px-4 py-2 whitespace-nowrap">
                <?= isset($log['ConductedAt']) ? htmlspecialchars($log['ConductedAt']) : (isset($log['AuditDate']) ? htmlspecialchars($log['AuditDate']) : '<span class="text-gray-400">N/A</span>') ?>
            </td>
            <td class="px-4 py-2 break-words">
                <?php if (isset($log['Source']) && $log['Source'] === 'financial'): ?>
                    Amount: <?= number_format($log['Amount'], 2) ?> | Type: <?= htmlspecialchars($log['EntryType']) ?> | Status: <?= htmlspecialchars($log['Status']) ?><br>
                    <?= htmlspecialchars($log['Description']) ?><br>
                    Notes: <?= htmlspecialchars($log['Notes']) ?>
                <?php else: ?>
                    <?= htmlspecialchars($log['Details']) ?>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (empty($logs)): ?>
        <tr><td colspan="6" class="text-center p-4 text-[#4E3B2A]">No logs found.</td></tr>
    <?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
	<!-- SweetAlert2 JS -->
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<!-- Custom SweetAlert2 Utility Functions -->
	<script src="../js/sweetalert.js"></script>
</body>
</html>