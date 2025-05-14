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
				<span id="header" class="text-2xl font-bold">Audit Plan</span>
				<!-- modal button -->
				<button data-modal-target="plan-modal" data-modal-toggle="plan-modal" class="flex size-fit">
					<span class="px-3 py-2 size-fit bg-accent rounded-md">New Plan</span>
				</button>
				<table class="w-full border-collapse table-auto">
					<thead>
						<tr class="bg-secondary text-white text-left">
							<th class="p-1 w-25">Plan ID</th>
							<th class="p-1">Title</th>
							<th class="p-1">Department</th>
							<th class="p-1">Planned Date</th>
							<th class="p-1">Status</th>
							<th class="p-1 w-25">Actions</th>
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

								echo "<tr class='border-b-1 border-accent bg-white'>";
								echo "<td class='p-1'>" . htmlspecialchars($row["PlanID"]) . "</td>";
								echo "<td class='p-1'>" . htmlspecialchars($row["Title"]) . "</td>";
								echo "<td class='p-1'>" . htmlspecialchars($row["Department"]) . "</td>";
								echo "<td class='p-1'>" . htmlspecialchars($row["ScheduledDate"]) . "</td>";
								echo "<td class='p-1'>" . htmlspecialchars($row["Status"]) . "</td>";
								echo "<td><div class='flex gap-1 p-1'>";

									// View button
									echo "<button data-modal-target='$viewModalId' data-modal-toggle='$viewModalId' class='w-full px-3 py-1 bg-blue-400 text-white rounded-md'>View</button>";

									// Delete button
									echo "<a href='php/plan-delete.php?id=$deleteId' onclick='return confirm(\"Are you sure you want to delete this plan?\")' class='text-center w-full px-3 py-1 bg-red-400 text-white rounded-md'>Delete</a>";

									echo "</div></td></tr>";

									// View Modal (read-only)
									$modals[] = "
									<div id='$viewModalId' data-modal-backdrop='static' tabindex='-1' aria-hidden='true' class='hidden fixed top-0 left-0 size-full z-50 items-center justify-center'>
										<div class='flex flex-col w-full max-w-md p-4 bg-white shadow-md rounded-md'>
											<span id='header' class='text-xl font-bold mb-2'>Audit Plan (View)</span>
											<div class='flex flex-col gap-3'>
												<div><strong>Plan ID:</strong> " . htmlspecialchars($row['PlanID']) . "</div>
												<div><strong>Title:</strong> " . htmlspecialchars($row['Title']) . "</div>
												<div><strong>Department:</strong> " . htmlspecialchars($row['Department']) . "</div>
												<div><strong>Scheduled Date:</strong> " . htmlspecialchars($row['ScheduledDate']) . "</div>
												<div><strong>Status:</strong> " . htmlspecialchars($row['Status']) . "</div>
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
											<span id='header' class='text-xl font-bold'>Audit Plan (Edit)</span>
											<form action='php/plan-update.php' method='POST' class='flex flex-col gap-3'>
												<span>" . htmlspecialchars($row['PlanID']) . "</span>
												<input type='hidden' name='PlanID' value='" . htmlspecialchars($row["PlanID"]) . "'>
												<div class='flex flex-row gap-3'>
													<div class='flex flex-col'>
														<label>Title:
															<input type='text' name='Title' value='" . htmlspecialchars($row["Title"]) . "' class='w-full border p-2 rounded'>
														</label>
													</div>
													<div class='flex flex-col'>
														<label>Department:
															<input type='text' name='Department' value='" . htmlspecialchars($row["Department"]) . "' class='w-full border p-2 rounded'>
														</label>
													</div>
												</div>
												<div class='flex flex-col'>
													<label>Scheduled Date:
														<input type='date' name='ScheduledDate' value='" . htmlspecialchars($row["ScheduledDate"]) . "' class='w-full border p-2 rounded'>
													</label>
												</div>
												<div class='flex flex-col'>
													<label>Status:
														<select name='Status' class='w-full border p-2 rounded'>
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
													<button type='submit' onclick='return confirm(\"Are you sure you want to edit this plan?\")' class='bg-green-600 text-white px-4 py-2 rounded-md'>Save</button>
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
				 <div id="plan-modal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="hidden absolute top-0 left-0 size-full z-50">
					<div class="w-auto h-fit p-4 flex flex-col gap-3 bg-white">
						<div class="flex flex-row justify-between items-center">
							<span id="header" class="text-2xl font-bold">New Plan</span>
							<button data-modal-target="plan-modal" data-modal-toggle="plan-modal" class="size-5 p-4 flex rounded-md font-bold items-center justify-center bg-red-200">X</button>
						</div>
						<form action="php/plan-submit.php" method="post" class="flex flex-col gap-3">
							<div class="flex flex-row gap-3">
								<div class="flex flex-col">
									<label for="title">Title:</label>
									<input type="text" name="Title" id="Title">
								</div>
								<div class="flex flex-col">
									<label for="department">Department:</label>
									<input type="text" name="Department" id="Department">
								</div>
							</div>
							<div class="flex flex-col">
								<label for="scheduled-date">Date:</label>
								<input type="date" name="ScheduledDate" id="ScheduledDate">
							</div>
							<div class="flex flex-col">
								<label for="description">Description:</label>
								<textarea name="Description" id="Description"></textarea>
							</div>
							<div class="flex justify-end">
								<button type="submit" class="px-3 py-2 bg-secondary text-white rounded-md">Submit</button>
							</div>
						</form>
					</div>
				 </div>
			</div>
		</div>
	</div>
</body>
	<script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
</html>