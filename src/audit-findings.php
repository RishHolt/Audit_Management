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
	<script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
	<link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
	<link rel="stylesheet" href="styles/output.css">
	<!-- SweetAlert2 CSS -->
	<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
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
				<a href="dashboard.php" class="w-full flex flex-row gap-2 px-3 py-2 rounded-md border-2 border-accent text-[#4E3B2A]">
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
				<a href="audit-findings.php" class="w-full flex flex-row gap-2 px-3 py-2 rounded-md border-2 border-white text-[#4E3B2A]">
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
			<div id="main" class="size-full flex flex-col gap-3 p-6 bg-primary">
				<span id="header" class="text-2xl font-bold text-[#4E3B2A]">Findings</span>
				<!-- Log Finding Button -->
				<button data-modal-target="finding-modal" data-modal-toggle="finding-modal" class="flex size-fit">
					<span class="px-3 py-2 size-fit bg-accent rounded-md">Log Finding</span>
				</button>

				<!-- Findings Table -->
				<table class="w-full border-collapse table-auto">
					<thead>
						<tr class="bg-secondary text-white">
							<th class="px-4 py-2 whitespace-nowrap">
								<div class="flex items-center justify-start gap-2">
									<box-icon name='hash' color='white'></box-icon>
									Finding ID
								</div>
							</th>
							<th class="px-4 py-2 whitespace-nowrap">
								<div class="flex items-center justify-start gap-2">
									<box-icon name='file' color='white'></box-icon>
									Audit ID
								</div>
							</th>
							<th class="px-4 py-2 whitespace-nowrap">
								<div class="flex items-center justify-start gap-2">
									<box-icon name='text' color='white'></box-icon>
									Description
								</div>
							</th>
							<th class="px-4 py-2 whitespace-nowrap">
								<div class="flex items-center justify-start gap-2">
									<box-icon name='tag' color='white'></box-icon>
									Category
								</div>
							</th>
							<th class="px-4 py-2 whitespace-nowrap">
								<div class="flex items-center justify-start gap-2">
									<box-icon name='info-circle' color='white'></box-icon>
									Status
								</div>
							</th>
							<th class="px-4 py-2 whitespace-nowrap">
								<div class="flex items-center justify-start gap-2">
									<box-icon name='cog' color='white'></box-icon>
									Actions
								</div>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php if (count($findings) > 0): ?>
							<?php foreach ($findings as $finding): 
								$editModalId = "edit-finding-modal-" . $finding['FindingID'];
							?>
								<tr class="border-b-1 border-accent bg-white hover:bg-primary transition-colors duration-200">
									<td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($finding['FindingID']) ?></td>
									<td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($finding['AuditID']) ?></td>
									<td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($finding['Description']) ?></td>
									<td class="px-4 py-2">
										<span class="px-2 py-1 rounded-full text-sm <?= 
											$finding['Category'] === 'Compliant' ? 'bg-green-100 text-green-800' : 
											($finding['Category'] === 'Non-Compliant' ? 'bg-red-100 text-red-800' : 
											'bg-yellow-100 text-yellow-800') ?>">
											<?= htmlspecialchars($finding['Category']) ?>
										</span>
									</td>
									<td class="px-4 py-2"><?= htmlspecialchars($finding['LoggedAt']) ?></td>
									<td class="px-4 py-2">
										<div class="flex gap-1">
											<button data-modal-target="<?= $editModalId ?>" data-modal-toggle="<?= $editModalId ?>" class="w-full px-3 py-2 bg-green-600 text-white rounded-md">Edit</button>
											<button onclick="handleDelete(<?= $finding['FindingID'] ?>)" class="w-full px-3 py-2 bg-red-400 text-white rounded-md">Delete</button>
										</div>
									</td>
								</tr>
								<!-- Edit Modal for this finding -->
								<div id="<?= $editModalId ?>" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="hidden fixed top-0 left-0 size-full z-50 items-center justify-center">
									<div class="flex flex-col w-full max-w-md p-4 bg-white shadow-md rounded-md">
										<div class="flex justify-between items-center mb-4">
											<span id="header" class="text-xl font-bold text-[#4E3B2A]">Edit Finding</span>
																		<button data-modal-hide="<?= $editModalId ?>" class="text-gray-400 bg-transparent hover:bg-primary transition-colors duration-200 rounded-lg text-sm w-8 h-8 flex justify-center items-center">
								<box-icon name='x'></box-icon>
							</button>
										</div>
										<!-- Show finding details as text -->
										<div class="flex flex-col gap-2 mb-4 p-3 bg-gray-50 rounded-md">
											<div><strong>Finding ID:</strong> <?= htmlspecialchars($finding['FindingID']) ?></div>
											<div><strong>Audit ID:</strong> <?= htmlspecialchars($finding['AuditID']) ?></div>
											<div><strong>Current Category:</strong> 
												<span class="px-2 py-1 rounded-full text-sm <?= 
													$finding['Category'] === 'Compliant' ? 'bg-green-100 text-green-800' : 
													($finding['Category'] === 'Non-Compliant' ? 'bg-red-100 text-red-800' : 
													'bg-yellow-100 text-yellow-800') ?>">
													<?= htmlspecialchars($finding['Category']) ?>
												</span>
											</div>
											<div><strong>Current Description:</strong> <?= htmlspecialchars($finding['Description']) ?></div>
											<div><strong>Logged At:</strong> <?= htmlspecialchars($finding['LoggedAt']) ?></div>
										</div>
										<form onsubmit="handleEdit(event)" action="php/findings-edit.php" method="post" class="flex flex-col gap-3">
											<input type="hidden" name="FindingID" value="<?= htmlspecialchars($finding['FindingID']) ?>">
											<div class="flex flex-col">
												<label for="Category-<?= $finding['FindingID'] ?>" class="mb-1">New Category:</label>
												<select name="Category" id="Category-<?= $finding['FindingID'] ?>" required class="w-full px-3 py-2 border rounded-lg bg-white focus:ring-2 focus:ring-accent focus:border-accent">
													<option value="Compliant" <?= $finding['Category'] == 'Compliant' ? 'selected' : '' ?>>Compliant</option>
													<option value="Non-Compliant" <?= $finding['Category'] == 'Non-Compliant' ? 'selected' : '' ?>>Non-Compliant</option>
													<option value="Observation" <?= $finding['Category'] == 'Observation' ? 'selected' : '' ?>>Observation</option>
												</select>
											</div>
											<div class="flex flex-col">
												<label for="Description-<?= $finding['FindingID'] ?>" class="mb-1">New Description:</label>
												<textarea name="Description" id="Description-<?= $finding['FindingID'] ?>" required class="w-full px-3 py-2 border rounded-lg bg-white focus:ring-2 focus:ring-accent focus:border-accent min-h-[100px]"><?= htmlspecialchars($finding['Description']) ?></textarea>
											</div>
											<div class="flex justify-end gap-2 mt-2">
												<button type="submit" class="px-4 py-2 bg-secondary text-white rounded-md hover:bg-opacity-90">Save Changes</button>
												<button type="button" data-modal-hide="<?= $editModalId ?>" class="px-4 py-2 bg-gray-400 text-white rounded-md hover:bg-opacity-90">Cancel</button>
											</div>
										</form>
									</div>
								</div>
							<?php endforeach; ?>
						<?php else: ?>
							<tr><td colspan="6" class="text-center text-[#4E3B2A]">No findings logged.</td></tr>
						<?php endif; ?>
					</tbody>
				</table>

				<!-- Log Finding Modal -->
				<div id="finding-modal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="hidden fixed top-0 left-0 size-full z-50 items-center justify-center">
					<div class="flex flex-col w-full max-w-md p-4 bg-white shadow-md rounded-md">
						<div class="flex justify-between items-center mb-4">
							<span id="header" class="text-xl font-bold text-[#4E3B2A]">Log Finding</span>
							<button data-modal-hide="finding-modal" class="text-gray-400 bg-transparent hover:bg-primary transition-colors duration-200 rounded-lg text-sm w-8 h-8 flex justify-center items-center">
								<box-icon name='x'></box-icon>
							</button>
						</div>
						<form onsubmit="handleCreate(event)" action="php/findings-submit.php" method="post" class="flex flex-col gap-3">
							<div class="flex flex-col">
								<label for="AuditID" class="mb-1">Select Audit:</label>
								<select name="AuditID" id="AuditID" required class="w-full px-3 py-2 border rounded-lg bg-white focus:ring-2 focus:ring-accent focus:border-accent">
									<option value="">-- Select Audit --</option>
									<?php foreach ($audits as $audit): ?>
										<option value="<?= htmlspecialchars($audit['AuditID']) ?>">
											Audit #<?= $audit['AuditID'] ?> (<?= htmlspecialchars($audit['ConductingBy']) ?>) - <?= htmlspecialchars($audit['ConductedAt']) ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="flex flex-col">
								<label for="Category" class="mb-1">Category:</label>
								<select name="Category" id="Category" required class="w-full px-3 py-2 border rounded-lg bg-white focus:ring-2 focus:ring-accent focus:border-accent">
									<option value="Compliant">Compliant</option>
									<option value="Non-Compliant">Non-Compliant</option>
									<option value="Observation">Observation</option>
								</select>
							</div>
							<div class="flex flex-col">
								<label for="Description" class="mb-1">Description:</label>
								<textarea name="Description" id="Description" required class="w-full px-3 py-2 border rounded-lg bg-white focus:ring-2 focus:ring-accent focus:border-accent min-h-[100px]"></textarea>
							</div>
							<div class="flex justify-end gap-2 mt-2">
								<button type="submit" class="px-4 py-2 bg-secondary text-white rounded-md hover:bg-opacity-90">Submit</button>
								<button type="button" data-modal-hide="finding-modal" class="px-4 py-2 bg-gray-400 text-white rounded-md hover:bg-opacity-90">Cancel</button>
							</div>
						</form>
					</div>
				</div>
				<!-- End Modal -->
			</div>
		</div>
	</div>
	<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
	<!-- SweetAlert2 JS -->
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<!-- Custom SweetAlert2 Utility Functions -->
	<script src="js/sweetalert.js"></script>
	<script>
		// Handle Create
		async function handleCreate(event) {
			event.preventDefault();
			try {
				showLoading('Logging finding...');
				const form = event.target;
				const formData = new FormData(form);
				const response = await fetch('php/findings-submit.php', {
					method: 'POST',
					body: formData
				});
				
				if (!response.ok) throw new Error('Failed to log finding');
				
				showCreateSuccess('Finding logged successfully');
				setTimeout(() => location.reload(), 2000);
			} catch (error) {
				showError(error.message);
			}
		}

		// Handle Edit
		async function handleEdit(event) {
			event.preventDefault();
			try {
				showLoading('Updating finding...');
				const form = event.target;
				const formData = new FormData(form);
				const response = await fetch('php/findings-edit.php', {
					method: 'POST',
					body: formData
				});
				
				if (!response.ok) throw new Error('Failed to update finding');
				
				showUpdateSuccess('Finding updated successfully');
				setTimeout(() => location.reload(), 2000);
			} catch (error) {
				showError(error.message);
			}
		}

		// Handle Delete
		async function handleDelete(findingId) {
			await showDeleteConfirmation(
				async () => {
					const response = await fetch(`php/findings-delete.php?id=${findingId}`);
					if (!response.ok) throw new Error('Failed to delete finding');
					location.reload();
				},
				'finding'
			);
		}
	</script>
</body>
</html>