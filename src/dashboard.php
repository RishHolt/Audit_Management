<?php 

include 'php/conn.php';

// Fetch total audits
$totalAudits = $conn->query("SELECT COUNT(*) as count FROM audit")->fetch_assoc()['count'];

// Fetch ongoing audits
$ongoingAudits = $conn->query("SELECT COUNT(*) as count FROM audit WHERE Status = 'Pending'")->fetch_assoc()['count'];

// Fetch pending corrective actions
$pendingActions = $conn->query("SELECT COUNT(*) as count FROM correctiveactions WHERE Status = 'Pending'")->fetch_assoc()['count'];

// Fetch upcoming audits
$upcomingAudits = $conn->query("SELECT COUNT(*) as count FROM audit a 
                               JOIN auditplan ap ON a.PlanID = ap.PlanID 
                               WHERE ap.ScheduledDate > CURRENT_DATE AND a.Status = 'Pending'")->fetch_assoc()['count'];

// Fetch recent audit plans
$recentPlans = $conn->query("SELECT * FROM auditplan ORDER BY PlanID DESC LIMIT 5");

// Fetch open audits
$openAudits = $conn->query("SELECT a.AuditID, a.ConductingBy, a.ConductedAt, a.Status, ap.Title, ap.Department 
                           FROM audit a 
                           JOIN auditplan ap ON a.PlanID = ap.PlanID 
                           WHERE a.Status != 'Completed' 
                           ORDER BY a.ConductedAt DESC LIMIT 5");

// Fetch pending corrective actions
$correctiveActions = $conn->query("SELECT ca.ActionID, ca.AssignedTo, ca.Task, ca.DueDate, ca.Status, f.Category 
                                 FROM correctiveactions ca 
                                 JOIN findings f ON ca.FindingID = f.FindingID 
                                 WHERE ca.Status = 'Pending' 
                                 ORDER BY ca.DueDate ASC LIMIT 5");

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
	<link rel="stylesheet" href="styles/output.css">
	<!-- SweetAlert2 CSS -->
	<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
	<title>Audit Management</title>
</head>
<body class="overflow-hidden">
	<div id="container" class="w-full h-screen flex flex-col">
		<div id="header" class="w-full min-h-20 max-h-20 bg-white border-b-2 border-accent">
			<div class="w-70 h-full flex items-center px-3 py-2 border-r-2 border-accent">
				<img class="size-full" src="assets/logo.svg" alt="">
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
				<!-- Dashboard Title -->
				<h1 id="header" class="text-2xl font-bold mb-2">Dashboard Overview</h1>

				<!-- Stats Cards -->
				<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
					<!-- Total Audits Card -->
					<div class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 hover:bg-primary">
						<div class="flex items-start space-x-4">
							<div class="flex-shrink-0">
								<div class="p-2 bg-blue-100 rounded-full">
									<box-icon name='file' type='solid' color='#3b82f6' size="sm"></box-icon>
								</div>
							</div>
							<div class="flex-1">
								<p class="text-sm font-semibold mb-1">Total Audits</p>
								<p class="text-xl font-bold"><?= $totalAudits ?></p>
							</div>
						</div>
					</div>

					<!-- Ongoing Audits Card -->
					<div class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 hover:bg-primary">
						<div class="flex items-start space-x-4">
							<div class="flex-shrink-0">
								<div class="p-2 bg-green-100 rounded-full">
									<box-icon name='time' type='solid' color='#22c55e' size="sm"></box-icon>
								</div>
							</div>
							<div class="flex-1">
								<p class="text-sm font-semibold mb-1">Ongoing Audits</p>
								<p class="text-xl font-bold"><?= $ongoingAudits ?></p>
							</div>
						</div>
					</div>

					<!-- Pending Actions Card -->
					<div class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 hover:bg-primary">
						<div class="flex items-start space-x-4">
							<div class="flex-shrink-0">
								<div class="p-2 bg-yellow-100 rounded-full">
									<box-icon name='bell' type='solid' color='#eab308' size="sm"></box-icon>
								</div>
							</div>
							<div class="flex-1">
								<p class="text-sm font-semibold mb-1">Pending Actions</p>
								<p class="text-xl font-bold"><?= $pendingActions ?></p>
							</div>
						</div>
					</div>

					<!-- Upcoming Audits Card -->
					<div class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 hover:bg-primary">
						<div class="flex items-start space-x-4">
							<div class="flex-shrink-0">
								<div class="p-2 bg-purple-100 rounded-full">
									<box-icon name='calendar' type='solid' color='#a855f7' size="sm"></box-icon>
								</div>
							</div>
							<div class="flex-1">
								<p class="text-sm font-semibold mb-1">Upcoming Audits</p>
								<p class="text-xl font-bold"><?= $upcomingAudits ?></p>
							</div>
						</div>
					</div>
				</div>

				<!-- Recent Audit Plans Table -->
				<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
					<div class="flex justify-between items-center mb-4">
						<h2 id="header" class="text-xl font-semibold text-[#4E3B2A]">Recent Audit Plans</h2>
						<input type="text" placeholder="Search plans..." class="px-3 py-2 border rounded-lg bg-white focus:ring-2 focus:ring-accent focus:border-accent">
					</div>
					<div class="overflow-x-auto">
						<table class="w-full">
							<thead class="bg-secondary text-white">
								<tr>
									<th class="px-4 py-2 whitespace-nowrap">
										<div class="flex items-center justify-start gap-2">
											<box-icon name='hash' color='white'></box-icon>
											Plan ID
										</div>
									</th>
									<th class="px-4 py-2 whitespace-nowrap">
										<div class="flex items-center justify-start gap-2">
											<box-icon name='text' color='white'></box-icon>
											Title
										</div>
									</th>
									<th class="px-4 py-2 whitespace-nowrap">
										<div class="flex items-center justify-start gap-2">
											<box-icon name='building' color='white'></box-icon>
											Department
										</div>
									</th>
									<th class="px-4 py-2 whitespace-nowrap">
										<div class="flex items-center justify-start gap-2">
											<box-icon name='calendar' color='white'></box-icon>
											Scheduled Date
										</div>
									</th>
									<th class="px-4 py-2 whitespace-nowrap">
										<div class="flex items-center justify-start gap-2">
											<box-icon name='info-circle' color='white'></box-icon>
											Status
										</div>
									</th>
								</tr>
							</thead>
							<tbody>
								<?php if ($recentPlans && $recentPlans->num_rows > 0): ?>
									<?php while ($plan = $recentPlans->fetch_assoc()): ?>
										<tr class="border-t hover:bg-primary transition-colors duration-200">
											<td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($plan['PlanID']) ?></td>
											<td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($plan['Title']) ?></td>
											<td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($plan['Department']) ?></td>
											<td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($plan['ScheduledDate']) ?></td>
											<td class="px-4 py-2">
												<span class="px-2 py-1 rounded-full text-sm 
													<?= $plan['Status'] === 'Completed' ? 'bg-green-100 text-green-800' : 
														($plan['Status'] === 'Open' ? 'bg-blue-100 text-blue-800' : 
														'bg-yellow-100 text-yellow-800') ?>">
													<?= htmlspecialchars($plan['Status']) ?>
												</span>
											</td>
										</tr>
									<?php endwhile; ?>
								<?php else: ?>
									<tr>
										<td colspan="5" class="px-4 py-2 text-center">No recent audit plans found</td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>

				<!-- Open Audits Table -->
				<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
					<div class="flex justify-between items-center mb-4">
						<h2 id="header" class="text-xl font-semibold text-[#4E3B2A]">Open Audits</h2>
						<input type="text" placeholder="Search audits..." class="px-3 py-2 border rounded-lg bg-white focus:ring-2 focus:ring-accent focus:border-accent">
					</div>
					<div class="overflow-x-auto">
						<table class="w-full">
							<thead class="bg-secondary text-white">
								<tr>
									<th class="px-4 py-2 whitespace-nowrap">
										<div class="flex items-center justify-start gap-2">
											<box-icon name='hash' color='white'></box-icon>
											Audit ID
										</div>
									</th>
									<th class="px-4 py-2 whitespace-nowrap">
										<div class="flex items-center justify-start gap-2">
											<box-icon name='text' color='white'></box-icon>
											Title
										</div>
									</th>
									<th class="px-4 py-2 whitespace-nowrap">
										<div class="flex items-center justify-start gap-2">
											<box-icon name='building' color='white'></box-icon>
											Department
										</div>
									</th>
									<th class="px-4 py-2 whitespace-nowrap">
										<div class="flex items-center justify-start gap-2">
											<box-icon name='user' color='white'></box-icon>
											Conducting By
										</div>
									</th>
									<th class="px-4 py-2 whitespace-nowrap">
										<div class="flex items-center justify-start gap-2">
											<box-icon name='time' color='white'></box-icon>
											Conducted At
										</div>
									</th>
									<th class="px-4 py-2 whitespace-nowrap">
										<div class="flex items-center justify-start gap-2">
											<box-icon name='info-circle' color='white'></box-icon>
											Status
										</div>
									</th>
								</tr>
							</thead>
							<tbody>
								<?php if ($openAudits && $openAudits->num_rows > 0): ?>
									<?php while ($audit = $openAudits->fetch_assoc()): ?>
										<tr class="border-t hover:bg-primary transition-colors duration-200">
											<td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($audit['AuditID']) ?></td>
											<td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($audit['Title']) ?></td>
											<td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($audit['Department']) ?></td>
											<td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($audit['ConductingBy']) ?></td>
											<td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($audit['ConductedAt']) ?></td>
											<td class="px-4 py-2">
												<span class="px-2 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
													<?= htmlspecialchars($audit['Status']) ?>
												</span>
											</td>
										</tr>
									<?php endwhile; ?>
								<?php else: ?>
									<tr>
										<td colspan="6" class="px-4 py-2 text-center">No open audits found</td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>

				<!-- Corrective Actions Table -->
				<div class="bg-white rounded-lg shadow-sm p-6">
					<div class="flex justify-between items-center mb-4">
						<h2 id="header" class="text-xl font-semibold text-[#4E3B2A]">Pending Corrective Actions</h2>
						<input type="text" placeholder="Search actions..." class="px-3 py-2 border rounded-lg bg-white focus:ring-2 focus:ring-accent focus:border-accent">
					</div>
					<div class="overflow-x-auto">
						<table class="w-full">
							<thead class="bg-secondary text-white">
								<tr>
									<th class="px-4 py-2 whitespace-nowrap">
										<div class="flex items-center justify-start gap-2">
											<box-icon name='id-card' color='white'></box-icon>
											Action ID
										</div>
									</th>
									<th class="px-4 py-2 whitespace-nowrap">
										<div class="flex items-center justify-start gap-2">
											<box-icon name='user-circle' color='white'></box-icon>
											Assigned To
										</div>
									</th>
									<th class="px-4 py-2 whitespace-nowrap">
										<div class="flex items-center justify-start gap-2">
											<box-icon name='list-check' color='white'></box-icon>
											Task
										</div>
									</th>
									<th class="px-4 py-2 whitespace-nowrap">
										<div class="flex items-center justify-start gap-2">
											<box-icon name='calendar-event' color='white'></box-icon>
											Due Date
										</div>
									</th>
									<th class="px-4 py-2 whitespace-nowrap">
										<div class="flex items-center justify-start gap-2">
											<box-icon name='bookmark' color='white'></box-icon>
											Finding Category
										</div>
									</th>
									<th class="px-4 py-2 whitespace-nowrap">
										<div class="flex items-center justify-start gap-2">
											<box-icon name='badge-check' color='white'></box-icon>
											Status
										</div>
									</th>
								</tr>
							</thead>
							<tbody>
								<?php if ($correctiveActions && $correctiveActions->num_rows > 0): ?>
									<?php while ($action = $correctiveActions->fetch_assoc()): ?>
										<tr class="border-t hover:bg-primary transition-colors duration-200">
											<td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($action['ActionID']) ?></td>
											<td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($action['AssignedTo']) ?></td>
											<td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($action['Task']) ?></td>
											<td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($action['DueDate']) ?></td>
											<td class="px-4 py-2">
												<span class="px-2 py-1 rounded-full text-sm 
													<?= $action['Category'] === 'Non-Compliant' ? 'bg-red-100 text-red-800' : 
														($action['Category'] === 'Observation' ? 'bg-yellow-100 text-yellow-800' : 
														'bg-green-100 text-green-800') ?>">
													<?= htmlspecialchars($action['Category']) ?>
												</span>
											</td>
											<td class="px-4 py-2">
												<span class="px-2 py-1 rounded-full text-sm bg-yellow-100 text-yellow-800">
													<?= htmlspecialchars($action['Status']) ?>
												</span>
											</td>
										</tr>
									<?php endwhile; ?>
								<?php else: ?>
									<tr>
										<td colspan="6" class="px-4 py-2 text-center">No pending corrective actions found</td>
									</tr>
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
	<script src="js/sweetalert.js"></script>
</body>
</html>