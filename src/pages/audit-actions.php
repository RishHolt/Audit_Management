<?php
include '../php/conn.php';

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
	<link rel="stylesheet" href="../styles/output.css">
	<!-- SweetAlert2 CSS -->
	<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
	<title>Audit Management</title>
</head>
<body>
	<div id="container" class="w-full h-dvh flex flex-col">
		<div id="header" class="w-full min-h-20 max-h-20 bg-white border-b-2 border-accent">
			<div class="w-70 h-full flex items-center px-3 py-2 border-r-2 border-accent">
				<img class="size-full" src="../assets/logo.svg" alt="">
			</div>
		</div>
		<div class="size-full flex flex-row">
			<div id="sidebar" class="min-w-70 px-3 py-2 h-full flex flex-col gap-3 bg-white border-r-2 border-accent">
				<span id="header" class="text-2xl font-bold w-full h-fit text-center text-[#4E3B2A]">Audit Management</span>
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
				<a href="audit-actions.php" class="w-full flex flex-row gap-2 px-3 py-2 rounded-md border-2 border-white text-[#4E3B2A] hover:bg-accent hover:text-white transition-colors duration-200">
					<box-icon name='check-square' type='solid' color='#4E3B2A'></box-icon>
					<span>Corrective Actions</span>
				</a>
				<a href="audit-logs.php" class="w-full flex flex-row gap-2 px-3 py-2 rounded-md border-2 border-accent text-[#4E3B2A] hover:bg-accent hover:text-white transition-colors duration-200">
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
							<th class="px-4 py-2 whitespace-nowrap w-[10%]">
								<div class="flex items-center justify-start gap-2">
									<box-icon name='hash' color='white'></box-icon>
									Action ID
								</div>
							</th>
							<th class="px-4 py-2 whitespace-nowrap w-[10%]">
								<div class="flex items-center justify-start gap-2">
									<box-icon name='file' color='white'></box-icon>
									Finding ID
								</div>
							</th>
							<th class="px-4 py-2 whitespace-nowrap w-[15%]">
								<div class="flex items-center justify-start gap-2">
									<box-icon name='user' color='white'></box-icon>
									Assigned To
								</div>
							</th>
							<th class="px-4 py-2 whitespace-nowrap w-[25%]">
								<div class="flex items-center justify-start gap-2">
									<box-icon name='list-check' color='white'></box-icon>
									Task
								</div>
							</th>
							<th class="px-4 py-2 whitespace-nowrap w-[10%]">
								<div class="flex items-center justify-start gap-2">
									<box-icon name='calendar-event' color='white'></box-icon>
									Due Date
								</div>
							</th>
							<th class="px-4 py-2 whitespace-nowrap w-[15%]">
								<div class="flex items-center justify-start gap-2">
									<box-icon name='info-circle' color='white'></box-icon>
									Status
								</div>
							</th>
							<th class="px-4 py-2 whitespace-nowrap w-[15%]">
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
							<tr class="bg-white border-b-1 border-accent hover:bg-primary transition-colors duration-200" data-action-id="<?= htmlspecialchars($action['ActionID']) ?>">
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
										<button onclick="handleDelete(<?= $action['ActionID'] ?>)" class="text-center w-full px-3 py-2 bg-red-400 text-white rounded-md">Delete</button>
									</div>
								</td>
							</tr>
							<!-- View Modal -->
							<div id="view-action-modal-<?= $action['ActionID'] ?>" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="hidden fixed top-0 left-0 size-full z-50 items-center justify-center">
								<div class="flex flex-col w-full max-w-md p-4 bg-white shadow-md rounded-md">
									<div class="flex justify-between items-center mb-4">
										<span id="header" class="text-xl font-bold text-[#4E3B2A]">View Corrective Action</span>
										<button data-modal-hide="view-action-modal-<?= $action['ActionID'] ?>" class="text-gray-400 bg-transparent hover:bg-primary transition-colors duration-200 rounded-lg text-sm w-8 h-8 flex justify-center items-center">
											<box-icon name='x'></box-icon>
										</button>
									</div>
									<div class="flex flex-col gap-2 mb-4 p-3 bg-gray-50 rounded-md">
										<div><strong>Status:</strong> 
											<span class="px-2 py-1 rounded-full text-sm <?= 
												$action['Status'] === 'Completed' ? 'bg-green-100 text-green-800' : 
												($action['Status'] === 'Failed' ? 'bg-red-100 text-red-800' : 
												($action['Status'] === 'Under Review' ? 'bg-yellow-100 text-yellow-800' : 
												($action['Status'] === 'Pending' ? 'bg-blue-100 text-blue-800' : 
												'bg-gray-100 text-gray-800'))) ?>">
												<?= htmlspecialchars($action['Status']) ?>
											</span>
										</div>
										<div><strong>Action ID:</strong> <?= htmlspecialchars($action['ActionID']) ?></div>
										<div><strong>Finding ID:</strong> <?= htmlspecialchars($action['FindingID']) ?></div>
										<div><strong>Assigned To:</strong> <?= htmlspecialchars($action['AssignedTo']) ?></div>
										<div><strong>Task:</strong> <?= nl2br(htmlspecialchars($action['Task'])) ?></div>
										<div><strong>Due Date:</strong> <?= htmlspecialchars($action['DueDate']) ?></div>
									</div>
									<div class="flex justify-end gap-2 mt-4">
										<button type="button" data-modal-hide="<?= $viewModalId ?>" data-modal-target="<?= $editModalId ?>" data-modal-toggle="<?= $editModalId ?>" class="bg-green-600 text-white px-4 py-2 rounded-md">Edit</button>
										<?php if ($action['Status'] === 'Under Review'): ?>
											<button type="button" onclick="handleComplete(<?= htmlspecialchars($action['ActionID']) ?>)" class="bg-green-600 text-white px-4 py-2 rounded-md">Mark as Complete</button>
										<?php endif; ?>
									</div>
								</div>
							</div>
							<!-- Edit Modal -->
							<div id="<?= $editModalId ?>" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="hidden fixed top-0 left-0 size-full z-50 items-center justify-center">
								<div class="flex flex-col w-full max-w-md p-4 bg-white shadow-md rounded-md">
									<div class="flex justify-between items-center mb-4">
										<span class="text-xl font-bold text-[#4E3B2A]">Update Action</span>
										<button data-modal-hide="<?= $editModalId ?>" class="text-gray-400 bg-transparent hover:bg-primary transition-colors duration-200 rounded-lg text-sm w-8 h-8 flex justify-center items-center">
											<box-icon name='x'></box-icon>
										</button>
									</div>
									<form id="editActionForm-<?= $action['ActionID'] ?>" class="flex flex-col gap-3">
								
										<input type="hidden" name="ActionID" value="<?= htmlspecialchars($action['ActionID']) ?>">
										<input type="hidden" name="FindingID" value="<?= htmlspecialchars($action['FindingID']) ?>">
										<div class="flex flex-row gap-3">
											<div class="flex flex-col flex-1">
												<label>Assigned To:
													<input type="text" name="AssignedTo" required value="<?= htmlspecialchars($action['AssignedTo']) ?>" class="px-3 py-2 border rounded-lg bg-white focus:ring-2 focus:ring-accent focus:border-accent w-full">
												</label>
											</div>
										</div>
										<div class="flex flex-col">
											<label>Task:
												<textarea name="Task" required class="px-3 py-2 border rounded-lg bg-white focus:ring-2 focus:ring-accent focus:border-accent w-full h-24"><?= htmlspecialchars($action['Task']) ?></textarea>
											</label>
										</div>
																					<div class="flex flex-col">
												<label>Status:
													<select name="Status" required class="px-3 py-2 border rounded-lg bg-white focus:ring-2 focus:ring-accent focus:border-accent w-full">
														<option value="Under Review" <?= $action['Status'] === 'Under Review' ? 'selected' : '' ?>>Under Review</option>
														<option value="Failed" <?= $action['Status'] === 'Failed' ? 'selected' : '' ?>>Failed</option>
													</select>
												</label>
											</div>
										<div class="flex justify-end gap-2 mt-4">
											<button type="button" data-modal-hide="<?= $editModalId ?>" class="bg-gray-400 text-white px-4 py-2 rounded-md">Cancel</button>
											<button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md">Update</button>
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
						<form id="assignActionForm" class="flex flex-col gap-3">
							<div class="flex flex-row gap-3">
								<div class="flex flex-col flex-1">
									<label>Select Finding:
										<select name="FindingID" required class="px-3 py-2 border rounded-lg bg-white focus:ring-2 focus:ring-accent focus:border-accent w-full">
											<option value="">Select a finding...</option>
											<?php foreach ($findings as $finding): ?>
												<option value="<?= htmlspecialchars($finding['FindingID']) ?>">
													<?= htmlspecialchars($finding['FindingID'] . ' - ' . $finding['Category']) ?>
												</option>
											<?php endforeach; ?>
										</select>
									</label>
								</div>
								<div class="flex flex-col flex-1">
									<label>Assigned To:
										<input type="text" name="AssignedTo" required class="px-3 py-2 border rounded-lg bg-white focus:ring-2 focus:ring-accent focus:border-accent w-full" placeholder="Enter assignee name">
									</label>
								</div>
							</div>
							<div class="flex flex-col">
								<label>Task:
									<textarea name="Task" required class="px-3 py-2 border rounded-lg bg-white focus:ring-2 focus:ring-accent focus:border-accent w-full h-24" placeholder="Describe the corrective action task"></textarea>
								</label>
							</div>
							<div class="flex flex-col">
								<label>Due Date:
									<input type="date" name="DueDate" required class="px-3 py-2 border rounded-lg bg-white focus:ring-2 focus:ring-accent focus:border-accent w-full">
								</label>
							</div>
							<div class="flex justify-end gap-2 mt-4">
								<button type="button" data-modal-hide="action-modal" class="bg-gray-400 text-white px-4 py-2 rounded-md">Cancel</button>
								<button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md">Assign</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Load dependencies in correct order -->
	<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script>
		// Handle Delete
		async function handleDelete(actionId) {
			try {
				const result = await Swal.fire({
					title: 'Delete Confirmation',
					text: 'Are you sure you want to delete this corrective action?',
					icon: 'warning',
					showCancelButton: true,
					confirmButtonText: 'Yes, delete it',
					cancelButtonText: 'Cancel',
					customClass: {
						popup: 'rounded-lg',
						title: 'text-[#4E3B2A]',
						content: 'text-[#4E3B2A]',
						confirmButton: 'bg-red-500 text-white px-4 py-2 rounded-md',
						cancelButton: 'bg-gray-400 text-white px-4 py-2 rounded-md'
					}
				});

				if (result.isConfirmed) {
					const formData = new FormData();
					formData.append('ActionID', actionId);
					
					const response = await fetch('../php/action-delete.php', {
						method: 'POST',
						body: formData
					});
					
					const data = await response.json();
					if (!data.success) {
						throw new Error(data.message || 'Failed to delete action');
					}
					
					await Swal.fire({
						icon: 'success',
						title: 'Success',
						text: 'Action has been deleted.',
						showConfirmButton: true,
						confirmButtonText: 'OK',
						customClass: {
							popup: 'rounded-lg',
							title: 'text-[#4E3B2A]',
							content: 'text-[#4E3B2A]',
							confirmButton: 'bg-accent text-white px-4 py-2 rounded-md'
						}
					});
					location.reload();
				}
			} catch (error) {
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: error.message,
					showConfirmButton: true,
					confirmButtonText: 'OK',
					customClass: {
						popup: 'rounded-lg',
						title: 'text-[#4E3B2A]',
						content: 'text-[#4E3B2A]',
						confirmButton: 'bg-accent text-white px-4 py-2 rounded-md'
					}
				});
			}
		}

		// Handle Complete
		async function handleComplete(actionId) {
			try {
				const result = await Swal.fire({
					title: 'Mark as Complete?',
					text: 'This will mark the action as completed.',
					icon: 'question',
					showCancelButton: true,
					confirmButtonText: 'Yes, complete it',
					cancelButtonText: 'Cancel',
					customClass: {
						popup: 'rounded-lg',
						title: 'text-[#4E3B2A]',
						content: 'text-[#4E3B2A]',
						confirmButton: 'bg-accent text-white px-4 py-2 rounded-md',
						cancelButton: 'bg-gray-400 text-white px-4 py-2 rounded-md'
					}
				});

				if (result.isConfirmed) {
					Swal.fire({
						title: 'Processing...',
						text: 'Marking action as complete...',
						allowOutsideClick: false,
						allowEscapeKey: false,
						showConfirmButton: true,
						confirmButtonText: 'OK',
						customClass: {
							popup: 'rounded-lg',
							title: 'text-[#4E3B2A]',
							content: 'text-[#4E3B2A]',
							confirmButton: 'bg-accent text-white px-4 py-2 rounded-md'
						},
						willOpen: () => {
							Swal.showLoading();
						}
					});

					const response = await fetch('../php/action-complete.php', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/x-www-form-urlencoded',
						},
						body: 'ActionID=' + actionId
					});
					
					if (!response.ok) {
						throw new Error('Network response was not ok');
					}

					const data = await response.json();
					console.log('Response data:', data); // Debug log
					
					if (data.status !== 'success') {
						throw new Error(data.message || 'Failed to complete action');
					}
					
					await Swal.fire({
						icon: 'success',
						title: 'Success',
						text: 'Action has been marked as completed.',
						showConfirmButton: true,
						confirmButtonText: 'OK',
						customClass: {
							popup: 'rounded-lg',
							title: 'text-[#4E3B2A]',
							content: 'text-[#4E3B2A]',
							confirmButton: 'bg-accent text-white px-4 py-2 rounded-md'
						}
					});
					
					window.location.reload();
				}
			} catch (error) {
				console.error('Error:', error); // Debug log
				await Swal.fire({
					icon: 'error',
					title: 'Error',
					text: error.message || 'Failed to complete action',
					showConfirmButton: true,
					confirmButtonText: 'OK',
					customClass: {
						popup: 'rounded-lg',
						title: 'text-[#4E3B2A]',
						content: 'text-[#4E3B2A]',
						confirmButton: 'bg-accent text-white px-4 py-2 rounded-md'
					}
				});
			}
		}

		// Handle Edit Form Submissions
		document.querySelectorAll('[id^="editActionForm-"]').forEach(form => {
			form.addEventListener('submit', async function(e) {
				e.preventDefault();
				try {
					Swal.fire({
						title: 'Processing...',
						text: 'Updating action...',
						allowOutsideClick: false,
						allowEscapeKey: false,
						showConfirmButton: true,
						confirmButtonText: 'OK',
						customClass: {
							popup: 'rounded-lg',
							title: 'text-[#4E3B2A]',
							content: 'text-[#4E3B2A]',
							confirmButton: 'bg-accent text-white px-4 py-2 rounded-md'
						},
						willOpen: () => {
							Swal.showLoading();
						}
					});

					const formData = new FormData(this);
					const urlEncodedData = new URLSearchParams(formData).toString();
					
					const response = await fetch('../php/action-edit.php', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/x-www-form-urlencoded',
						},
						body: urlEncodedData
					});
					
					const data = await response.json();
					if (!response.ok || !data.success) {
						throw new Error(data.message || 'Failed to update action');
					}
					
					await Swal.fire({
						icon: 'success',
						title: 'Success',
						text: 'Action updated successfully',
						showConfirmButton: true,
						confirmButtonText: 'OK',
						customClass: {
							popup: 'rounded-lg',
							title: 'text-[#4E3B2A]',
							content: 'text-[#4E3B2A]',
							confirmButton: 'bg-accent text-white px-4 py-2 rounded-md'
						}
					});
					
					window.location.reload();
				} catch (error) {
					console.error('Error:', error);
					await Swal.fire({
						icon: 'error',
						title: 'Error',
						text: error.message || 'Failed to update action',
						showConfirmButton: true,
						confirmButtonText: 'OK',
						customClass: {
							popup: 'rounded-lg',
							title: 'text-[#4E3B2A]',
							content: 'text-[#4E3B2A]',
							confirmButton: 'bg-accent text-white px-4 py-2 rounded-md'
						}
					});
				}
			});
		});

		// Handle Assign Action Form
		$('#assignActionForm').on('submit', async function(e) {
			e.preventDefault();
			try {
				const result = await Swal.fire({
					title: 'Confirm Assignment',
					text: 'Are you sure you want to assign this corrective action?',
					icon: 'question',
					showCancelButton: true,
					confirmButtonText: 'Yes, assign it',
					cancelButtonText: 'Cancel',
					customClass: {
						popup: 'rounded-lg',
						title: 'text-[#4E3B2A]',
						content: 'text-[#4E3B2A]',
						confirmButton: 'bg-accent text-white px-4 py-2 rounded-md',
						cancelButton: 'bg-gray-400 text-white px-4 py-2 rounded-md'
					}
				});

				if (result.isConfirmed) {
					Swal.fire({
						title: 'Processing...',
						text: 'Assigning corrective action...',
						allowOutsideClick: false,
						allowEscapeKey: false,
						showConfirmButton: true,
						confirmButtonText: 'OK',
						customClass: {
							popup: 'rounded-lg',
							title: 'text-[#4E3B2A]',
							content: 'text-[#4E3B2A]',
							confirmButton: 'bg-accent text-white px-4 py-2 rounded-md'
						},
						willOpen: () => {
							Swal.showLoading();
						}
					});

					const formData = new FormData(this);
					const urlEncodedData = new URLSearchParams(formData).toString();
					
					const response = await fetch('../php/action-add.php', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/x-www-form-urlencoded',
						},
						body: urlEncodedData
					});
					
					if (!response.ok) {
						throw new Error('Network response was not ok');
					}

					const data = await response.json();
					console.log('Response data:', data); // Debug log
					
					if (!data.success) {
						throw new Error(data.message || 'Failed to assign action');
					}
					
					await Swal.fire({
						icon: 'success',
						title: 'Success',
						text: 'Corrective action assigned successfully',
						showConfirmButton: true,
						confirmButtonText: 'OK',
						customClass: {
							popup: 'rounded-lg',
							title: 'text-[#4E3B2A]',
							content: 'text-[#4E3B2A]',
							confirmButton: 'bg-accent text-white px-4 py-2 rounded-md'
						}
					});
					
					window.location.reload();
				}
			} catch (error) {
				console.error('Error:', error); // Debug log
				await Swal.fire({
					icon: 'error',
					title: 'Error',
					text: error.message || 'Failed to assign action',
					showConfirmButton: true,
					confirmButtonText: 'OK',
					customClass: {
						popup: 'rounded-lg',
						title: 'text-[#4E3B2A]',
						content: 'text-[#4E3B2A]',
						confirmButton: 'bg-accent text-white px-4 py-2 rounded-md'
					}
				});
			}
		});
	</script>
</body>
</html>