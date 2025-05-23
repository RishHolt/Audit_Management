<?php
include '../php/conn.php';

// Fetch GL entries with audit status by joining across databases
$query = "SELECT 
    je.EntryID,
    je.AccountID,
    je.EntryType,
    je.Amount,
    je.EntryDate,
    je.Description,
    fa.AuditID,
    fa.Status as AuditStatus,
    fa.ReviewedBy,
    fa.AuditDate
FROM " . DB_NAME_FINANCIALS . ".journalentries je
LEFT JOIN " . DB_NAME . ".financial_audit_gl fa ON je.EntryID = fa.EntryID
ORDER BY je.EntryDate DESC";

try {
    $result = executeQuery($query, 'financials');
$entries = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $entries[] = $row;
    }
}
} catch (Exception $e) {
    // Handle exception (optional: log error, show message, etc.)
    $entries = [];
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
    <title>GL Financial Audit</title>
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
                <span id="header" class="text-2xl font-bold text-[#4E3B2A]">Financial Audit (GL)</span>
                
                <!-- Table -->
                <table class="w-full border-collapse table-auto">
                    <thead>
                        <tr class="bg-secondary text-white">
                            <th class="px-4 py-2 whitespace-nowrap">
                                <div class="flex items-center justify-start gap-2">
                                    <box-icon name='hash' color='white'></box-icon>
                                    Entry ID
                                </div>
                            </th>
                            <th class="px-4 py-2 whitespace-nowrap">
                                <div class="flex items-center justify-start gap-2">
                                    <box-icon name='dollar' color='white'></box-icon>
                                    Amount
                                </div>
                            </th>
                            <th class="px-4 py-2 whitespace-nowrap">
                                <div class="flex items-center justify-start gap-2">
                                    <box-icon name='transfer' color='white'></box-icon>
                                    Type
                                </div>
                            </th>
                            <th class="px-4 py-2 whitespace-nowrap">
                                <div class="flex items-center justify-start gap-2">
                                    <box-icon name='calendar' color='white'></box-icon>
                                    Date
                                </div>
                            </th>
                            <th class="px-4 py-2 whitespace-nowrap">
                                <div class="flex items-center justify-start gap-2">
                                    <box-icon name='info-circle' color='white'></box-icon>
                                    Description
                                </div>
                            </th>
                            <th class="px-4 py-2 whitespace-nowrap">
                                <div class="flex items-center justify-start gap-2">
                                    <box-icon name='badge' color='white'></box-icon>
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
                        <?php if (count($entries) > 0): ?>
                            <?php foreach ($entries as $entry): 
                                $viewModalId = "view-audit-modal-" . $entry['EntryID'];
                                $editModalId = "edit-audit-modal-" . $entry['EntryID'];
                            ?>
                            <tr class="border-b-1 border-accent bg-white hover:bg-primary transition-colors duration-200">
                                <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($entry['EntryID']) ?></td>
                                <td class="px-4 py-2 whitespace-nowrap"><?= number_format($entry['Amount'], 2) ?></td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <span class="px-2 py-1 rounded-full text-sm <?= $entry['EntryType'] === 'Debit' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' ?>">
                                        <?= htmlspecialchars($entry['EntryType']) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap"><?= date('Y-m-d H:i', strtotime($entry['EntryDate'])) ?></td>
                                <td class="px-4 py-2 truncate max-w-xs"><?= htmlspecialchars($entry['Description']) ?></td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <span class="px-2 py-1 rounded-full text-sm <?= 
                                        !isset($entry['AuditStatus']) ? 'bg-gray-100 text-gray-800' : 
                                        ($entry['AuditStatus'] === 'Reviewed' ? 'bg-green-100 text-green-800' : 
                                        ($entry['AuditStatus'] === 'Flagged' ? 'bg-red-100 text-red-800' : 
                                        'bg-yellow-100 text-yellow-800')) ?>">
                                        <?= !isset($entry['AuditStatus']) ? 'Not Audited' : htmlspecialchars($entry['AuditStatus']) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-2">
                                    <div class="flex gap-1">
                                        <?php if (isset($entry['AuditID'])): ?>
                                            <button data-modal-target="<?= $viewModalId ?>" data-modal-toggle="<?= $viewModalId ?>" class="w-full px-3 py-2 bg-blue-400 text-white rounded-md">View</button>
                                            <button data-modal-target="<?= $editModalId ?>" data-modal-toggle="<?= $editModalId ?>" class="w-full px-3 py-2 bg-green-400 text-white rounded-md">Edit</button>
                                        <?php else: ?>
                                            <button data-modal-target="<?= $editModalId ?>" data-modal-toggle="<?= $editModalId ?>" class="w-full px-3 py-2 bg-accent text-white rounded-md">Add Audit</button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- View Modal -->
                            <?php if (isset($entry['AuditID'])): ?>
                            <div id="<?= $viewModalId ?>" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="hidden fixed top-0 left-0 size-full z-50 items-center justify-center">
                                <div class="flex flex-col w-full max-w-md p-4 bg-white shadow-md rounded-md">
                                    <div class="flex justify-between items-center mb-4">
                                        <span class="text-xl font-bold text-[#4E3B2A]">Audit Details</span>
                                        <button data-modal-hide="<?= $viewModalId ?>" class="text-gray-400 bg-transparent hover:bg-primary transition-colors duration-200 rounded-lg text-sm w-8 h-8 flex justify-center items-center">
                                            <box-icon name='x'></box-icon>
                                        </button>
                                    </div>
                                    <div class="flex flex-col gap-2 mb-4 p-3 bg-gray-50 rounded-md">
                                        <div><strong>Status:</strong> 
                                            <span class="px-2 py-1 rounded-full text-sm <?= 
                                                $entry['AuditStatus'] === 'Reviewed' ? 'bg-green-100 text-green-800' : 
                                                ($entry['AuditStatus'] === 'Flagged' ? 'bg-red-100 text-red-800' : 
                                                'bg-yellow-100 text-yellow-800') ?>">
                                                <?= htmlspecialchars($entry['AuditStatus']) ?>
                                            </span>
                                        </div>
                                        <div><strong>Reviewed By:</strong> <?= htmlspecialchars($entry['ReviewedBy']) ?></div>
                                        <div><strong>Audit Date:</strong> <?= htmlspecialchars($entry['AuditDate']) ?></div>
                                        <div class="mt-2">
                                            <strong>Entry Details:</strong>
                                            <div class="mt-1 space-y-1">
                                                <div>Amount: <?= number_format($entry['Amount'], 2) ?></div>
                                                <div>Type: <?= htmlspecialchars($entry['EntryType']) ?></div>
                                                <div>Date: <?= date('Y-m-d H:i', strtotime($entry['EntryDate'])) ?></div>
                                                <div>Description: <?= htmlspecialchars($entry['Description']) ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex justify-end gap-2">
                                        <button data-modal-hide="<?= $viewModalId ?>" data-modal-target="<?= $editModalId ?>" data-modal-toggle="<?= $editModalId ?>" class="bg-green-600 text-white px-4 py-2 rounded-md">Edit</button>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Edit/Add Modal -->
                            <div id="<?= $editModalId ?>" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="hidden fixed top-0 left-0 size-full z-50 items-center justify-center">
                                <div class="flex flex-col w-full max-w-md p-4 bg-white shadow-md rounded-md">
                                    <div class="flex justify-between items-center mb-4">
                                        <span class="text-xl font-bold text-[#4E3B2A]"><?= isset($entry['AuditID']) ? 'Edit Audit' : 'Add Audit' ?></span>
                                        <button data-modal-hide="<?= $editModalId ?>" class="text-gray-400 bg-transparent hover:bg-primary transition-colors duration-200 rounded-lg text-sm w-8 h-8 flex justify-center items-center">
                                            <box-icon name='x'></box-icon>
                                        </button>
                                    </div>
                                    <form action="../php/submit_financial_audit_gl.php" method="POST" class="flex flex-col gap-3">
                                        <input type="hidden" name="EntryID" value="<?= $entry['EntryID'] ?>">
                                        <?php if (isset($entry['AuditID'])): ?>
                                            <input type="hidden" name="AuditID" value="<?= $entry['AuditID'] ?>">
                                        <?php endif; ?>
                                        <div class="flex flex-col">
                                            <label>Reviewed By:
                                                <input type="text" name="ReviewedBy" required value="<?= isset($entry['ReviewedBy']) ? htmlspecialchars($entry['ReviewedBy']) : '' ?>" class="w-full px-3 py-2 border rounded-lg">
                                            </label>
                                        </div>
                                        <div class="flex flex-col">
                                            <label>Audit Date:
                                                <input type="date" name="AuditDate" required value="<?= isset($entry['AuditDate']) ? $entry['AuditDate'] : date('Y-m-d') ?>" class="w-full px-3 py-2 border rounded-lg">
                                            </label>
                                        </div>
                                        <div class="flex flex-col">
                                            <label>Status:
                                                <select name="Status" required class="w-full px-3 py-2 border rounded-lg">
                                                    <option value="Not Audited" <?= (isset($entry['AuditStatus']) && $entry['AuditStatus'] === 'Not Audited') ? 'selected' : '' ?>>Not Audited</option>
                                                    <option value="Pending" <?= (isset($entry['AuditStatus']) && $entry['AuditStatus'] === 'Pending') ? 'selected' : '' ?>>Pending</option>
                                                    <option value="Reviewed" <?= (isset($entry['AuditStatus']) && $entry['AuditStatus'] === 'Reviewed') ? 'selected' : '' ?>>Reviewed</option>
                                                    <option value="Flagged" <?= (isset($entry['AuditStatus']) && $entry['AuditStatus'] === 'Flagged') ? 'selected' : '' ?>>Flagged</option>
                                                </select>
                                            </label>
                                        </div>
                                        <div class="flex flex-col">
                                            <label>Notes:
                                                <textarea name="Notes" class="w-full px-3 py-2 border rounded-lg min-h-[100px]"><?= isset($entry['Notes']) ? htmlspecialchars($entry['Notes']) : '' ?></textarea>
                                            </label>
                                        </div>
                                        <div class="flex justify-end gap-2 mt-2">
                                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md">Save</button>
                                            <button type="button" data-modal-hide="<?= $editModalId ?>" class="px-4 py-2 bg-gray-400 text-white rounded-md">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center">No journal entries found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom SweetAlert2 Utility Functions -->
    <script src="../js/sweetalert.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form[action="../php/submit_financial_audit_gl.php"]');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(form);
                    fetch(form.action, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: data.message,
                                confirmButtonColor: '#3085d6',
                                allowOutsideClick: false
                            }).then(() => {
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                } else {
                                    window.location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'An error occurred.',
                                confirmButtonColor: '#d33'
                            });
                        }
                    })
                    .catch(() => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while submitting the form.',
                            confirmButtonColor: '#d33'
                        });
                    });
                });
            });
        });
    </script>
</body>
</html>