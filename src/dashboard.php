<?php 

include 'php/conn.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Cinzel&display=swap" rel="stylesheet">
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
			<div id="main" class="size-full bg-primary">

			</div>
		</div>
	</div>
</body>
	<script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
</html>