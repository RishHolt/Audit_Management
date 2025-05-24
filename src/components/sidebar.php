<?php
// Sidebar component for Audit Management
?>
<div id="sidebar" class="flex flex-col gap-3 bg-white px-3 py-2 border-accent border-r-2 min-w-70 h-full">
	<span id="header" class="w-full h-fit font-bold text-[#4E3B2A] text-2xl text-center">Audit Management</span>
	<a href="dashboard.php" class="flex flex-row gap-2 hover:bg-accent px-3 py-2 rounded-md w-full text-[#4E3B2A] transition-colors duration-200">
		<box-icon name='dashboard' type='solid' color='#4E3B2A'></box-icon>
		<span>Dashboard</span>
	</a>
	<a href="audit-plan.php" class="flex flex-row gap-2 hover:bg-accent px-3 py-2 rounded-md w-full text-[#4E3B2A] transition-colors duration-200">
		<box-icon name='calendar-check' type='solid' color='#4E3B2A'></box-icon>
		<span>Audit Plan</span>
	</a>
	<a href="audit-conduct.php" class="flex flex-row gap-2 hover:bg-accent px-3 py-2 rounded-md w-full text-[#4E3B2A] transition-colors duration-200">
		<box-icon name='file-doc' type='solid' color='#4E3B2A'></box-icon>
		<span>Conduct Audit</span>
	</a>
	<a href="audit-findings.php" class="flex flex-row gap-2 hover:bg-accent px-3 py-2 rounded-md w-full text-[#4E3B2A] transition-colors duration-200">
		<box-icon name='search-alt-2' type='solid' color='#4E3B2A'></box-icon>
		<span>Findings</span>
	</a>
	<a href="audit-actions.php" class="flex flex-row gap-2 hover:bg-accent px-3 py-2 rounded-md w-full text-[#4E3B2A] transition-colors duration-200">
		<box-icon name='check-square' type='solid' color='#4E3B2A'></box-icon>
		<span>Corrective Actions</span>
	</a>
	<a href="audit-logs.php" class="flex flex-row gap-2 hover:bg-accent px-3 py-2 rounded-md w-full text-[#4E3B2A] transition-colors duration-200">
		<box-icon name='time-five' type='solid' color='#4E3B2A'></box-icon>
		<span>Audit Logs</span>
	</a>
</div>
