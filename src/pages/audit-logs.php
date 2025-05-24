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
    <div id="container" class="flex flex-col w-full h-screen">
        <?php include '../components/topbar.php'; ?>
        <div class="flex flex-row flex-1 overflow-hidden">
            <?php include '../components/sidebar.php'; ?>
			<div id="main" class="flex flex-col flex-1 gap-3 bg-primary p-6 overflow-hidden">
				<span id="header" class="font-bold text-[#4E3B2A] text-2xl">Audit Logs</span>
				<div class="flex-1 overflow-hidden">
					<div class="w-full h-full overflow-y-auto">
						<table class="w-full border-collapse table-auto">
							<thead class="top-0 z-10 sticky">
								<tr class="bg-secondary text-white">
									<th class="px-4 py-2 w-[8%] whitespace-nowrap">
										<div class="flex justify-start items-center gap-2">
											<box-icon name='hash' color='white'></box-icon>
											Log ID
										</div>
									</th>
									<th class="px-4 py-2 w-[15%] whitespace-nowrap">
										<div class="flex justify-start items-center gap-2">
											<box-icon name='text' color='white'></box-icon>
											Action
										</div>
									</th>
									<th class="px-4 py-2 w-[15%] whitespace-nowrap">
										<div class="flex justify-start items-center gap-2">
											<box-icon name='user' color='white'></box-icon>
											Conducted By
										</div>
									</th>
									<th class="px-4 py-2 w-[15%] whitespace-nowrap">
										<div class="flex justify-start items-center gap-2">
											<box-icon name='time' color='white'></box-icon>
											Timestamp
										</div>
									</th>
									<th class="px-4 py-2 w-[47%]">
										<div class="flex justify-start items-center gap-2">
											<box-icon name='info-circle' color='white'></box-icon>
											Details
										</div>
									</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($logs as $log): ?>
        <tr class="bg-white hover:bg-primary border-accent border-b transition-colors duration-200">
            <td class="px-4 py-2 whitespace-nowrap">
                <?= isset($log['LogID']) ? htmlspecialchars($log['LogID']) : '<span class="text-gray-400">N/A</span>' ?>
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
        <tr><td colspan="5" class="p-4 text-[#4E3B2A] text-center">No logs found.</td></tr>
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