<?php
include '../php/conn.php';
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
				<a href="audit-plan.php" class="w-full flex flex-row gap-2 px-3 py-2 rounded-md border-2 border-white text-[#4E3B2A] hover:bg-accent hover:text-white transition-colors duration-200">
					<box-icon name='calendar-check' type='solid' color='#4E3B2A'></box-icon>
					<span>Audit Plan</span>
				</a>
				<a href="audit-conduct.php" class="w-full flex flex-row gap-2 px-3 py-2 rounded-md border-2 border-accent text-[#4E3B2A] hover:bg-accent hover:text-white transition-colors duration-200">
					<box-icon name='file-doc' type='solid' color='#4E3B2A'></box-icon>
					<span>Conduct Audit</span>
				</a>
				<a href="audit-findings.php" class="w-full flex flex-row gap-2 px-3 py-2 rounded-md border-2 border-accent text-[#4E3B2A] hover:bg-accent hover:text-white transition-colors duration-200">
					<box-icon name='search-alt-2' type='solid' color='#4E3B2A'></box-icon>
					<span>Findings</span>
				</a>
				<a href="audit-actions.php" class="w-full flex flex-row gap-2 px-3 py-2 rounded-md border-2 border-accent text-[#4E3B2A] hover:bg-accent hover:text-white transition-colors duration-200">
					<box-icon name='check-square' type='solid' color='#4E3B2A'></box-icon>
					<span>Corrective Actions</span>
				</a>
				<a href="audit-logs.php" class="w-full flex flex-row gap-2 px-3 py-2 rounded-md border-2 border-accent text-[#4E3B2A] hover:bg-accent hover:text-white transition-colors duration-200">
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
							<th class="px-4 py-2 whitespace-nowrap w-[10%]">
								<div class="flex items-center justify-start gap-2">
									<box-icon name='hash' color='white'></box-icon>
									Plan ID
								</div>
							</th>
							<th class="px-4 py-2 whitespace-nowrap w-[25%]">
								<div class="flex items-center justify-start gap-2">
									<box-icon name='text' color='white'></box-icon>
									Title
								</div>
							</th>
							<th class="px-4 py-2 whitespace-nowrap w-[20%]">
								<div class="flex items-center justify-start gap-2">
									<box-icon name='building' color='white'></box-icon>
									Department
								</div>
							</th>
							<th class="px-4 py-2 whitespace-nowrap w-[15%]">
								<div class="flex items-center justify-start gap-2">
									<box-icon name='calendar' color='white'></box-icon>
									Planned Date
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
						<?php
						$sql = "SELECT ap.*, a.ConductingBy, a.AuditID 
							   FROM auditplan ap 
							   LEFT JOIN audit a ON ap.PlanID = a.PlanID";
						$result = $conn->query($sql);
						$modals = []; // Initialize to avoid undefined variable

						if ($result->num_rows > 0) {
							while($row = $result->fetch_assoc()) {
								
								$viewModalId = "view-modal-" . $row["PlanID"];
								$editModalId = "edit-modal-" . $row["PlanID"];
								$deleteId = $row["PlanID"];

								// Fetch findings if audit exists
								$findingsHtml = '';
								if (!empty($row['AuditID'])) {
									$findingsResult = $conn->query("SELECT FindingID, Category, Description, LoggedAt FROM findings WHERE AuditID = " . intval($row['AuditID']));
									if ($findingsResult && $findingsResult->num_rows > 0) {
										$findingsHtml .= "<div class='mt-4'><strong>Findings:</strong><ul class='list-disc pl-5'>";
										while ($finding = $findingsResult->fetch_assoc()) {
											$findingsHtml .= "<li><span class='font-semibold'>" . htmlspecialchars($finding['Category']) . ":</span> " . 
														   htmlspecialchars($finding['Description']) . 
														   " <span class='text-xs text-gray-500'>(" . htmlspecialchars($finding['LoggedAt']) . ")</span></li>";
										}
										$findingsHtml .= "</ul></div>";
									} else {
										$findingsHtml .= "<div class='mt-4'><strong>Findings:</strong> <span class='text-gray-500'>None</span></div>";
									}
								}

								echo "<tr class='border-b-1 border-accent bg-white hover:bg-primary transition-colors duration-200'>";
								echo "<td class='px-4 py-2 whitespace-nowrap'>" . htmlspecialchars($row["PlanID"]) . "</td>";
								echo "<td class='px-4 py-2 whitespace-nowrap'>" . htmlspecialchars($row["Title"]) . "</td>";
								echo "<td class='px-4 py-2 whitespace-nowrap'>" . htmlspecialchars($row["Department"]) . "</td>";
								echo "<td class='px-4 py-2 whitespace-nowrap'>" . htmlspecialchars($row["ScheduledDate"]) . "</td>";
								echo "<td class='px-4 py-2'>
									<span class='px-2 py-1 rounded-full text-sm " . 
									($row["Status"] === 'Completed' ? 'bg-green-100 text-green-800' : 
									($row["Status"] === 'Under Review' ? 'bg-yellow-100 text-yellow-800' : 
									($row["Status"] === 'Cancelled' ? 'bg-red-100 text-red-800' : 
									'bg-gray-100 text-gray-800'))) . "'>
									" . htmlspecialchars($row["Status"]) . "
									</span>
								</td>";
								echo "<td class='px-4 py-2'><div class='flex gap-1'>
									<button data-modal-target='$viewModalId' data-modal-toggle='$viewModalId' class='flex justify-center items-center w-full px-3 py-2 bg-blue-400 text-white rounded-md'>View</button>
									<button onclick='handleDelete(\"$deleteId\")' class='text-center w-full px-3 py-2 bg-red-400 text-white rounded-md'>Delete</button>
								</div></td>";
								echo "</tr>";

								// View Modal (read-only)
								$modals[] = "
								<div id='$viewModalId' data-modal-backdrop='static' tabindex='-1' aria-hidden='true' class='hidden fixed top-0 left-0 size-full z-50 items-center justify-center'>
									<div class='flex flex-col w-full max-w-md h-[85vh] bg-white shadow-md rounded-md'>
										<div class='flex justify-between items-center p-4'>
											<span id='header' class='text-xl font-bold text-[#4E3B2A]'>View Audit Plan</span>
											<button data-modal-hide='$viewModalId' class='text-gray-400 bg-transparent hover:bg-primary transition-colors duration-200 rounded-lg text-sm w-8 h-8 flex justify-center items-center'>
												<box-icon name='x'></box-icon>
											</button>
										</div>
										<div class='flex-1 overflow-y-auto p-4' style='max-height: calc(85vh - 8rem);'>
											<div class='flex flex-col gap-2 mb-4 p-3 bg-gray-50 rounded-md'>
												<div><strong>Status:</strong> 
													<span class='px-2 py-1 rounded-full text-sm " . 
													($row["Status"] === 'Completed' ? 'bg-green-100 text-green-800' : 
													($row["Status"] === 'Under Review' ? 'bg-yellow-100 text-yellow-800' : 
													($row["Status"] === 'Cancelled' ? 'bg-red-100 text-red-800' : 
													'bg-gray-100 text-gray-800'))) . "'>
													" . htmlspecialchars($row["Status"]) . "
													</span>
												</div>
												<div><strong>Plan ID:</strong> " . htmlspecialchars($row['PlanID']) . "</div>
												<div><strong>Title:</strong> " . htmlspecialchars($row['Title']) . "</div>
												<div><strong>Department:</strong> " . htmlspecialchars($row['Department']) . "</div>
												<div><strong>Scheduled Date:</strong> " . htmlspecialchars($row['ScheduledDate']) . "</div>
												<div><strong>Description:</strong> " . nl2br(htmlspecialchars($row['Description'])) . "</div>
											</div>";

											// Conducting By Section
											$auditResult = $conn->query("SELECT AuditID, ConductingBy, ConductedAt, Status FROM audit WHERE PlanID = " . intval($row['PlanID']));
											
											$modals[] = "<div class='mt-2'>
												<strong class='text-[#4E3B2A]'>Audit</strong>";
											
											if ($auditResult && $auditResult->num_rows > 0) {
												$modals[] = "<div class='mt-2 space-y-2'>";
												while ($audit = $auditResult->fetch_assoc()) {
													$modals[] = "
														<div class='p-3 bg-white rounded border border-accent'>
															<div class='mb-1'><strong>Status:</strong> 
																<span class='px-2 py-1 rounded-full text-sm " . 
																($audit['Status'] === 'Completed' ? 'bg-green-100 text-green-800' : 
																($audit['Status'] === 'Under Review' ? 'bg-yellow-100 text-yellow-800' : 
																'bg-blue-100 text-blue-800')) . "'>
																" . htmlspecialchars($audit['Status']) . "
																</span>
															</div>
															<div class='mb-1'><strong>Conducting By:</strong> " . htmlspecialchars($audit['ConductingBy']) . "</div>
															<div><strong>Conducted At:</strong> " . htmlspecialchars($audit['ConductedAt']) . "</div>
														</div>";
												}
												$modals[] = "</div>";
											} else {
												$modals[] = "<div class='mt-2 p-3 text-gray-500'>No audits conducted yet</div>";
											}
											$modals[] = "</div>";

											// Findings Section
											if (!empty($row['AuditID'])) {
												$findingsResult = $conn->query("SELECT FindingID, Category, Description, LoggedAt FROM findings WHERE AuditID = " . intval($row['AuditID']));
												
												$modals[] = "<div class='mt-2'>
													<strong class='text-[#4E3B2A]'>Findings</strong>";
												
												if ($findingsResult && $findingsResult->num_rows > 0) {
													$modals[] = "<div class='mt-2 space-y-2'>";
													while ($finding = $findingsResult->fetch_assoc()) {
														$modals[] = "
															<div class='p-3 bg-white rounded border border-accent'>
																                                                <div class='mb-1'><strong>Status:</strong> 
                                                    <span class='px-2 py-1 rounded-full text-sm " . 
                                                    ($finding['Category'] === 'Compliant' ? 'bg-green-100 text-green-800' : 
                                                    ($finding['Category'] === 'Non-Compliant' ? 'bg-red-100 text-red-800' : 
                                                    'bg-yellow-100 text-yellow-800')) . "'>
                                                    " . htmlspecialchars($finding['Category']) . "
                                                    </span>
                                                </div>
                                                <div class='mb-1'><strong>Description:</strong> " . htmlspecialchars($finding['Description']) . "</div>
                                                <div><strong>Logged At:</strong> <span class='text-sm text-gray-600'>" . htmlspecialchars($finding['LoggedAt']) . "</span></div>
															</div>";
													}
													$modals[] = "</div>";
												} else {
													$modals[] = "<div class='mt-2 p-3 text-gray-500'>No findings logged</div>";
												}
												$modals[] = "</div>";

												// Corrective Actions Section
												$modals[] = "<div class='mt-2'>
													<strong class='text-[#4E3B2A]'>Corrective Actions</strong>";

												$findingsResult->data_seek(0);
												$hasActions = false;
												if ($findingsResult && $findingsResult->num_rows > 0) {
													$modals[] = "<div class='mt-2 space-y-2'>";
													while ($finding = $findingsResult->fetch_assoc()) {
														$actionsResult = $conn->query("SELECT ActionID, AssignedTo, Task, DueDate, Status FROM correctiveactions WHERE FindingID = " . intval($finding['FindingID']));
														
														if ($actionsResult && $actionsResult->num_rows > 0) {
															$hasActions = true;
															while ($action = $actionsResult->fetch_assoc()) {
																$modals[] = "
																	<div class='p-3 bg-white rounded border border-accent'>
																		<div class='mb-1'><strong>Status:</strong> 
																			<span class='px-2 py-1 rounded-full text-sm " . 
																			($action['Status'] === 'Completed' ? 'bg-green-100 text-green-800' : 
																			($action['Status'] === 'Failed' ? 'bg-red-100 text-red-800' : 
																			($action['Status'] === 'Under Review' ? 'bg-yellow-100 text-yellow-800' : 
																			'bg-blue-100 text-blue-800'))) . "'>
																			" . htmlspecialchars($action['Status']) . "
																			</span>
																		</div>
																		<div class='mb-1'><strong>Assigned To:</strong> " . htmlspecialchars($action['AssignedTo']) . "</div>
																		<div class='mb-1'><strong>Task:</strong> " . htmlspecialchars($action['Task']) . "</div>
																		<div><strong>Due Date:</strong> " . htmlspecialchars($action['DueDate']) . "</div>
																	</div>";
															}
														}
													}
													$modals[] = "</div>";
													if (!$hasActions) {
														$modals[] = "<div class='mt-2 p-3 text-gray-500'>No corrective actions assigned</div>";
													}
												}
												$modals[] = "</div>";
											} else {
												$modals[] = "
													<div class='mt-2'>
														<strong class='text-[#4E3B2A]'>Findings</strong>
														<div class='mt-2 p-3 text-gray-500'>No audit conducted yet</div>
													</div>
													<div class='mt-2'>
														<strong class='text-[#4E3B2A]'>Corrective Actions</strong>
														<div class='mt-2 p-3 text-gray-500'>No audit conducted yet</div>
													</div>";
											}

											$modals[] = "
										</div>
										<div class='flex justify-end gap-2 mt-4 p-4 border-t border-gray-200'>
											" . ($row['Status'] !== 'Completed' && $row['Status'] !== 'Cancelled' ? "<button type='button' data-modal-hide='$viewModalId' data-modal-target='$editModalId' data-modal-toggle='$editModalId' class='bg-green-600 text-white px-4 py-2 rounded-md'>Edit</button>" : "") . "
										</div>
									</div>
								</div>";

								// Edit Modal (form)
								$modals[] = "
								<div id='$editModalId' data-modal-backdrop='static' tabindex='-1' aria-hidden='true' class='hidden fixed top-0 left-0 size-full z-50 items-center justify-center'>
									<div class='flex flex-col w-full max-w-md p-4 bg-white shadow-md rounded-md'>
										<div class='flex justify-between items-center mb-4'>
											<span id='header' class='text-xl font-bold text-[#4E3B2A]'>Audit Plan</span>
											<button data-modal-hide='$editModalId' class='text-gray-400 bg-transparent hover:bg-primary transition-colors duration-200 rounded-lg text-sm w-8 h-8 flex justify-center items-center'>
												<box-icon name='x'></box-icon>
											</button>
										</div>
										<form id='editPlanForm' action='../php/plan-update.php' method='POST' class='flex flex-col gap-3'>
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
						<form id="newPlanForm" onsubmit="handleCreate(event)" action="../php/plan-submit.php" method="post" class="flex flex-col gap-3">
							<div class="flex flex-row gap-3">
								<div class="flex flex-col flex-1">
									<label for="title" class="mb-1">Title:</label>
									<input type="text" name="Title" id="Title" placeholder="Enter audit plan title..." class="w-full border p-2 rounded" required>
								</div>
								<div class="flex flex-col flex-1">
									<label for="department" class="mb-1">Department:</label>
									<input type="text" name="Department" id="Department" placeholder="Enter department name..." class="w-full border p-2 rounded" required>
								</div>
							</div>
							<div class="flex flex-col">
								<label for="scheduled-date" class="mb-1">Scheduled Date:</label>
								<input type="date" name="ScheduledDate" id="ScheduledDate" class="w-full border p-2 rounded" required>
							</div>
							<div class="flex flex-col">
								<label for="description" class="mb-1">Description:</label>
								<textarea name="Description" id="Description" placeholder="Enter audit plan description..." class="w-full border p-2 rounded min-h-[100px]" required></textarea>
							</div>
							<div class="flex justify-end gap-2 mt-2">
								<button type="submit" class="px-4 py-2 bg-secondary text-white rounded-md hover:bg-opacity-90">Submit</button>
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
	<script src="../js/sweetalert.js"></script>
	<script>
		// Handle Delete
		async function handleDelete(planId) {
			try {
				// Always show initial confirmation first
				const initialConfirm = await Swal.fire({
					title: 'Delete Confirmation',
					text: 'Are you sure you want to delete this audit plan?',
					icon: 'warning',
					showCancelButton: true,
					confirmButtonText: 'Yes, proceed',
					cancelButtonText: 'No, cancel',
					customClass: {
						popup: 'rounded-lg',
						title: 'text-[#4E3B2A]',
						content: 'text-[#4E3B2A]',
						confirmButton: 'bg-red-500 text-white px-4 py-2 rounded-md',
						cancelButton: 'bg-gray-400 text-white px-4 py-2 rounded-md'
					}
				});

				// Stop if user cancels
				if (!initialConfirm.isConfirmed) {
					return;
				}

				// Now check for associated audits
				const checkResponse = await fetch(`../php/plan-delete.php?id=${planId}`);
				const checkData = await checkResponse.json();

				// If the plan was already deleted
				if (!checkData.success && checkData.alreadyDeleted) {
					await showSuccessWithRefresh('Plan has been deleted');
					return;
				}

				// If there are associated audits
				if (!checkData.success && checkData.requireForce) {
					const forceConfirm = await Swal.fire({
						title: 'Warning!',
						html: `This plan has ${checkData.auditCount} associated audit${checkData.auditCount > 1 ? 's' : ''}.<br>Deleting this plan will also delete all associated audits and their findings.<br><br>Do you want to proceed?`,
						icon: 'warning',
						showCancelButton: true,
						confirmButtonText: 'Yes, delete everything',
						cancelButtonText: 'No, cancel',
						customClass: {
							popup: 'rounded-lg',
							title: 'text-[#4E3B2A]',
							content: 'text-[#4E3B2A]',
							confirmButton: 'bg-red-500 text-white px-4 py-2 rounded-md',
							cancelButton: 'bg-gray-400 text-white px-4 py-2 rounded-md'
						}
					});

					// Stop if user cancels force delete
					if (!forceConfirm.isConfirmed) {
						return;
					}

					// Proceed with force delete
					showLoading('Deleting plan and associated audits...');
					const deleteResponse = await fetch(`../php/plan-delete.php?id=${planId}&force=1`);
					const deleteData = await deleteResponse.json();

					if (!deleteData.success) {
						throw new Error(deleteData.message || 'Failed to delete plan and associated audits');
					}

					await showSuccessWithRefresh(deleteData.message);
				} else {
					// No associated audits, proceed with normal delete
					showLoading('Deleting audit plan...');
					const deleteResponse = await fetch(`../php/plan-delete.php?id=${planId}&confirm=1`);
					const deleteData = await deleteResponse.json();

					if (!deleteData.success) {
						if (deleteData.alreadyDeleted) {
							await showSuccessWithRefresh('Plan has been deleted');
							return;
						}
						throw new Error(deleteData.message || 'Failed to delete plan');
					}

					await showSuccessWithRefresh(deleteData.message);
				}
			} catch (error) {
				console.error('Delete error:', error);
				await showError(error.message);
				// Refresh the page if we got an error, as the plan might have been deleted anyway
				if (error.message.includes('not found')) {
					window.location.reload();
				}
			}
		}

		// Handle Edit
		async function handleEdit(form) {
			try {
				showLoading('Updating audit plan...');
				const formData = new FormData(form);
				const response = await fetch('../php/plan-update.php', {
					method: 'POST',
					body: formData
				});
				
				let data;
				const contentType = response.headers.get('content-type');
				if (contentType && contentType.includes('application/json')) {
					data = await response.json();
				} else {
					// If not JSON, get the text content for error message
					const text = await response.text();
					throw new Error(text || 'Failed to update plan');
				}
				
				if (!response.ok || !data.success) {
					throw new Error(data.message || 'Failed to update plan');
				}
				
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
				const response = await fetch('../php/plan-submit.php', {
					method: 'POST',
					body: formData
				});
				
				let data;
				const contentType = response.headers.get('content-type');
				if (contentType && contentType.includes('application/json')) {
					data = await response.json();
				} else {
					// If not JSON, get the text content for error message
					const text = await response.text();
					throw new Error(text || 'Failed to create plan');
				}
				
				if (!response.ok || !data.success) {
					throw new Error(data.message || 'Failed to create plan');
				}
				
				showCreateSuccess('Audit plan created successfully');
				setTimeout(() => location.reload(), 2000);
			} catch (error) {
				showError(error.message);
			}
		}
	</script>
</body>
</html>