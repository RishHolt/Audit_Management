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
	<div id="container" class="flex flex-col w-full h-screen">
		<?php include '../components/topbar.php'; ?>
		<div class="flex flex-row flex-1 overflow-hidden">
			<?php include '../components/sidebar.php'; ?>
			<div id="main" class="flex flex-col flex-1 gap-4 bg-primary p-6 overflow-y-auto">
				<!-- Dashboard Header -->
				<div class="flex justify-between items-center mb-6">
					<h1 id="header" class="font-bold text-[#4E3B2A] text-2xl">Dashboard Overview</h1>
				</div>

				<!-- Stats Cards -->
				<div class="gap-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 mb-6">
					<!-- Total Audits Card -->
					<div class="bg-white shadow-sm hover:shadow-md p-4 rounded-lg transition-all duration-300">
						<div class="flex justify-between items-start">
							<div>
								<p class="mb-1 font-semibold text-gray-600 text-sm">Total Audits</p>
								<p class="font-bold text-[#4E3B2A] text-2xl"><?= $totalAudits ?></p>
								<div class="flex items-center mt-2">
									<span class="text-sm <?= $auditChange >= 0 ? 'text-green-600' : 'text-red-600' ?>">
										<?= $auditChange ?>% <?= $auditChange >= 0 ? '↑' : '↓' ?>
									</span>
									<span class="ml-1 text-gray-500 text-xs">vs last month</span>
								</div>
							</div>
							<div class="bg-blue-100 p-2 rounded-full">
								<box-icon name='file' type='solid' color='#3b82f6' size="md"></box-icon>
							</div>
						</div>
					</div>
					
					<!-- Active Audits Card -->
					<div class="bg-white shadow-sm hover:shadow-md p-4 rounded-lg transition-all duration-300">
						<div class="flex justify-between items-start">
							<div>
								<p class="mb-1 font-semibold text-gray-600 text-sm">Active Audits</p>
								<p class="font-bold text-[#4E3B2A] text-2xl"><?= $totalOngoing ?></p>
								<div class="flex items-center mt-2">
									<span class="text-gray-500 text-xs">
										<?= $ongoingAudits ?> pending, <?= $underReviewAudits ?> in review
									</span>
								</div>
							</div>
							<div class="bg-yellow-100 p-2 rounded-full">
								<box-icon name='loader-circle' type='solid' color='#eab308' size="md"></box-icon>
							</div>
						</div>
					</div>

					<!-- Compliance Rate Card -->
					<div class="bg-white shadow-sm hover:shadow-md p-4 rounded-lg transition-all duration-300">
						<div class="flex justify-between items-start">
							<div>
								<p class="mb-1 font-semibold text-gray-600 text-sm">Compliance Rate</p>
								<p class="font-bold text-[#4E3B2A] text-2xl"><?= $complianceRate ?>%</p>
								<div class="flex items-center mt-2">
									<span class="text-gray-500 text-xs">
										<?= $compliantFindings ?> of <?= $totalFindings ?> compliant
									</span>
								</div>
							</div>
							<div class="bg-green-100 p-2 rounded-full">
								<box-icon name='check-circle' type='solid' color='#22c55e' size="md"></box-icon>
							</div>
						</div>
					</div>
				</div>

				<!-- Tables Grid -->
				<div class="gap-6 grid grid-cols-1">
					<!-- Scheduled Audit Plans -->
					<div class="bg-white shadow-sm mb-6 p-6 rounded-lg">
						<div class="flex justify-between items-center mb-4">
							<h2 class="font-semibold text-[#4E3B2A] text-lg">Scheduled Audit Plans</h2>
							<a href="audit-plan.php" class="text-text hover:underline">View All</a>
						</div>
						<?php include '../components/scheduled-audit-plans-table.php'; ?>
					</div>

					<!-- Open Audit Plans -->
					<div class="bg-white shadow-sm mb-6 p-6 rounded-lg">
						<div class="flex justify-between items-center mb-4">
							<h2 class="font-semibold text-[#4E3B2A] text-lg">Open Audit Plans</h2>
							<a href="audit-plan.php" class="text-text hover:underline">View All</a>
						</div>
						<?php include '../components/open-audit-plans-table.php'; ?>
					</div>

					<!-- Recent Findings -->
					<div class="bg-white shadow-sm mb-6 p-6 rounded-lg">
						<div class="flex justify-between items-center mb-4">
							<h2 class="font-semibold text-[#4E3B2A] text-lg">Recent Findings</h2>
							<a href="audit-findings.php" class="text-text hover:underline">View All</a>
						</div>
						<?php include '../components/recent-findings-table.php'; ?>
					</div>

					<!-- Pending Actions -->
					<div class="bg-white shadow-sm mb-6 p-6 rounded-lg">
						<div class="flex justify-between items-center mb-4">
							<h2 class="font-semibold text-[#4E3B2A] text-lg">Pending Actions</h2>
							<a href="audit-actions.php" class="text-text hover:underline">View All</a>
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