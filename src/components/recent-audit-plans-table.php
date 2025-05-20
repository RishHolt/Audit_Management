<?php
// Ensure we have access to the database connection
if (!isset($conn)) {
    require_once '../php/conn.php';
}

// Get recent audit plans if not already fetched
if (!isset($recentPlans)) {
    $recentPlans = $conn->query("
        SELECT ap.*, 
               COUNT(DISTINCT a.AuditID) as audit_count,
               COUNT(DISTINCT f.FindingID) as finding_count
        FROM auditplan ap
        LEFT JOIN audit a ON ap.PlanID = a.PlanID
        LEFT JOIN findings f ON a.AuditID = f.AuditID
        GROUP BY ap.PlanID
        ORDER BY ap.PlanID DESC 
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
                        Scheduled Date
                    </div>
                </th>
                <th class="px-4 py-2 whitespace-nowrap">
                    <div class="flex items-center justify-start gap-2">
                        <box-icon name='info-circle' color='white'></box-icon>
                        Status
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php if ($recentPlans && $recentPlans->num_rows > 0): ?>
                <?php while ($plan = $recentPlans->fetch_assoc()): ?>
                    <tr class="border-t hover:bg-primary transition-colors duration-200">
                        <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($plan['PlanID']) ?></td>
                        <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($plan['Title']) ?></td>
                        <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($plan['Department']) ?></td>
                        <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($plan['ScheduledDate']) ?></td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded-full text-sm 
                                <?= $plan['Status'] === 'Completed' ? 'bg-green-100 text-green-800' : 
                                    ($plan['Status'] === 'Open' ? 'bg-blue-100 text-blue-800' : 
                                    'bg-yellow-100 text-yellow-800') ?>">
                                <?= htmlspecialchars($plan['Status']) ?>
                            </span>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="px-4 py-2 text-center">No recent audit plans found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div> 