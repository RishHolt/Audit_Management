<?php 

include '../php/conn.php';

// Enhanced statistics with period comparison
$currentMonth = date('m');
$currentYear = date('Y');
$lastMonth = date('m', strtotime('-1 month'));
$lastYear = date('Y', strtotime('-1 month'));

// Total Audits with month-over-month change
$totalAuditsCurrentMonth = $conn->query("SELECT COUNT(*) as count FROM audit 
    WHERE MONTH(ConductedAt) = $currentMonth AND YEAR(ConductedAt) = $currentYear")->fetch_assoc()['count'];
$totalAuditsLastMonth = $conn->query("SELECT COUNT(*) as count FROM audit 
    WHERE MONTH(ConductedAt) = $lastMonth AND YEAR(ConductedAt) = $lastYear")->fetch_assoc()['count'];
$totalAudits = $conn->query("SELECT COUNT(*) as count FROM audit")->fetch_assoc()['count'];
$auditChange = $totalAuditsLastMonth > 0 ? 
    round((($totalAuditsCurrentMonth - $totalAuditsLastMonth) / $totalAuditsLastMonth) * 100, 1) : 0;

// Ongoing audits with status breakdown
$ongoingAudits = $conn->query("SELECT COUNT(*) as count FROM audit WHERE Status = 'Pending'")->fetch_assoc()['count'];
$underReviewAudits = $conn->query("SELECT COUNT(*) as count FROM audit WHERE Status = 'Under Review'")->fetch_assoc()['count'];
$totalOngoing = $ongoingAudits + $underReviewAudits;

// Compliance rate calculation
$totalFindings = $conn->query("SELECT COUNT(*) as count FROM findings")->fetch_assoc()['count'];
$compliantFindings = $conn->query("SELECT COUNT(*) as count FROM findings WHERE Category = 'Compliant'")->fetch_assoc()['count'];
$complianceRate = $totalFindings > 0 ? round(($compliantFindings / $totalFindings) * 100, 1) : 100;

// Pending actions with priority breakdown
$pendingActions = $conn->query("SELECT COUNT(*) as count FROM correctiveactions WHERE Status = 'Pending'")->fetch_assoc()['count'];
$highPriorityActions = $conn->query("
    SELECT COUNT(*) as count 
    FROM correctiveactions ca 
    JOIN findings f ON ca.FindingID = f.FindingID 
    WHERE ca.Status = 'Pending' 
    AND f.Category = 'Non-Compliant'")->fetch_assoc()['count'];

// Upcoming audits with department breakdown
$upcomingAudits = $conn->query("
    SELECT ap.Department, COUNT(*) as count 
    FROM audit a 
    JOIN auditplan ap ON a.PlanID = ap.PlanID 
    WHERE ap.ScheduledDate > CURRENT_DATE AND a.Status = 'Pending'
    GROUP BY ap.Department")->fetch_all(MYSQLI_ASSOC);
$totalUpcoming = array_sum(array_column($upcomingAudits, 'count'));

// Department-wise audit distribution
$departmentAudits = $conn->query("
    SELECT ap.Department, 
           COUNT(*) as total_audits,
           SUM(CASE WHEN f.Category = 'Compliant' THEN 1 ELSE 0 END) as compliant_findings,
           COUNT(DISTINCT f.FindingID) as total_findings
    FROM auditplan ap
    LEFT JOIN audit a ON ap.PlanID = a.PlanID
    LEFT JOIN findings f ON a.AuditID = f.AuditID
    GROUP BY ap.Department
    ORDER BY total_audits DESC
    LIMIT 5")->fetch_all(MYSQLI_ASSOC);

// Recent audit plans with more details
$recentPlans = $conn->query("
    SELECT ap.*, 
           COUNT(DISTINCT a.AuditID) as audit_count,
           COUNT(DISTINCT f.FindingID) as finding_count
    FROM auditplan ap
    LEFT JOIN audit a ON ap.PlanID = a.PlanID
    LEFT JOIN findings f ON a.AuditID = f.AuditID
    GROUP BY ap.PlanID
    ORDER BY ap.PlanID DESC 
    LIMIT 5");

// Open audits with priority indicators
$openPlans = $conn->query("
    SELECT ap.PlanID, ap.Title, ap.Department, ap.ScheduledDate, ap.Status,
           DATEDIFF(ap.ScheduledDate, CURRENT_DATE) as days_until_due
    FROM auditplan ap 
    WHERE ap.Status = 'Open' 
    ORDER BY ap.ScheduledDate ASC 
    LIMIT 5");

// Pending corrective actions with risk levels
$correctiveActions = $conn->query("
    SELECT ca.ActionID, ca.AssignedTo, ca.Task, ca.DueDate, ca.Status, 
           f.Category,
           DATEDIFF(ca.DueDate, CURRENT_DATE) as days_until_due
    FROM correctiveactions ca 
    JOIN findings f ON ca.FindingID = f.FindingID 
    WHERE ca.Status = 'Pending' 
    ORDER BY 
        CASE f.Category 
            WHEN 'Non-Compliant' THEN 1 
            WHEN 'Observation' THEN 2 
            ELSE 3 
        END,
        ca.DueDate ASC 
    LIMIT 5");

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
				<a href="dashboard.php" class="w-full flex flex-row gap-2 px-3 py-2 rounded-md border-2 border-white text-[#4E3B2A]">
					<box-icon name='dashboard' type='solid' color='#4E3B2A'></box-icon>
					<span>Dashboard</span>
				</a>
				<a href="audit-plan.php" class="w-full flex flex-row gap-2 px-3 py-2 rounded-md border-2 border-accent text-[#4E3B2A]">
					<box-icon name='calendar-check' type='solid' color='#4E3B2A'></box-icon>
					<span>Audit Plan</span>
				</a>
				<a href="audit-conduct.php" class="w-full flex flex-row gap-2 px-3 py-2 rounded-md border-2 border-accent text-[#4E3B2A]">
					<box-icon name='file-doc' type='solid' color='#4E3B2A'></box-icon>
					<span>Conduct Audit</span>
				</a>
				<a href="audit-findings.php" class="w-full flex flex-row gap-2 px-3 py-2 rounded-md border-2 border-accent text-[#4E3B2A]">
					<box-icon name='search-alt-2' type='solid' color='#4E3B2A'></box-icon>
					<span>Findings</span>
				</a>
				<a href="audit-actions.php" class="w-full flex flex-row gap-2 px-3 py-2 rounded-md border-2 border-accent text-[#4E3B2A]">
					<box-icon name='check-square' type='solid' color='#4E3B2A'></box-icon>
					<span>Corrective Actions</span>
				</a>
				<a href="audit-logs.php" class="w-full flex flex-row gap-2 px-3 py-2 rounded-md border-2 border-accent text-[#4E3B2A]">
					<box-icon name='time-five' type='solid' color='#4E3B2A'></box-icon>
					<span>Audit Logs</span>
				</a>
			</div>
			<div id="main" class="flex-1 flex flex-col gap-4 p-6 bg-primary overflow-y-auto">
				<!-- Dashboard Header -->
				<div class="flex justify-between items-center mb-6">
					<h1 id="header" class="text-2xl font-bold text-[#4E3B2A]">Dashboard Overview</h1>
				</div>

				<!-- Stats Cards -->
				<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
					<!-- Total Audits Card -->
					<div class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition-all duration-300">
						<div class="flex items-start justify-between">
							<div>
								<p class="text-sm font-semibold mb-1 text-gray-600">Total Audits</p>
								<p class="text-2xl font-bold text-[#4E3B2A]"><?= $totalAudits ?></p>
								<div class="flex items-center mt-2">
									<span class="text-sm <?= $auditChange >= 0 ? 'text-green-600' : 'text-red-600' ?>">
										<?= $auditChange ?>% 
										<?= $auditChange >= 0 ? '↑' : '↓' ?>
									</span>
									<span class="text-xs text-gray-500 ml-1">vs last month</span>
								</div>
							</div>
							<div class="p-2 bg-blue-100 rounded-full">
								<box-icon name='file' type='solid' color='#3b82f6' size="md"></box-icon>
							</div>
						</div>
					</div>

					<!-- Ongoing Audits Card -->
					<div class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition-all duration-300">
						<div class="flex items-start justify-between">
							<div>
								<p class="text-sm font-semibold mb-1 text-gray-600">Ongoing Audits</p>
								<p class="text-2xl font-bold text-[#4E3B2A]"><?= $totalOngoing ?></p>
								<div class="flex items-center mt-2">
									<span class="text-xs text-gray-500">
										<?= $ongoingAudits ?> Pending • <?= $underReviewAudits ?> Under Review
									</span>
								</div>
							</div>
							<div class="p-2 bg-green-100 rounded-full">
								<box-icon name='time' type='solid' color='#22c55e' size="md"></box-icon>
							</div>
						</div>
					</div>

					<!-- Compliance Rate Card -->
					<div class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition-all duration-300">
						<div class="flex items-start justify-between">
							<div>
								<p class="text-sm font-semibold mb-1 text-gray-600">Compliance Rate</p>
								<p class="text-2xl font-bold text-[#4E3B2A]"><?= $complianceRate ?>%</p>
								<div class="flex items-center mt-2">
									<span class="text-xs text-gray-500">
										<?= $compliantFindings ?> of <?= $totalFindings ?> findings compliant
									</span>
								</div>
							</div>
							<div class="p-2 bg-purple-100 rounded-full">
								<box-icon name='check-circle' type='solid' color='#a855f7' size="md"></box-icon>
							</div>
						</div>
					</div>

					<!-- Pending Actions Card -->
					<div class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition-all duration-300">
						<div class="flex items-start justify-between">
							<div>
								<p class="text-sm font-semibold mb-1 text-gray-600">Pending Actions</p>
								<p class="text-2xl font-bold text-[#4E3B2A]"><?= $pendingActions ?></p>
								<div class="flex items-center mt-2">
									<span class="text-xs text-gray-500">
										<?= $highPriorityActions ?> high priority actions
									</span>
								</div>
							</div>
							<div class="p-2 bg-yellow-100 rounded-full">
								<box-icon name='bell' type='solid' color='#eab308' size="md"></box-icon>
							</div>
						</div>
					</div>
				</div>

				<!-- Tables Grid -->
				<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
					<!-- Scheduled Audit Plans -->
					<div class="bg-white rounded-lg shadow-sm p-6">
						<div class="flex justify-between items-center mb-4">
							<h2 class="text-lg font-semibold text-[#4E3B2A]">Scheduled Audit Plans</h2>
							<a href="audit-plan.php" class="text-accent hover:underline">View All</a>
						</div>
						<?php include '../components/scheduled-audit-plans-table.php'; ?>
					</div>

					<!-- Open Audit Plans -->
					<div class="bg-white rounded-lg shadow-sm p-6">
						<div class="flex justify-between items-center mb-4">
							<h2 class="text-lg font-semibold text-[#4E3B2A]">Open Audit Plans</h2>
							<a href="audit-plan.php" class="text-accent hover:underline">View All</a>
						</div>
						<?php include '../components/open-audit-plans-table.php'; ?>
					</div>

					<!-- Recent Findings -->
					<div class="bg-white rounded-lg shadow-sm p-6">
						<div class="flex justify-between items-center mb-4">
							<h2 class="text-lg font-semibold text-[#4E3B2A]">Recent Findings</h2>
							<a href="audit-findings.php" class="text-accent hover:underline">View All</a>
						</div>
						<?php include '../components/recent-findings-table.php'; ?>
					</div>

					<!-- Pending Actions -->
					<div class="bg-white rounded-lg shadow-sm p-6">
						<div class="flex justify-between items-center mb-4">
							<h2 class="text-lg font-semibold text-[#4E3B2A]">Pending Actions</h2>
							<a href="audit-actions.php" class="text-accent hover:underline">View All</a>
						</div>
						<?php include '../components/pending-actions-table.php'; ?>
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