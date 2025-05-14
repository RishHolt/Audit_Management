<?php 

include 'php/conn.php';

// Fetch audits for dropdown
$audits = [];
$auditResult = $conn->query("SELECT AuditID, ConductingBy, ConductedAt FROM audit");
if ($auditResult && $auditResult->num_rows > 0) {
    while ($audit = $auditResult->fetch_assoc()) {
        $audits[] = $audit;
    }
}

// Fetch all findings
$findings = [];
$findingsResult = $conn->query("SELECT * FROM findings ORDER BY FindingID DESC");
if ($findingsResult && $findingsResult->num_rows > 0) {
    while ($finding = $findingsResult->fetch_assoc()) {
        $findings[] = $finding;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Cinzel&display=swap" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
	<link rel="stylesheet" href="styles/output.css">
	<title>Audit Management</title>
</head>
<body>
	<div id="container" class="w-full h-dvh flex flex-col">
		<div id="header" class="w-full min-h-20 max-h-20 bg-white border-b-2 border-accent">
			<div class="w-70 h-full flex items-center px-3 py-2 border-r-2 border-accent">
				<img class="size-full" src="assets/logo.svg" alt="">
			</div>
		</div>
		<div class="size-full flex flex-row">
			<div id="sidebar" class="min-w-70 px-3 py-2 h-full flex flex-col gap-3 bg-white border-r-2 border-accent">
				<span id="header" class="text-2xl font-bold w-full h-fit text-center">Audit Management</span>
				<a href="dashboard.php" class="w-full flex flex-row gap-2 px-3 py-2 rounded-md border-2 border-white">
					<box-icon type='solid' name='dashboard'></box-icon>
					<span>Dashboard</span>
				</a>
				<a href="audit-plan.php" class="w-full flex flex-row gap-2 px-3 py-2 rounded-md border-2 border-accent">
					<box-icon type='solid' name='dashboard'></box-icon>
					<span>Audit Plan</span>
				</a>
				<a href="audit-conduct.php" class="w-full flex flex-row gap-2 px-3 py-2 rounded-md border-2 border-accent">
					<box-icon type='solid' name='dashboard'></box-icon>
					<span>Conduct Audit</span>
				</a>
				<a href="audit-findings.php" class="w-full flex flex-row gap-2 px-3 py-2 rounded-md border-2 border-accent">
					<box-icon type='solid' name='dashboard'></box-icon>
					<span>Findings</span>
				</a>
				<a href="audit-actions.php" class="w-full flex flex-row gap-2 px-3 py-2 rounded-md border-2 border-accent">
					<box-icon type='solid' name='dashboard'></box-icon>
					<span>Corrective Actions</span>
				</a>
				<a href="audit-logs.php" class="w-full flex flex-row gap-2 px-3 py-2 rounded-md border-2 border-accent">
					<box-icon type='solid' name='dashboard'></box-icon>
					<span>Audit Logs</span>
				</a>
			</div>
			<div id="main" class="size-full flex flex-col gap-3 px-3 py-2 bg-primary">
				<span id="header" class="text-2xl font-bold">Findings</span>
				<!-- Log Finding Button -->
				<button data-modal-target="finding-modal" data-modal-toggle="finding-modal" class="flex size-fit">
					<span class="px-3 py-2 size-fit bg-accent rounded-md">Log Finding</span>
				</button>

				<!-- Findings Table -->
				<table class="w-full border-collapse table-auto">
					<thead>
						<tr class="bg-secondary text-white text-left">
							<th class="p-1 w-25">FindingID</th>
							<th class="p-1 w-25">AuditID</th>
							<th class="p-1">Category</th>
							<th class="p-1">Description</th>
							<th class="p-1">LoggedAt</th>
							<th class="p-1 w-25">Actions</th>
						</tr>
					</thead>
					<tbody>
						<?php if (count($findings) > 0): ?>
							<?php foreach ($findings as $finding): 
								$editModalId = "edit-finding-modal-" . $finding['FindingID'];
							?>
								<tr class="border-b-1 border-accent bg-white">
									<td class="p-1"><?= htmlspecialchars($finding['FindingID']) ?></td>
									<td class="p-1"><?= htmlspecialchars($finding['AuditID']) ?></td>
									<td class="p-1"><?= htmlspecialchars($finding['Category']) ?></td>
									<td class="p-1"><?= htmlspecialchars($finding['Description']) ?></td>
									<td class="p-1"><?= htmlspecialchars($finding['LoggedAt']) ?></td>
									<td class="p-1">
										<div class="flex gap-1">
											<button data-modal-target="<?= $editModalId ?>" data-modal-toggle="<?= $editModalId ?>" class="w-full px-3 py-1 bg-green-600 text-white rounded-md">Edit</button>
											<a href="php/findings-delete.php?id=<?= $finding['FindingID'] ?>" onclick="return confirm('Delete this finding?')" class="w-full px-3 py-1 bg-red-400 text-white rounded-md">Delete</a>
										</div>
									</td>
								</tr>
								<!-- Edit Modal for this finding -->
								<div id="<?= $editModalId ?>" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="hidden fixed top-0 left-0 size-full z-50 items-center justify-center">
									<div class="flex flex-col w-full max-w-md p-4 bg-white shadow-md rounded-md">
										<span class="text-xl font-bold mb-2">Edit Finding</span>
										<!-- Show finding details as text -->
										<div class="flex flex-col gap-1 mb-2">
											<span><strong>Finding ID:</strong> <?= htmlspecialchars($finding['FindingID']) ?></span>
											<span><strong>Audit ID:</strong> <?= htmlspecialchars($finding['AuditID']) ?></span>
											<span><strong>Description:</strong> <?= htmlspecialchars($finding['Description']) ?></span>
											<span><strong>Logged At:</strong> <?= htmlspecialchars($finding['LoggedAt']) ?></span>
										</div>
										<form action="php/findings-edit.php" method="post" class="flex flex-col gap-3">
											<input type="hidden" name="FindingID" value="<?= htmlspecialchars($finding['FindingID']) ?>">
											<div class="flex flex-col">
												<label for="Category-<?= $finding['FindingID'] ?>">Category:</label>
												<select name="Category" id="Category-<?= $finding['FindingID'] ?>" required class="border p-2 rounded">
													<option value="Compliant" <?= $finding['Category'] == 'Compliant' ? 'selected' : '' ?>>Compliant</option>
													<option value="Non-Compliant" <?= $finding['Category'] == 'Non-Compliant' ? 'selected' : '' ?>>Non-Compliant</option>
													<option value="Observation" <?= $finding['Category'] == 'Observation' ? 'selected' : '' ?>>Observation</option>
												</select>
											</div>
											<div class="flex flex-col">
												<label for="Description-<?= $finding['FindingID'] ?>">Description:</label>
												<textarea name="Description" id="Description-<?= $finding['FindingID'] ?>" required class="border p-2 rounded"><?= htmlspecialchars($finding['Description']) ?></textarea>
											</div>
											<div class="flex justify-end gap-2 mt-2">
												<button type="submit" class="px-3 py-2 bg-secondary text-white rounded-md">Save</button>
												<button type="button" data-modal-hide="<?= $editModalId ?>" class="bg-gray-400 text-white px-4 py-2 rounded-md">Close</button>
											</div>
										</form>
									</div>
								</div>
							<?php endforeach; ?>
						<?php else: ?>
							<tr><td colspan="6" class="text-center">No findings logged.</td></tr>
						<?php endif; ?>
					</tbody>
				</table>

				<!-- Log Finding Modal -->
				<div id="finding-modal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="hidden fixed top-0 left-0 size-full z-50 items-center justify-center">
					<div class="flex flex-col w-full max-w-md p-4 bg-white shadow-md rounded-md">
						<span class="text-xl font-bold mb-2">Log Finding</span>
						<form action="php/findings-submit.php" method="post" class="flex flex-col gap-3">
							<div class="flex flex-col">
								<label for="AuditID">Select Audit:</label>
								<select name="AuditID" id="AuditID" required class="border p-2 rounded">
									<option value="">-- Select Audit --</option>
									<?php foreach ($audits as $audit): ?>
										<option value="<?= htmlspecialchars($audit['AuditID']) ?>">
											Audit #<?= $audit['AuditID'] ?> by <?= htmlspecialchars($audit['ConductingBy']) ?> (<?= htmlspecialchars($audit['ConductedAt']) ?>)
										</option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="flex flex-col">
								<label for="Category">Category:</label>
								<select name="Category" id="Category" required class="border p-2 rounded">
									<option value="Compliant">Compliant</option>
									<option value="Non-Compliant">Non-Compliant</option>
									<option value="Observation">Observation</option>
								</select>
							</div>
							<div class="flex flex-col">
								<label for="Description">Description:</label>
								<textarea name="Description" id="Description" required class="border p-2 rounded"></textarea>
							</div>
							<div class="flex justify-end gap-2 mt-2">
								<button type="submit" class="px-3 py-2 bg-secondary text-white rounded-md">Submit</button>
								<button type="button" data-modal-hide="finding-modal" class="bg-gray-400 text-white px-4 py-2 rounded-md">Close</button>
							</div>
						</form>
					</div>
				</div>
				<!-- End Modal -->
			</div>
		</div>
	</div>
</body>
	<script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
</html>