<?php
// Ensure we have access to the database connection
if (!isset($conn)) {
    require_once '../php/conn.php';
}

// Get recent findings if not already fetched
if (!isset($recentFindings)) {
    $recentFindings = $conn->query("
        SELECT f.FindingID, f.AuditID, f.Description, f.Category, f.LoggedAt,
               a.PlanID, ap.Title as AuditTitle
        FROM findings f
        LEFT JOIN audit a ON f.AuditID = a.AuditID
        LEFT JOIN auditplan ap ON a.PlanID = ap.PlanID
        ORDER BY f.LoggedAt DESC
        LIMIT 5");
}
?>

<div class="overflow-x-auto">
    <table class="w-full">
        <thead class="bg-secondary text-white">
            <tr>
                <th class="px-4 py-2 whitespace-nowrap">
                    <div class="flex items-center justify-start gap-2">
                        <box-icon name='hash' color='white'></box-icon>
                        Finding ID
                    </div>
                </th>
                <th class="px-4 py-2 whitespace-nowrap">
                    <div class="flex items-center justify-start gap-2">
                        <box-icon name='file' color='white'></box-icon>
                        Audit Title
                    </div>
                </th>
                <th class="px-4 py-2 whitespace-nowrap">
                    <div class="flex items-center justify-start gap-2">
                        <box-icon name='detail' color='white'></box-icon>
                        Description
                    </div>
                </th>
                <th class="px-4 py-2 whitespace-nowrap">
                    <div class="flex items-center justify-start gap-2">
                        <box-icon name='bookmark' color='white'></box-icon>
                        Category
                    </div>
                </th>
                <th class="px-4 py-2 whitespace-nowrap">
                    <div class="flex items-center justify-start gap-2">
                        <box-icon name='time' color='white'></box-icon>
                        Logged At
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php if ($recentFindings && $recentFindings->num_rows > 0): ?>
                <?php while ($finding = $recentFindings->fetch_assoc()): ?>
                    <tr class="border-t hover:bg-primary transition-colors duration-200">
                        <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($finding['FindingID']) ?></td>
                        <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($finding['AuditTitle']) ?></td>
                        <td class="px-4 py-2">
                            <div class="max-w-xs overflow-hidden text-ellipsis">
                                <?= htmlspecialchars($finding['Description']) ?>
                            </div>
                        </td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded-full text-sm 
                                <?= $finding['Category'] === 'Non-Compliant' ? 'bg-red-100 text-red-800' : 
                                    ($finding['Category'] === 'Observation' ? 'bg-yellow-100 text-yellow-800' : 
                                    'bg-green-100 text-green-800') ?>">
                                <?= htmlspecialchars($finding['Category']) ?>
                            </span>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap">
                            <?= date('Y-m-d H:i', strtotime($finding['LoggedAt'])) ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="px-4 py-2 text-center">No recent findings found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div> 