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
    <div id="container" class="flex flex-col w-full h-dvh">
        <?php include '../components/topbar.php'; ?>
        <div class="flex flex-row size-full">
            <?php include '../components/sidebar.php'; ?>
			<div id="main" class="flex flex-col gap-3 bg-primary p-6 size-full">
				<span id="header" class="font-bold text-[#4E3B2A] text-2xl">Corrective Actions</span>
				<!-- Add Action Button -->
				<button data-modal-target="action-modal" data-modal-toggle="action-modal" class="flex size-fit">
					<span class="bg-accent px-3 py-2 rounded-md size-fit">Assign Action</span>
				</button>
				<!-- Actions Table -->
				<table class="w-full border-collapse table-auto">
					<thead>
						<tr class="bg-secondary text-white">
							<th class="px-4 py-2 w-[10%] whitespace-nowrap">
								<div class="flex justify-start items-center gap-2">
									<box-icon name='hash' color='white'></box-icon>
									Action ID
								</div>
							</th>
							<th class="px-4 py-2 w-[10%] whitespace-nowrap">
								<div class="flex justify-start items-center gap-2">
									<box-icon name='file' color='white'></box-icon>
									Finding ID
								</div>
							</th>
							<th class="px-4 py-2 w-[15%] whitespace-nowrap">
								<div class="flex justify-start items-center gap-2">
									<box-icon name='user' color='white'></box-icon>
									Assigned To
								</div>
							</th>
							<th class="px-4 py-2 w-[25%] whitespace-nowrap">
								<div class="flex justify-start items-center gap-2">
									<box-icon name='list-check' color='white'></box-icon>
									Task
								</div>
							</th>
							<th class="px-4 py-2 w-[10%] whitespace-nowrap">
								<div class="flex justify-start items-center gap-2">
									<box-icon name='calendar-event' color='white'></box-icon>
									Due Date
								</div>
							</th>
							<th class="px-4 py-2 w-[15%] whitespace-nowrap">
								<div class="flex justify-start items-center gap-2">
									<box-icon name='info-circle' color='white'></box-icon>
									Status
								</div>
							</th>
							<th class="px-4 py-2 w-[15%] whitespace-nowrap">
								<div class="flex justify-start items-center gap-2">
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
							<tr class="bg-white hover:bg-primary border-accent border-b-1 transition-colors duration-200" data-action-id="<?= htmlspecialchars($action['ActionID']) ?>">
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
										<button data-modal-target="<?= $viewModalId ?>" data-modal-toggle="<?= $viewModalId ?>" class="bg-blue-400 px-3 py-2 rounded-md w-full text-white">View</button>
										<button onclick="handleDelete(<?= $action['ActionID'] ?>)" class="bg-red-400 px-3 py-2 rounded-md w-full text-white text-center">Delete</button>
									</div>
								</td>
							</tr>
							<!-- View Modal -->
							<div id="view-action-modal-<?= $action['ActionID'] ?>" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="hidden top-0 left-0 z-50 fixed justify-center items-center size-full">
								<div class="flex flex-col bg-white shadow-md p-4 rounded-md w-full max-w-md">
									<div class="flex justify-between items-center mb-4">
										<span id="header" class="font-bold text-[#4E3B2A] text-xl">View Corrective Action</span>
										<button data-modal-hide="view-action-modal-<?= $action['ActionID'] ?>" class="flex justify-center items-center bg-transparent hover:bg-primary rounded-lg w-8 h-8 text-gray-400 text-sm transition-colors duration-200">
											<box-icon name='x'></box-icon>
										</button>
									</div>
									<div class="flex flex-col gap-2 bg-gray-50 mb-4 p-3 rounded-md">
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
										<button type="button" data-modal-hide="<?= $viewModalId ?>" data-modal-target="<?= $editModalId ?>" data-modal-toggle="<?= $editModalId ?>" class="bg-green-600 px-4 py-2 rounded-md text-white">Edit</button>
										<?php if ($action['Status'] === 'Under Review'): ?>
											<button type="button" onclick="handleComplete(<?= htmlspecialchars($action['ActionID']) ?>)" class="bg-green-600 px-4 py-2 rounded-md text-white">Mark as Complete</button>
										<?php endif; ?>
									</div>
								</div>
							</div>
							<!-- Edit Modal -->
							<div id="<?= $editModalId ?>" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="hidden top-0 left-0 z-50 fixed justify-center items-center size-full">
								<div class="flex flex-col bg-white shadow-md p-4 rounded-md w-full max-w-md">
									<div class="flex justify-between items-center mb-4">
										<span class="font-bold text-[#4E3B2A] text-xl">Update Action</span>
										<button data-modal-hide="<?= $editModalId ?>" class="flex justify-center items-center bg-transparent hover:bg-primary rounded-lg w-8 h-8 text-gray-400 text-sm transition-colors duration-200">
											<box-icon name='x'></box-icon>
										</button>
									</div>
									<form id="editActionForm-<?= $action['ActionID'] ?>" class="flex flex-col gap-3">
								
										<input type="hidden" name="ActionID" value="<?= htmlspecialchars($action['ActionID']) ?>">
										<input type="hidden" name="FindingID" value="<?= htmlspecialchars($action['FindingID']) ?>">
										<div class="flex flex-row gap-3">
											<div class="flex flex-col flex-1">
												<label>Assigned To:
													<input type="text" name="AssignedTo" required value="<?= htmlspecialchars($action['AssignedTo']) ?>" class="bg-white px-3 py-2 border focus:border-accent rounded-lg focus:ring-2 focus:ring-accent w-full">
												</label>
											</div>
										</div>
										<div class="flex flex-col">
											<label>Task:
												<textarea name="Task" required class="bg-white px-3 py-2 border focus:border-accent rounded-lg focus:ring-2 focus:ring-accent w-full h-24"><?= htmlspecialchars($action['Task']) ?></textarea>
											</label>
										</div>
																					<div class="flex flex-col">
												<label>Status:
													<select name="Status" required class="bg-white px-3 py-2 border focus:border-accent rounded-lg focus:ring-2 focus:ring-accent w-full">
														<option value="Under Review" <?= $action['Status'] === 'Under Review' ? 'selected' : '' ?>>Under Review</option>
														<option value="Failed" <?= $action['Status'] === 'Failed' ? 'selected' : '' ?>>Failed</option>
													</select>
												</label>
											</div>
										<div class="flex justify-end gap-2 mt-4">
											<button type="button" data-modal-hide="<?= $editModalId ?>" class="bg-gray-400 px-4 py-2 rounded-md text-white">Cancel</button>
											<button type="submit" class="bg-green-600 px-4 py-2 rounded-md text-white">Update</button>
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
				<div id="action-modal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="hidden top-0 left-0 z-50 fixed justify-center items-center size-full">
					<div class="flex flex-col bg-white shadow-md p-4 rounded-md w-full max-w-md">
						<span class="mb-2 font-bold text-[#4E3B2A] text-xl">Assign Corrective Action</span>
						<form id="assignActionForm" class="flex flex-col gap-3">
							<div class="flex flex-row gap-3">
								<div class="flex flex-col flex-1">
									<label>Select Finding:
										<select name="FindingID" required class="bg-white px-3 py-2 border focus:border-accent rounded-lg focus:ring-2 focus:ring-accent w-full">
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
										<input type="text" name="AssignedTo" required class="bg-white px-3 py-2 border focus:border-accent rounded-lg focus:ring-2 focus:ring-accent w-full" placeholder="Enter assignee name">
									</label>
								</div>
							</div>
							<div class="flex flex-col">
								<label>Task:
									<textarea name="Task" required class="bg-white px-3 py-2 border focus:border-accent rounded-lg focus:ring-2 focus:ring-accent w-full h-24" placeholder="Describe the corrective action task"></textarea>
								</label>
							</div>
							<div class="flex flex-col">
								<label>Due Date:
									<input type="date" name="DueDate" required class="bg-white px-3 py-2 border focus:border-accent rounded-lg focus:ring-2 focus:ring-accent w-full">
								</label>
							</div>
							<div class="flex justify-end gap-2 mt-4">
								<button type="button" data-modal-hide="action-modal" class="bg-gray-400 px-4 py-2 rounded-md text-white">Cancel</button>
								<button type="submit" class="bg-green-600 px-4 py-2 rounded-md text-white">Assign</button>
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