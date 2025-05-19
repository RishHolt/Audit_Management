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
				<a href="audit-plan.php" class="w-full flex flex-row gap-2 px-3 py-2 rounded-md border-2 border-white text-[#4E3B2A]">
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
			<div id="main" class="size-full flex flex-col gap-3 p-6 bg-primary">
				<span id="header" class="text-2xl font-bold text-[#4E3B2A]">Audit Plan</span>
				<!-- modal button -->
				<button data-modal-target="plan-modal" data-modal-toggle="plan-modal" class="flex size-fit">
					<span class="px-3 py-2 size-fit bg-accent rounded-md">New Plan</span>
				</button>
				<table class="w-full border-collapse table-auto">
					<thead>
						<tr class="bg-secondary text-white">
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
									Planned Date
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
						<?php
						include 'php/conn.php';
						$sql = "SELECT * FROM auditplan";
						$result = $conn->query($sql);
						$modals = []; // Initialize to avoid undefined variable

						if ($result->num_rows > 0) {
							while($row = $result->fetch_assoc()) {
								
								$viewModalId = "view-modal-" . $row["PlanID"];
								$editModalId = "edit-modal-" . $row["PlanID"];
								$deleteId = $row["PlanID"];

								echo "<tr class='border-b-1 border-accent bg-white hover:bg-primary transition-colors duration-200'>";
								echo "<td class='px-4 py-2 whitespace-nowrap'>" . htmlspecialchars($row["PlanID"]) . "</td>";
								echo "<td class='px-4 py-2 whitespace-nowrap'>" . htmlspecialchars($row["Title"]) . "</td>";
								echo "<td class='px-4 py-2 whitespace-nowrap'>" . htmlspecialchars($row["Department"]) . "</td>";
								echo "<td class='px-4 py-2 whitespace-nowrap'>" . htmlspecialchars($row["ScheduledDate"]) . "</td>";
								echo "<td class='px-4 py-2'>
									<span class='px-2 py-1 rounded-full text-sm " . 
									($row["Status"] === 'Completed' ? 'bg-green-100 text-green-800' : 
									($row["Status"] === 'Under Review' ? 'bg-yellow-100 text-yellow-800' : 
									($row["Status"] === 'Assigned' ? 'bg-blue-100 text-blue-800' : 
									($row["Status"] === 'Cancelled' ? 'bg-red-100 text-red-800' : 
									'bg-gray-100 text-gray-800')))) . "'>
									" . htmlspecialchars($row["Status"]) . "
									</span>
								</td>";
								echo "<td class='px-4 py-2'><div class='flex gap-1'>
									<button data-modal-target='$viewModalId' data-modal-toggle='$viewModalId' class='w-full px-3 py-2 bg-blue-400 text-white rounded-md'>View</button>
									<button onclick='handleDelete(\"$deleteId\")' class='text-center w-full px-3 py-2 bg-red-400 text-white rounded-md'>Delete</button>
								</div></td>";
								echo "</tr>";

								// View Modal (read-only)
								$modals[] = "
								<div id='$viewModalId' data-modal-backdrop='static' tabindex='-1' aria-hidden='true' class='hidden fixed top-0 left-0 size-full z-50 items-center justify-center'>
									<div class='flex flex-col w-full max-w-md p-4 bg-white shadow-md rounded-md'>
										<span id='header' class='text-xl font-bold mb-2 text-[#4E3B2A]'>Audit Plan</span>
										<div class='flex flex-col gap-3'>
											<div><strong>Plan ID:</strong> " . htmlspecialchars($row['PlanID']) . "</div>
											<div><strong>Title:</strong> " . htmlspecialchars($row['Title']) . "</div>
											<div><strong>Department:</strong> " . htmlspecialchars($row['Department']) . "</div>
											<div><strong>Scheduled Date:</strong> " . htmlspecialchars($row['ScheduledDate']) . "</div>
											<div><strong>Status:</strong> 
												<span class='px-2 py-1 rounded-full text-sm " . 
												($row["Status"] === 'Completed' ? 'bg-green-100 text-green-800' : 
												($row["Status"] === 'Under Review' ? 'bg-yellow-100 text-yellow-800' : 
												($row["Status"] === 'Assigned' ? 'bg-blue-100 text-blue-800' : 
												($row["Status"] === 'Cancelled' ? 'bg-red-100 text-red-800' : 
												'bg-gray-100 text-gray-800')))) . "'>
												" . htmlspecialchars($row["Status"]) . "
												</span>
											</div>
											<div><strong>Description:</strong> " . nl2br(htmlspecialchars($row['Description'])) . "</div>
										</div>
										<div class='flex justify-end gap-2 mt-4'>
											<button type='button' data-modal-hide='$viewModalId' class='bg-gray-400 text-white px-4 py-2 rounded-md'>Close</button>
											<button type='button' data-modal-hide='$viewModalId' data-modal-target='$editModalId' data-modal-toggle='$editModalId' class='bg-green-600 text-white px-4 py-2 rounded-md'>Edit</button>
										</div>
									</div>
								</div>";

								// Edit Modal (form)
								$modals[] = "
								<div id='$editModalId' data-modal-backdrop='static' tabindex='-1' aria-hidden='true' class='hidden fixed top-0 left-0 size-full z-50 items-center justify-center'>
									<div class='flex flex-col w-full max-w-md p-4 bg-white shadow-md rounded-md'>
										<span id='header' class='text-xl font-bold mb-2 text-[#4E3B2A]'>Audit Plan (Edit)</span>
										<form id='editPlanForm' action='php/plan-update.php' method='POST' class='flex flex-col gap-3'>
											<span>" . htmlspecialchars($row['PlanID']) . "</span>
											<input type='hidden' name='PlanID' value='" . htmlspecialchars($row["PlanID"]) . "'>
											<div class='flex flex-row gap-3'>
												<div class='flex flex-col'>
													<label>Title:
														<input type='text' name='Title' value='" . htmlspecialchars($row["Title"]) . "' class='px-3 py-2 border rounded-lg bg-white focus:ring-2 focus:ring-accent focus:border-accent w-full'>
													</label>
												</div>
												<div class='flex flex-col'>
													<label>Department:
														<input type='text' name='Department' value='" . htmlspecialchars($row["Department"]) . "' class='px-3 py-2 border rounded-lg bg-white focus:ring-2 focus:ring-accent focus:border-accent w-full'>
													</label>
												</div>
											</div>
											<div class='flex flex-col'>
												<label>Scheduled Date:
													<input type='date' name='ScheduledDate' value='" . htmlspecialchars($row["ScheduledDate"]) . "' class='px-3 py-2 border rounded-lg bg-white focus:ring-2 focus:ring-accent focus:border-accent w-full'>
												</label>
											</div>
											<div class='flex flex-col'>
												<label>Status:
													<select name='Status' class='px-3 py-2 border rounded-lg bg-white focus:ring-2 focus:ring-accent focus:border-accent w-full'>
														<option value='Scheduled' " . ($row["Status"] == 'Scheduled' ? 'selected' : '') . ">Scheduled</option>
														<option value='Open' " . ($row["Status"] == 'Open' ? 'selected' : '') . ">Open</option>
														<option value='Assigned' " . ($row["Status"] == 'Assigned' ? 'selected' : '') . ">Assigned</option>
														<option value='Under Review' " . ($row["Status"] == 'Under Review' ? 'selected' : '') . ">Under Review</option>
														<option value='Completed' " . ($row["Status"] == 'Completed' ? 'selected' : '') . ">Completed</option>
														<option value='Cancelled' " . ($row["Status"] == 'Cancelled' ? 'selected' : '') . ">Cancelled</option>
													</select>
												</label>
											</div>
											<div class='flex flex-col'>
												<label>Description:
													<textarea name='Description' class='w-full border p-2 rounded'>" . htmlspecialchars($row["Description"]) . "</textarea>
												</label>
											</div>
											<div class='flex justify-end gap-2 mt-2'>
												<button type='button' onclick='handleEdit(this.form)' class='bg-green-600 text-white px-4 py-2 rounded-md'>Save</button>
												<button type='button' data-modal-hide='$editModalId' class='bg-gray-400 text-white px-4 py-2 rounded-md'>Close</button>
											</div>
										</form>
									</div>
								</div>";
							}
						} else {
							echo "<tr><td colspan='6'>No records found</td></tr>";
						}
						$conn->close();

						// Output modals
						if (!empty($modals)) {
							foreach ($modals as $modal) {
								echo $modal;
							}
						}
						?>
					</tbody>
				</table>
						
				<!-- plan modal -->
				 <div id="plan-modal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="hidden fixed top-0 left-0 size-full z-50 items-center justify-center">
					<div class="flex flex-col w-full max-w-md p-4 bg-white shadow-md rounded-md">
						<div class="flex flex-row justify-between items-center mb-4">
							<span id="header" class="text-xl font-bold">New Plan</span>
							<button data-modal-target="plan-modal" data-modal-toggle="plan-modal" class="text-gray-400 bg-transparent hover:bg-primary transition-colors duration-200 rounded-lg text-sm w-8 h-8 flex justify-center items-center">
								<box-icon name='x'></box-icon>
							</button>
						</div>
						<form id="newPlanForm" onsubmit="handleCreate(event)" action="php/plan-submit.php" method="post" class="flex flex-col gap-3">
							<div class="flex flex-row gap-3">
								<div class="flex flex-col flex-1">
									<label for="title" class="mb-1">Title:</label>
									<input type="text" name="Title" id="Title" class="w-full border p-2 rounded" required>
								</div>
								<div class="flex flex-col flex-1">
									<label for="department" class="mb-1">Department:</label>
									<input type="text" name="Department" id="Department" class="w-full border p-2 rounded" required>
								</div>
							</div>
							<div class="flex flex-col">
								<label for="scheduled-date" class="mb-1">Scheduled Date:</label>
								<input type="date" name="ScheduledDate" id="ScheduledDate" class="w-full border p-2 rounded" required>
							</div>
							<div class="flex flex-col">
								<label for="description" class="mb-1">Description:</label>
								<textarea name="Description" id="Description" class="w-full border p-2 rounded min-h-[100px]" required></textarea>
							</div>
							<div class="flex justify-end gap-2 mt-2">
								<button type="submit" class="px-4 py-2 bg-secondary text-white rounded-md hover:bg-opacity-90">Submit</button>
								<button type="button" data-modal-hide="plan-modal" class="px-4 py-2 bg-gray-400 text-white rounded-md hover:bg-opacity-90">Cancel</button>
							</div>
						</form>
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
	<script>
		// Handle Delete
		async function handleDelete(planId) {
			await showDeleteConfirmation(
				async () => {
					const response = await fetch(`php/plan-delete.php?id=${planId}`);
					if (!response.ok) throw new Error('Failed to delete plan');
					location.reload();
				},
				'audit plan'
			);
		}

		// Handle Edit
		async function handleEdit(form) {
			try {
				showLoading('Updating audit plan...');
				const formData = new FormData(form);
				const response = await fetch('php/plan-update.php', {
					method: 'POST',
					body: formData
				});
				
				if (!response.ok) throw new Error('Failed to update plan');
				
				showUpdateSuccess('Audit plan updated successfully');
				setTimeout(() => location.reload(), 2000);
			} catch (error) {
				showError(error.message);
			}
		}

		// Handle Create
		async function handleCreate(event) {
			event.preventDefault();
			try {
				showLoading('Creating audit plan...');
				const form = event.target;
				const formData = new FormData(form);
				const response = await fetch('php/plan-submit.php', {
					method: 'POST',
					body: formData
				});
				
				if (!response.ok) throw new Error('Failed to create plan');
				
				showCreateSuccess('Audit plan created successfully');
				setTimeout(() => location.reload(), 2000);
			} catch (error) {
				showError(error.message);
			}
		}
	</script>
</body>
</html>