<?php 

include '../php/conn.php';

// Fetch audit plans for dropdown (not completed/cancelled)
$plans = [];
$planResult = $conn->query("
    SELECT PlanID, Title 
    FROM auditplan 
    WHERE Status NOT IN ('Completed', 'Cancelled')
    AND PlanID NOT IN (SELECT PlanID FROM audit)
");
if ($planResult && $planResult->num_rows > 0) {
    while ($plan = $planResult->fetch_assoc()) {
        $plans[] = $plan;
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
                <span id="header" class="font-bold text-[#4E3B2A] text-2xl">Conduct Audit</span>
                <!-- Conduct Audit button and other links -->
                <div class="flex items-center">
                    <div class="flex justify-start">
                        <button data-modal-target="conduct-modal" data-modal-toggle="conduct-modal" class="flex items-center gap-2 bg-accent fill-text px-3 py-2 rounded-md text-text">
                            <box-icon name='plus-circle' type='solid'></box-icon>
                            <span>Conduct Audit</span>
                        </button>
                    </div>
                    <div class="flex flex-1 justify-end gap-2">
                        <a href="general-ledger.php" class="flex items-center gap-2 bg-secondary px-3 py-2 rounded-md text-white transition-colors duration-200">
                            <box-icon name='book' type='solid' color='white'></box-icon>
                            <span>General Ledger</span>
                        </a>
                        <a href="procurement.php" class="flex items-center gap-2 bg-secondary px-3 py-2 rounded-md text-white transition-colors duration-200">
                            <box-icon name='cart' type='solid' color='white'></box-icon>
                            <span>Procurement</span>
                        </a>
                        <a href="suppliers.php" class="flex items-center gap-2 bg-secondary px-3 py-2 rounded-md text-white transition-colors duration-200">
                            <box-icon name='user' type='solid' color='white'></box-icon>
                            <span>Suppliers</span>
                        </a>
                        <a href="vendor-portal.php" class="flex items-center gap-2 bg-secondary px-3 py-2 rounded-md text-white transition-colors duration-200">
                            <box-icon name='store' type='solid' color='white'></box-icon>
                            <span>Vendor Portal</span>
                        </a>
                    </div>
                </div>

                <!-- Conduct Audit Modal -->
                <div id="conduct-modal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="hidden top-0 left-0 z-50 absolute size-full">
                    <div class="flex flex-col bg-white shadow-md p-4 rounded-md w-full max-w-md">
                        <div class="flex justify-between items-center mb-4">
                            <span id="header" class="font-bold text-[#4E3B2A] text-xl">Conduct Audit</span>
                            <button data-modal-hide="conduct-modal" class="flex justify-center items-center bg-transparent hover:bg-primary rounded-lg w-8 h-8 text-gray-400 text-sm transition-colors duration-200">
                                <box-icon name='x'></box-icon>
                            </button>
                        </div>
                        <div class="mb-4">
                            <div class="flex gap-3">
                                <button type="button" onclick="showAuditForm('plan')" class="flex-1 bg-secondary px-3 py-2 rounded-md text-white">Select Plan Audit</button>
                                <button type="button" onclick="showAuditForm('custom')" class="flex-1 bg-secondary px-3 py-2 rounded-md text-white">Create Custom Audit</button>
                            </div>
                        </div>
                        <!-- Plan Audit Form -->
                        <form id="planAuditForm" onsubmit="handleConductAudit(event)" action="../php/conduct-audit.php" method="post" class="hidden flex-col gap-3">
                            <div class="flex flex-col">
                                <label for="PlanID">Select Audit Plan:</label>
                                <select name="PlanID" id="PlanID" required class="bg-white px-3 py-2 border focus:border-accent rounded-lg focus:ring-2 focus:ring-accent">
                                    <option value="">Select an audit plan...</option>
                                    <?php foreach ($plans as $plan): ?>
                                        <option value="<?= htmlspecialchars($plan['PlanID']) ?>">
                                            <?= htmlspecialchars($plan['Title']) ?> (ID: <?= $plan['PlanID'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="flex flex-col">
                                <label for="planConductingBy">Conducting By:</label>
                                <input type="text" name="ConductingBy" id="planConductingBy" required placeholder="Enter conductor's name..." class="bg-white px-3 py-2 border focus:border-accent rounded-lg focus:ring-2 focus:ring-accent">
                            </div>
                            <input type="hidden" name="Status" value="Pending">
                            <div class="flex justify-end">
                                <button type="submit" class="bg-secondary px-3 py-2 rounded-md text-white">Submit</button>
                            </div>
                        </form>
                        
                        <!-- Custom Audit Form -->
                        <form id="customAuditForm" onsubmit="handleCustomAudit(event)" action="../php/conduct-audit.php" method="post" class="hidden flex-col gap-3">
                            <div class="flex flex-col">
                                <label for="Title">Audit Title:</label>
                                <input type="text" name="Title" id="Title" required placeholder="Enter audit title..." class="bg-white px-3 py-2 border focus:border-accent rounded-lg focus:ring-2 focus:ring-accent">
                            </div>
                            <div class="flex flex-col">
                                <label for="Description">Description:</label>
                                <textarea name="Description" id="Description" required placeholder="Enter audit description..." rows="3" class="bg-white px-3 py-2 border focus:border-accent rounded-lg focus:ring-2 focus:ring-accent"></textarea>
                            </div>
                            <div class="flex flex-col">
                                <label for="Department">Department:</label>
                                <input type="text" name="Department" id="Department" required placeholder="Enter department name..." class="bg-white px-3 py-2 border focus:border-accent rounded-lg focus:ring-2 focus:ring-accent">
                            </div>
                            <div class="flex flex-col">
                                <label for="customConductingBy">Conducting By:</label>
                                <input type="text" name="ConductingBy" id="customConductingBy" required placeholder="Enter conductor's name..." class="bg-white px-3 py-2 border focus:border-accent rounded-lg focus:ring-2 focus:ring-accent">
                            </div>
                            <input type="hidden" name="Status" value="Pending">
                            <input type="hidden" name="isCustom" value="1">
                            <div class="flex justify-end">
                                <button type="submit" class="bg-secondary px-3 py-2 rounded-md text-white">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>

                <table class="w-full border-collapse table-auto">
                    <tr class="bg-secondary text-white">
                        <th class="px-4 py-2 w-[10%] whitespace-nowrap">
                            <div class="flex justify-start items-center gap-2">
                                <box-icon name='hash' color='white'></box-icon>
                                Audit ID
                            </div>
                        </th>
                        <th class="px-4 py-2 w-[10%] whitespace-nowrap">
                            <div class="flex justify-start items-center gap-2">
                                <box-icon name='file' color='white'></box-icon>
                                Plan ID
                            </div>
                        </th>
                        <th class="px-4 py-2 w-[20%] whitespace-nowrap">
                            <div class="flex justify-start items-center gap-2">
                                <box-icon name='text' color='white'></box-icon>
                                Title
                            </div>
                        </th>
                        <th class="px-4 py-2 w-[15%] whitespace-nowrap">
                            <div class="flex justify-start items-center gap-2">
                                <box-icon name='user' color='white'></box-icon>
                                Conducting By
                            </div>
                        </th>
                        <th class="px-4 py-2 w-[15%] whitespace-nowrap">
                            <div class="flex justify-start items-center gap-2">
                                <box-icon name='time' color='white'></box-icon>
                                Conducted At
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
                    <?php
                    // Fetch conducted audits
                    $auditResult = $conn->query(
                        "SELECT ac.AuditID, ac.PlanID, 
                                CASE 
                                    WHEN ac.PlanID IS NULL THEN ac.Title
                                    ELSE ap.Title 
                                END as Title,
                                ac.ConductingBy, ac.ConductedAt, ac.Status
                         FROM audit ac
                         LEFT JOIN auditplan ap ON ac.PlanID = ap.PlanID
                         ORDER BY ac.AuditID DESC"
                    );
                    $auditModals = [];
                    if ($auditResult && $auditResult->num_rows > 0) {
                        while ($audit = $auditResult->fetch_assoc()) {
                            $viewAuditModalId = "view-audit-modal-" . $audit["AuditID"];
                            echo "<tr class='bg-white hover:bg-primary border-accent border-b-1 transition-colors duration-200'>";
                            echo "<td class='px-4 py-2 whitespace-nowrap'>" . htmlspecialchars($audit["AuditID"]) . "</td>";
                            echo "<td class='px-4 py-2 whitespace-nowrap'>" . htmlspecialchars($audit["PlanID"]) . "</td>";
                            echo "<td class='px-4 py-2 whitespace-nowrap'>" . htmlspecialchars($audit["Title"]) . "</td>";
                            echo "<td class='px-4 py-2 whitespace-nowrap'>" . htmlspecialchars($audit["ConductingBy"]) . "</td>";
                            echo "<td class='px-4 py-2 whitespace-nowrap'>" . htmlspecialchars($audit["ConductedAt"]) . "</td>";
                            
                            // Fetch findings for this audit
                            $findingsHtml = '';
                            $findingsResult = $conn->query("SELECT FindingID, Category, Description, LoggedAt FROM findings WHERE AuditID = " . intval($audit['AuditID']));

                            // Determine if all findings are Compliant
                            $allCompliant = true;
                            $hasFindings = false;
                            if ($findingsResult && $findingsResult->num_rows > 0) {
                                $findingsHtml .= "<div class='mt-4'><strong>Findings:</strong><ul class='pl-5 list-disc'>";
                                while ($finding = $findingsResult->fetch_assoc()) {
                                    $hasFindings = true;
                                    if ($finding['Category'] !== 'Compliant') {
                                        $allCompliant = false;
                                    }
                                    $findingsHtml .= "<li><span class='font-semibold'>" . htmlspecialchars($finding['Category']) . ":</span> " . htmlspecialchars($finding['Description']) . " <span class='text-gray-500 text-xs'>(" . htmlspecialchars($finding['LoggedAt']) . ")</span></li>";
                                }
                                $findingsHtml .= "</ul></div>";

                                // If all findings are compliant and status is Pending, update to Under Review
                                if ($allCompliant && $audit['Status'] === 'Pending') {
                                    $conn->query("UPDATE audit SET Status = 'Under Review' WHERE AuditID = " . intval($audit['AuditID']));
                                    $audit['Status'] = 'Under Review';
                                }
                            } else {
                                $findingsHtml .= "<div class='mt-4'><strong>Findings:</strong> <span class='text-gray-500'>None</span></div>";
                            }

                            // Show status based on current state
                            $displayStatus = $audit['Status'];

                            echo "<td class='px-4 py-2'>
                                <span class='px-2 py-1 rounded-full text-sm " . ($displayStatus ==='Completed' ? 'bg-green-100 text-green-800' : 
                                ($displayStatus === 'Under Review' ? 'bg-yellow-100 text-yellow-800' : 
                                ($displayStatus === 'Pending' ? 'bg-blue-100 text-blue-800' : 
                                'bg-gray-100 text-gray-800'))) . "'>
                                " . htmlspecialchars($displayStatus) . "
                                </span>
                            </td>";
                            echo "<td class='px-4 py-2'>
                                <div class='flex gap-1'>
                                    <button data-modal-target='view-audit-modal-" . $audit["AuditID"] . "' data-modal-toggle='view-audit-modal-" . $audit["AuditID"] . "' class='bg-blue-400 px-3 py-2 rounded-md w-full text-white'>View</button>
                                    <button onclick='handleDelete(\"" . $audit["AuditID"] . "\")' class='bg-red-400 px-3 py-2 rounded-md w-full text-white'>Delete</button>
                                </div>
                            </td>";
                            echo "</tr>";

                            // View Audit Modal (read-only)
                            $auditModals[] = "
                            <div id='view-audit-modal-" . $audit["AuditID"] . "' data-modal-backdrop='static' tabindex='-1' aria-hidden='true' class='hidden top-0 left-0 z-50 fixed justify-center items-center size-full'>
                                <div class='flex flex-col bg-white shadow-md p-4 rounded-md w-full max-w-md'>
                                    <div class='flex justify-between items-center mb-4'>
                                        <span id='header' class='font-bold text-[#4E3B2A] text-xl'>Audit Details</span>
                                        <button data-modal-hide='view-audit-modal-" . $audit["AuditID"] . "' class='flex justify-center items-center bg-transparent hover:bg-primary rounded-lg w-8 h-8 text-gray-400 text-sm transition-colors duration-200'>
                                            <box-icon name='x'></box-icon>
                                        </button>
                                    </div>
                                    <div class='flex flex-col gap-2 bg-gray-50 mb-4 p-3 rounded-md'>
                                        <div><strong>Status:</strong> 
                                            <span class='px-2 py-1 rounded-full text-sm " . ($displayStatus ==='Completed' ? 'bg-green-100 text-green-800' : 
                                            ($displayStatus === 'Under Review' ? 'bg-yellow-100 text-yellow-800' : 
                                            ($displayStatus === 'Pending' ? 'bg-blue-100 text-blue-800' : 
                                            'bg-gray-100 text-gray-800'))) . "'>
                                            " . htmlspecialchars($displayStatus) . "
                                            </span>
                                        </div>
                                        <div><strong>Audit ID:</strong> " . htmlspecialchars($audit['AuditID']) . "</div>
                                        <div><strong>Plan ID:</strong> " . htmlspecialchars($audit['PlanID']) . "</div>
                                        <div><strong>Title:</strong> " . htmlspecialchars($audit['Title']) . "</div>
                                        <div><strong>Conducting By:</strong> " . htmlspecialchars($audit['ConductingBy']) . "</div>
                                        <div><strong>Conducted At:</strong> " . htmlspecialchars($audit['ConductedAt']) . "</div>
                                    </div>";
                            
                            // Add findings section
                            if ($findingsResult && $findingsResult->num_rows > 0) {
                                $auditModals[] = "
                                    <div class='mt-2'>
                                        <strong class='text-[#4E3B2A]'>Findings</strong>
                                        <div class='space-y-2 mt-2'>";
                                
                                $findingsResult->data_seek(0); // Reset the pointer to start
                                while ($finding = $findingsResult->fetch_assoc()) {
                                    $auditModals[] = "
                                            <div class='bg-white p-3 border border-accent rounded'>
                                                <div class='mb-1'><strong>Status:</strong> 
                                                    <span class='px-2 py-1 rounded-full text-sm " . ($finding['Category'] === 'Compliant' ? 'bg-green-100 text-green-800' : 
                                                    ($finding['Category'] === 'Non-Compliant' ? 'bg-red-100 text-red-800' : 
                                                    'bg-yellow-100 text-yellow-800')) . "'>
                                                    " . htmlspecialchars($finding['Category']) . "
                                                    </span>
                                                </div>
                                                <div class='mb-1'><strong>Description:</strong> " . htmlspecialchars($finding['Description']) . "</div>
                                                <div><strong>Logged At:</strong> <span class='text-gray-600 text-sm'>" . htmlspecialchars($finding['LoggedAt']) . "</span></div>
                                            </div>";
                                }
                                
                                $auditModals[] = "
                                        </div>
                                    </div>";
                            } else {
                                $auditModals[] = "
                                    <div class='mt-2'>
                                        <strong class='text-[#4E3B2A]'>Findings</strong>
                                        <div class='mt-2 p-3 text-gray-500'>No findings logged</div>
                                    </div>";
                            }

                            $auditModals[] = "
                                    <div class='flex justify-end gap-2 mt-4'>
                                        <button data-modal-hide='view-audit-modal-" . $audit["AuditID"] . "' data-modal-target='edit-audit-modal-" . $audit["AuditID"] . "' data-modal-toggle='edit-audit-modal-" . $audit["AuditID"] . "' class='bg-green-600 px-4 py-2 rounded-md text-white'>Edit</button>
                                        " . ($displayStatus === 'Under Review' ? "<button onclick='handleComplete(" . htmlspecialchars($audit['AuditID']) . ")' class='bg-green-600 px-4 py-2 rounded-md text-white'>Mark as Complete</button>" : "") . "
                                    </div>
                                </div>
                            </div>";

                            // Edit Audit Modal
                            $auditModals[] = "
                            <div id='edit-audit-modal-" . $audit["AuditID"] . "' data-modal-backdrop='static' tabindex='-1' aria-hidden='true' class='hidden top-0 left-0 z-50 fixed justify-center items-center size-full'>
                                <div class='flex flex-col bg-white shadow-md p-4 rounded-md w-full max-w-md'>
                                    <div class='flex justify-between items-center mb-4'>
                                        <span id='header' class='font-bold text-[#4E3B2A] text-xl'>Edit Audit</span>
                                        <button data-modal-hide='edit-audit-modal-" . $audit["AuditID"] . "' class='flex justify-center items-center bg-transparent hover:bg-primary rounded-lg w-8 h-8 text-gray-400 text-sm transition-colors duration-200'>
                                            <box-icon name='x'></box-icon>
                                        </button>
                                    </div>
                                    <form onsubmit='handleEditAudit(event)' class='flex flex-col gap-3'>
                                        <input type='hidden' name='AuditID' value='" . htmlspecialchars($audit['AuditID']) . "'>
                                        <div class='flex flex-col'>
                                            <label for='Title'>Title:</label>
                                            <input type='text' name='Title' id='Title' value='" . htmlspecialchars($audit['Title']) . "' required class='bg-white px-3 py-2 border focus:border-accent rounded-lg focus:ring-2 focus:ring-accent'>
                                        </div>
                                        <div class='flex flex-col'>
                                            <label for='ConductingBy'>Conducting By:</label>
                                            <input type='text' name='ConductingBy' id='ConductingBy' value='" . htmlspecialchars($audit['ConductingBy']) . "' required class='bg-white px-3 py-2 border focus:border-accent rounded-lg focus:ring-2 focus:ring-accent'>
                                        </div>
                                        <div class='flex justify-end gap-2'>
                                            <button type='submit' class='bg-secondary px-4 py-2 rounded-md text-white'>Save Changes</button>
                                            <button type='button' data-modal-hide='edit-audit-modal-" . $audit["AuditID"] . "' class='bg-gray-400 px-4 py-2 rounded-md text-white'>Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-[#4E3B2A] text-center'>No conducted audits found.</td></tr>";
                    }
                    ?>
                </table>
                <?php
                // Output audit modals
                if (!empty($auditModals)) {
                    foreach ($auditModals as $modal) {
                        echo $modal;
                    }
                }
                ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom SweetAlert2 Utility Functions -->
    <script src="../js/sweetalert.js"></script>
    <script>
        // Function to show/hide forms
        function showAuditForm(type) {
            const planForm = document.getElementById('planAuditForm');
            const customForm = document.getElementById('customAuditForm');
            
            if (type === 'plan') {
                planForm.classList.remove('hidden');
                planForm.classList.add('flex');
                customForm.classList.remove('flex');
                customForm.classList.add('hidden');
            } else {
                customForm.classList.remove('hidden');
                customForm.classList.add('flex');
                planForm.classList.remove('flex');
                planForm.classList.add('hidden');
            }
        }

        // Show plan form by default when modal opens
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('conduct-modal');
            if (modal) {
                modal.addEventListener('shown.bs.modal', function() {
                    showAuditForm('plan');
                });
            }
        });

        // Handle Plan-based Audit
        async function handleConductAudit(event) {
            event.preventDefault();
            try {
                showLoading('Starting plan-based audit...');
                const form = event.target;
                const formData = new FormData(form);
                
                const response = await fetch('../php/conduct-audit.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                if (!data.success) {
                    throw new Error(data.message || 'Failed to start audit');
                }

                await showSuccessWithRefresh(data.message);
            } catch (error) {
                console.error('Conduct audit error:', error);
                await showError(error.message);
            } finally {
                closeLoading();
            }
        }

        // Handle Custom Audit
        async function handleCustomAudit(event) {
            event.preventDefault();
            try {
                showLoading('Creating custom audit...');
                const form = event.target;
                const formData = new FormData(form);
                formData.append('isCustom', '1'); // Ensure isCustom is set
                
                const response = await fetch('../php/conduct-audit.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                if (!data.success) {
                    throw new Error(data.message || 'Failed to create custom audit');
                }

                await showSuccessWithRefresh(data.message);
            } catch (error) {
                console.error('Custom audit error:', error);
                await showError(error.message);
            } finally {
                closeLoading();
            }
        }

        // Handle Custom Audit
        async function handleCustomAudit(event) {
            event.preventDefault();
            try {
                showLoading('Starting custom audit...');
                const form = event.target;
                const formData = new FormData(form);
                const response = await fetch('../php/conduct-audit.php', {
                    method: 'POST',
                    body: formData
                });
                
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Unexpected response from server');
                }

                const data = await response.json();
                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Failed to start custom audit');
                }
                
                await showSuccessWithRefresh(data.message);
            } catch (error) {
                console.error('Custom audit error:', error);
                await showError(error.message);
            } finally {
                closeLoading();
            }
        }
        // Handle Conduct Audit
        async function handleConductAudit(event) {
            event.preventDefault();
            try {
                showLoading('Starting audit...');
                const form = event.target;
                const formData = new FormData(form);
                const response = await fetch('../php/conduct-audit.php', {
                    method: 'POST',
                    body: formData
                });
                
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Unexpected response from server');
                }

                const data = await response.json();
                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Failed to start audit');
                }
                
                await showSuccessWithRefresh(data.message);
            } catch (error) {
                console.error('Conduct audit error:', error);
                await showError(error.message);
            } finally {
                closeLoading();
            }
        }

        // Handle Edit Audit
        async function handleEditAudit(event) {
            event.preventDefault();
            try {
                const form = event.target;
                const formData = new FormData(form);

                // Get original values from the view modal
                const auditId = formData.get('AuditID');
                const viewModal = document.getElementById(`view-audit-modal-${auditId}`);
                
                // Helper function to get the value part of a label-value div
                const getValueFromLabel = (container, label) => {
                    const div = Array.from(container.querySelectorAll('div')).find(el => el.textContent.includes(`${label}:`));
                    return div ? div.textContent.split(':')[1].trim() : '';
                };

                // Get original values
                const originalTitle = getValueFromLabel(viewModal, 'Title');
                const originalConductingBy = getValueFromLabel(viewModal, 'Conducting By');
                const originalConductedAt = getValueFromLabel(viewModal, 'Conducted At');

                // Get new values
                const newTitle = formData.get('Title');
                const newConductingBy = formData.get('ConductingBy');
                const newConductedAt = new Date(formData.get('ConductedAt')).toISOString().slice(0, 19).replace('T', ' ');

                // Check if any changes were made
                if (originalTitle === newTitle && 
                    originalConductingBy === newConductingBy && 
                    originalConductedAt === newConductedAt) {
                    await Swal.fire({
                        icon: 'warning',
                        title: 'No Changes',
                        text: 'No changes were made to the audit.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }

                showLoading('Updating audit...');
                const response = await fetch('../php/conduct-edit.php', {
                    method: 'POST',
                    body: formData
                });
                
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Unexpected response from server');
                }

                const data = await response.json();
                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Failed to update audit');
                }
                
                await showSuccessWithRefresh(data.message);
            } catch (error) {
                console.error('Edit audit error:', error);
                await showError(error.message);
            } finally {
                closeLoading();
            }
        }

        // Handle Delete
        async function handleDelete(auditId) {
            await showDeleteConfirmation(
                async () => {
                    try {
                        const response = await fetch(`../php/conduct-delete.php?id=${auditId}`);
                        if (!response.ok) {
                            throw new Error('Failed to delete audit');
                        }
                        showCreateSuccess('Audit deleted successfully');
                        setTimeout(() => location.reload(), 2000);
                    } catch (error) {
                        showError(error.message);
                    }
                },
                'audit'
            );
        }

        // Handle Mark Complete
        function handleComplete(auditID) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This will mark the audit as complete. Make sure all findings are compliant!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, mark as complete'
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoading('Marking audit as complete...');
                    
                    const formData = new FormData();
                    formData.append('AuditID', auditID);
                    
                    fetch('../php/mark-complete.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showSuccessWithRefresh(data.message);
                        } else {
                            throw new Error(data.message);
                        }
                    })
                    .catch(error => {
                        showError(error.message || 'Failed to mark audit as complete');
                    });
                }
            });
        }
    </script>
</body>
</html>