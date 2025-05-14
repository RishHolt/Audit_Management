<?php 

include 'php/conn.php';

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
                <span id="header" class="text-2xl font-bold">Conduct Audit</span>
                <!-- Conduct Audit button -->
                <button data-modal-target="conduct-modal" data-modal-toggle="conduct-modal" class="flex size-fit">
                    <span class="px-3 py-2 size-fit bg-accent rounded-md">Conduct Audit</span>
                </button>

                <!-- Conduct Audit Modal -->
                <div id="conduct-modal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="hidden absolute top-0 left-0 size-full z-50">
                    <div class="w-full h-fit p-4 flex flex-col gap-3 bg-white rounded-md shadow-md max-w-md mx-auto mt-10">
                        <div class="flex flex-row justify-between items-center mb-2">
                            <span id="header" class="text-2xl font-bold">Conduct Audit</span>
                            <button data-modal-target="conduct-modal" data-modal-toggle="conduct-modal" class="size-5 p-4 flex rounded-md font-bold items-center justify-center bg-red-200">X</button>
                        </div>
                        <form action="php/conduct-audit.php" method="post" class="flex flex-col gap-3">
                            <div class="flex flex-col">
                                <label for="PlanID">Select Audit Plan:</label>
                                <select name="PlanID" id="PlanID" required class="border p-2 rounded">
                                    <option value="">-- Select Plan --</option>
                                    <?php foreach ($plans as $plan): ?>
                                        <option value="<?= htmlspecialchars($plan['PlanID']) ?>">
                                            <?= htmlspecialchars($plan['Title']) ?> (ID: <?= $plan['PlanID'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="flex flex-col">
                                <label for="ConductingBy">Conducting By:</label>
                                <input type="text" name="ConductingBy" id="ConductingBy" required class="border p-2 rounded">
                            </div>
                            <input type="hidden" name="Status" value="Pending">
                            <div class="flex justify-end">
                                <button type="submit" class="px-3 py-2 bg-secondary text-white rounded-md">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>

                <table class="w-full border-collapse table-auto">
                    <tr class="bg-secondary text-white text-left">
                        <th class="p-1 w-25">Audit ID</th>
                        <th class="p-1 w-25">Plan ID</th>
                        <th class="p-1">Title</th>
                        <th class="p-1">Conducting By</th>
                        <th class="p-1">Conducted At</th>
                        <th class="p-1">Status</th>
                        <th class="p-1 w-25">Actions</th>
                    </tr>
                    <?php
                    // Fetch conducted audits
                    $auditResult = $conn->query(
                        "SELECT ac.AuditID, ac.PlanID, ap.Title, ac.ConductingBy, ac.ConductedAt, ac.Status
                         FROM audit ac
                         JOIN auditplan ap ON ac.PlanID = ap.PlanID
                         ORDER BY ac.AuditID DESC"
                    );
                    $auditModals = [];
                    if ($auditResult && $auditResult->num_rows > 0) {
                        while ($audit = $auditResult->fetch_assoc()) {
                            $viewAuditModalId = "view-audit-modal-" . $audit["AuditID"];
                            echo "<tr class='border-b-1 border-accent bg-white'>";
                            echo "<td class='p-1'>" . htmlspecialchars($audit["AuditID"]) . "</td>";
                            echo "<td class='p-1'>" . htmlspecialchars($audit["PlanID"]) . "</td>";
                            echo "<td class='p-1'>" . htmlspecialchars($audit["Title"]) . "</td>";
                            echo "<td class='p-1'>" . htmlspecialchars($audit["ConductingBy"]) . "</td>";
                            echo "<td class='p-1'>" . htmlspecialchars($audit["ConductedAt"]) . "</td>";
                            
                            // Fetch findings for this audit
                            $findingsHtml = '';
                            $findingsResult = $conn->query("SELECT Category, Description, LoggedAt FROM findings WHERE AuditID = " . intval($audit['AuditID']));

                            // Determine if all findings are Compliant
                            $allCompliant = true;
                            $hasFindings = false;
                            if ($findingsResult && $findingsResult->num_rows > 0) {
                                $findingsHtml .= "<div class='mt-4'><strong>Findings:</strong><ul class='list-disc pl-5'>";
                                while ($finding = $findingsResult->fetch_assoc()) {
                                    $hasFindings = true;
                                    if ($finding['Category'] !== 'Compliant') {
                                        $allCompliant = false;
                                    }
                                    $findingsHtml .= "<li><span class='font-semibold'>" . htmlspecialchars($finding['Category']) . ":</span> " . htmlspecialchars($finding['Description']) . " <span class='text-xs text-gray-500'>(" . htmlspecialchars($finding['LoggedAt']) . ")</span></li>";
                                }
                                $findingsHtml .= "</ul></div>";
                            } else {
                                $allCompliant = false; // No findings, don't set to Under Review
                                $findingsHtml .= "<div class='mt-4'><strong>Findings:</strong> <span class='text-gray-500'>None</span></div>";
                            }

                            // Show Completed if audit is completed, otherwise Under Review if all findings are Compliant
                            $displayStatus = ($audit['Status'] === 'Completed')
                                ? 'Completed'
                                : (($hasFindings && $allCompliant) ? 'Under Review' : $audit['Status']);

                            echo "<td class='p-1'>" . htmlspecialchars($displayStatus) . "</td>";
                            echo "<td class='p-1'>
                                <div class='flex gap-1'>
                                    <button data-modal-target='$viewAuditModalId' data-modal-toggle='$viewAuditModalId' class='w-full px-3 py-1 bg-blue-400 text-white rounded-md'>View</button>
                                    <a href='php/conduct-delete.php?id=" . $audit["AuditID"] . "' onclick='return confirm(\"Delete this audit?\")' class='w-full text-center px-3 py-1 bg-red-400 text-white rounded-md'>Delete</a>
                                </div>
                            </td>";
                            echo "</tr>";

                            // View Audit Modal
                            $auditModals[] = "
                            <div id='$viewAuditModalId' data-modal-backdrop='static' tabindex='-1' aria-hidden='true' class='hidden fixed top-0 left-0 size-full z-50 items-center justify-center'>
                                <div class='flex flex-col w-full max-w-md p-4 bg-white shadow-md rounded-md'>
                                    <span class='text-xl font-bold mb-2'>Audit Details</span>
                                    <div class='flex flex-col gap-2'>
                                        <div><strong>Audit ID:</strong> " . htmlspecialchars($audit['AuditID']) . "</div>
                                        <div><strong>Plan ID:</strong> " . htmlspecialchars($audit['PlanID']) . "</div>
                                        <div><strong>Title:</strong> " . htmlspecialchars($audit['Title']) . "</div>
                                        <div><strong>Conducting By:</strong> " . htmlspecialchars($audit['ConductingBy']) . "</div>
                                        <div><strong>Conducted At:</strong> " . htmlspecialchars($audit['ConductedAt']) . "</div>
                                        <div><strong>Status:</strong> " . htmlspecialchars($displayStatus) . "</div>
                                    </div>
                                    $findingsHtml
                                    <div class='flex justify-end gap-2 mt-4'>
                                        " . (
                                            $displayStatus === 'Under Review'
                                            ? "<form action='php/mark-complete.php' method='post' class='inline'>
                                                    <input type='hidden' name='AuditID' value='" . htmlspecialchars($audit['AuditID']) . "'>
                                                    <button type='submit' class='bg-green-600 text-white px-4 py-2 rounded-md' onclick='return confirm(\"Mark this audit as complete?\")'>Mark as Complete</button>
                                               </form>"
                                            : ""
                                        ) . "
                                        <button type='button' data-modal-hide='$viewAuditModalId' class='bg-gray-400 text-white px-4 py-2 rounded-md'>Close</button>
                                    </div>
                                </div>
                            </div>";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center'>No conducted audits found.</td></tr>";
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
</body>
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
</html>