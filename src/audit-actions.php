<?php
include 'php/conn.php';

// Fetch eligible findings for dropdown
$findings = [];
$findingsResult = $conn->query("SELECT FindingID, Category, Description FROM findings WHERE Category IN ('Non-Compliant', 'Observation')");
if ($findingsResult && $findingsResult->num_rows > 0) {
    while ($f = $findingsResult->fetch_assoc()) {
        $findings[] = $f;
    }
}

// Fetch all actions
$actions = [];
$actionsResult = $conn->query("SELECT * FROM correctiveactions ORDER BY ActionID DESC");
if ($actionsResult && $actionsResult->num_rows > 0) {
    while ($a = $actionsResult->fetch_assoc()) {
        $actions[] = $a;
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
				<span id="header" class="text-2xl font-bold w-full h-fit text-center text-[#4E3B2A]">Audit Management</span>
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
				<a href="audit-findings.php" class="w-full flex flex-row gap-2 px-3 py-2 rounded-md border-2 border-accent text-[#4E3B2A]">
					<box-icon name='search-alt-2' type='solid' color='#4E3B2A'></box-icon>
					<span>Findings</span>
				</a>
				<a href="audit-actions.php" class="w-full flex flex-row gap-2 px-3 py-2 rounded-md border-2 border-white text-[#4E3B2A]">
					<box-icon name='check-square' type='solid' color='#4E3B2A'></box-icon>
					<span>Corrective Actions</span>
				</a>
				<a href="audit-logs.php" class="w-full flex flex-row gap-2 px-3 py-2 rounded-md border-2 border-accent text-[#4E3B2A]">
					<box-icon name='time-five' type='solid' color='#4E3B2A'></box-icon>
					<span>Audit Logs</span>
				</a>
			</div>
			<div id="main" class="size-full flex flex-col gap-3 p-6 bg-primary">
				<span id="header" class="text-2xl font-bold text-[#4E3B2A]">Corrective Actions</span>
				<!-- Add Action Button -->
				<button data-modal-target="action-modal" data-modal-toggle="action-modal" class="flex size-fit">
					<span class="px-3 py-2 size-fit bg-accent rounded-md">Assign Action</span>
				</button>
				<!-- Actions Table -->
				<table class="w-full border-collapse table-auto">
					<thead>
						<tr class="bg-secondary text-white">
							<th class="px-4 py-2 whitespace-nowrap">
								<div class="flex items-center justify-start gap-2">
									<box-icon name='hash' color='white'></box-icon>
									Action ID
								</div>
							</th>
							<th class="px-4 py-2 whitespace-nowrap">
								<div class="flex items-center justify-start gap-2">
									<box-icon name='file' color='white'></box-icon>
									Finding ID
								</div>
							</th>
							<th class="px-4 py-2 whitespace-nowrap">
								<div class="flex items-center justify-start gap-2">
									<box-icon name='user' color='white'></box-icon>
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
									<box-icon name='info-circle' color='white'></box-icon>
									Status
								</div>
							</th>
							<th class="px-4 py-2 w-25 whitespace-nowrap">
								<div class="flex items-center justify-start gap-2">
									<box-icon name='cog' color='white'></box-icon>
									Actions
								</div>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php if (count($actions) > 0): ?>
							<?php foreach ($actions as $action): 
								$viewModalId = "view-action-modal-" . $action['ActionID'];
								$editModalId = "edit-action-modal-" . $action['ActionID'];
							?>
							<tr class="bg-white border-b-1 border-accent hover:bg-primary transition-colors duration-200">
								<td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($action['ActionID']) ?></td>
								<td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($action['FindingID']) ?></td>
								<td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($action['AssignedTo']) ?></td>
								<td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($action['Task']) ?></td>
								<td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($action['DueDate']) ?></td>
								<td class="px-4 py-2">
									<span class="px-2 py-1 rounded-full text-sm <?= 
										$action['Status'] === 'Completed' ? 'bg-green-100 text-green-800' : 
										($action['Status'] === 'Failed' ? 'bg-red-100 text-red-800' : 
										($action['Status'] === 'Under Review' ? 'bg-yellow-100 text-yellow-800' : 
										'bg-blue-100 text-blue-800')) ?>">
										<?= htmlspecialchars($action['Status']) ?>
									</span>
								</td>
								<td class="px-4 py-2">
									<div class="flex gap-1">
										<button data-modal-target="<?= $viewModalId ?>" data-modal-toggle="<?= $viewModalId ?>" class="w-full px-3 py-2 bg-blue-400 text-white rounded-md">View</button>
										<button data-modal-target="<?= $editModalId ?>" data-modal-toggle="<?= $editModalId ?>" class="w-full px-3 py-2 bg-green-600 text-white rounded-md">Update</button>
										<button onclick="handleDelete(<?= $action['ActionID'] ?>)" class="w-full px-3 py-2 bg-red-400 text-white rounded-md">Delete</button>
									</div>
								</td>
							</tr>
							<!-- View Modal -->
							<div id="<?= $viewModalId ?>" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="hidden fixed top-0 left-0 size-full z-50 items-center justify-center">
								<div class="flex flex-col w-full max-w-md p-4 bg-white shadow-md rounded-md">
									<span class="text-xl font-bold mb-2 text-[#4E3B2A]">Action Details</span>
									<div class="flex flex-col gap-2">
										<div><strong>ActionID:</strong> <?= htmlspecialchars($action['ActionID']) ?></div>
										<div><strong>FindingID:</strong> <?= htmlspecialchars($action['FindingID']) ?></div>
										<div><strong>Assigned To:</strong> <?= htmlspecialchars($action['AssignedTo']) ?></div>
										<div><strong>Task:</strong> <?= htmlspecialchars($action['Task']) ?></div>
										<div><strong>Due Date:</strong> <?= htmlspecialchars($action['DueDate']) ?></div>
										<div><strong>Status:</strong> 
											<span class="px-2 py-1 rounded-full text-sm <?= 
												$action['Status'] === 'Completed' ? 'bg-green-100 text-green-800' : 
												($action['Status'] === 'Failed' ? 'bg-red-100 text-red-800' : 
												($action['Status'] === 'Under Review' ? 'bg-yellow-100 text-yellow-800' : 
												'bg-blue-100 text-blue-800')) ?>">
												<?= htmlspecialchars($action['Status']) ?>
											</span>
										</div>
									</div>
									<div class="flex justify-end gap-2 mt-4">
										<?php if ($action['Status'] !== 'Completed' && $action['Status'] !== 'Failed'): ?>
										<?php if ($action['Status'] === 'Under Review'): ?>
										<button type="button" onclick="handleComplete(<?= htmlspecialchars($action['ActionID']) ?>)" class="bg-green-600 text-white px-4 py-2 rounded-md">Mark as Complete</button>
										<?php endif; ?>
										<?php endif; ?>
										<button type="button" data-modal-hide="<?= $viewModalId ?>" class="bg-gray-400 text-white px-4 py-2 rounded-md">Close</button>
									</div>
								</div>
							</div>
							<!-- Edit Modal -->
							<div id="<?= $editModalId ?>" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="hidden fixed top-0 left-0 size-full z-50 items-center justify-center">
								<div class="flex flex-col w-full max-w-md p-4 bg-white shadow-md rounded-md">
									<span class="text-xl font-bold mb-2 text-[#4E3B2A]">Update Action</span>
									<form onsubmit="handleEdit(event)" action="php/action-edit.php" method="post" class="flex flex-col gap-3">
										<input type="hidden" name="ActionID" value="<?= htmlspecialchars($action['ActionID']) ?>">
										<div class="flex flex-col">
													<label for="AssignedTo-<?= $action['ActionID'] ?>">Assigned To:</label>
													<input type="text" name="AssignedTo" id="AssignedTo-<?= $action['ActionID'] ?>" value="<?= htmlspecialchars($action['AssignedTo']) ?>" class="px-3 py-2 border rounded-lg bg-white focus:ring-2 focus:ring-accent focus:border-accent" required>
										</div>
										<div class="flex flex-col">
													<label for="Task-<?= $action['ActionID'] ?>">Task:</label>
													<textarea name="Task" id="Task-<?= $action['ActionID'] ?>" class="px-3 py-2 border rounded-lg bg-white focus:ring-2 focus:ring-accent focus:border-accent min-h-[100px]" required><?= htmlspecialchars($action['Task']) ?></textarea>
										</div>
										<div class="flex flex-col">
													<label for="DueDate-<?= $action['ActionID'] ?>">Due Date:</label>
													<input type="date" name="DueDate" id="DueDate-<?= $action['ActionID'] ?>" value="<?= htmlspecialchars($action['DueDate']) ?>" class="px-3 py-2 border rounded-lg bg-white focus:ring-2 focus:ring-accent focus:border-accent" required>
										</div>
										<div class="flex flex-col">
												<label for="Status-<?= $action['ActionID'] ?>">Status:</label>
												<select name="Status" id="Status-<?= $action['ActionID'] ?>" class="px-3 py-2 border rounded-lg bg-white focus:ring-2 focus:ring-accent focus:border-accent">
														<option value="Pending" <?= $action['Status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
														<option value="Under Review" <?= $action['Status'] == 'Under Review' ? 'selected' : '' ?>>Under Review</option>
														<option value="Failed" <?= $action['Status'] == 'Failed' ? 'selected' : '' ?>>Failed</option>
												</select>
										</div>
										<div class="flex justify-end gap-2 mt-2">
												<button type="submit" class="px-3 py-2 bg-secondary text-white rounded-md">Update</button>
												<button type="button" data-modal-hide="<?= $editModalId ?>" class="bg-gray-400 text-white px-4 py-2 rounded-md">Close</button>
										</div>
									</form>
								</div>
							</div>
							<?php endforeach; ?>
						<?php else: ?>
							<tr><td colspan="7" class="text-center">No actions assigned.</td></tr>
						<?php endif; ?>
					</tbody>
				</table>

				<!-- Assign Action Modal -->
				<div id="action-modal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="hidden fixed top-0 left-0 size-full z-50 items-center justify-center">
					<div class="flex flex-col w-full max-w-md p-4 bg-white shadow-md rounded-md">
						<span class="text-xl font-bold mb-2 text-[#4E3B2A]">Assign Corrective Action</span>
						<form onsubmit="handleCreate(event)" action="php/action-add.php" method="post" class="flex flex-col gap-3">
							<div class="flex flex-col">
								<label for="FindingID">Select Finding:</label>
								<select name="FindingID" id="FindingID" required class="px-3 py-2 border rounded-lg bg-white focus:ring-2 focus:ring-accent focus:border-accent">
									<option value="">-- Select Finding --</option>
									<?php foreach ($findings as $f): ?>
										<option value="<?= htmlspecialchars($f['FindingID']) ?>">
											#<?= $f['FindingID'] ?> (<?= htmlspecialchars($f['Category']) ?>) <?= htmlspecialchars($f['Description']) ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="flex flex-col">
								<label for="AssignedTo">Assigned To:</label>
								<input type="text" name="AssignedTo" id="AssignedTo" required class="px-3 py-2 border rounded-lg bg-white focus:ring-2 focus:ring-accent focus:border-accent">
							</div>
							<div class="flex flex-col">
								<label for="Task">Task:</label>
								<textarea name="Task" id="Task" required class="px-3 py-2 border rounded-lg bg-white focus:ring-2 focus:ring-accent focus:border-accent min-h-[100px]"></textarea>
							</div>
							<div class="flex flex-col">
								<label for="DueDate">Due Date:</label>
								<input type="date" name="DueDate" id="DueDate" required class="px-3 py-2 border rounded-lg bg-white focus:ring-2 focus:ring-accent focus:border-accent">
							</div>
							<div class="flex justify-end gap-2 mt-2">
								<button type="submit" class="px-3 py-2 bg-secondary text-white rounded-md">Assign</button>
								<button type="button" data-modal-hide="action-modal" class="bg-gray-400 text-white px-4 py-2 rounded-md">Close</button>
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
				showLoading('Assigning action...');
				const form = event.target;
				const formData = new FormData(form);
				const response = await fetch('php/action-add.php', {
					method: 'POST',
					body: formData
				});
				
				if (!response.ok) throw new Error('Failed to assign action');
				
				showCreateSuccess('Action assigned successfully');
				setTimeout(() => location.reload(), 2000);
			} catch (error) {
				showError(error.message);
			}
		}

		// Handle Edit
		async function handleEdit(event) {
			event.preventDefault();
			try {
				showLoading('Updating action...');
				const form = event.target;
				const formData = new FormData(form);
				const response = await fetch('php/action-edit.php', {
					method: 'POST',
					body: formData
				});
				
				if (!response.ok) throw new Error('Failed to update action');
				
				showUpdateSuccess('Action updated successfully');
				setTimeout(() => location.reload(), 2000);
			} catch (error) {
				showError(error.message);
			}
		}

		// Handle Delete
		async function handleDelete(actionId) {
			await showDeleteConfirmation(
				async () => {
					const response = await fetch(`php/action-delete.php?id=${actionId}`);
					if (!response.ok) throw new Error('Failed to delete action');
					location.reload();
				},
				'corrective action'
			);
		}

		// Handle Complete
		async function handleComplete(actionId) {
			try {
				showLoading('Marking action as complete...');
				const formData = new FormData();
				formData.append('ActionID', actionId);
				
				const response = await fetch('php/action-complete.php', {
					method: 'POST',
					body: formData
				});
				
				if (!response.ok) throw new Error('Failed to mark action as complete');
				
				showUpdateSuccess('Action marked as complete');
				setTimeout(() => location.reload(), 2000);
			} catch (error) {
				showError(error.message);
			}
		}
	</script>
</body>
</html>