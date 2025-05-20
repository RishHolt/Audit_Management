<?php
// Ensure we have access to the database connection
if (!isset($conn)) {
    require_once '../php/conn.php';
}

// Get open audit plans if not already fetched
if (!isset($openPlans)) {
    $openPlans = $conn->query("
        SELECT 
            ap.PlanID,
            ap.Title,
            ap.Department,
            CASE 
                WHEN COUNT(DISTINCT a.AuditID) = 0 THEN 'Not Started'
                WHEN EXISTS (
                    SELECT 1 FROM audit a2 
                    WHERE a2.PlanID = ap.PlanID 
                    AND a2.Status = 'Completed'
                ) THEN 'Completed'
                ELSE 'In Progress'
            END as progress_status
        FROM auditplan ap
        LEFT JOIN audit a ON ap.PlanID = a.PlanID
        WHERE ap.Status = 'Open'
        GROUP BY ap.PlanID, ap.Title, ap.Department
        ORDER BY ap.ScheduledDate ASC 
        LIMIT 5");
}

// Function to safely get array value
function safe_get($array, $key, $default = '') {
    return isset($array[$key]) ? $array[$key] : $default;
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
                        <box-icon name='time' color='white'></box-icon>
                        Progress
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php if ($openPlans && $openPlans->num_rows > 0): ?>
                <?php while ($plan = $openPlans->fetch_assoc()): ?>
                    <tr class="border-t hover:bg-primary transition-colors duration-200">
                        <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars(safe_get($plan, 'PlanID')) ?></td>
                        <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars(safe_get($plan, 'Title')) ?></td>
                        <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars(safe_get($plan, 'Department')) ?></td>
                        <td class="px-4 py-2">
                            <?php 
                            $progress = safe_get($plan, 'progress_status', 'Not Started');
                            $progressClass = $progress === 'Completed' ? 'bg-green-100 text-green-800' : 
                                           ($progress === 'In Progress' ? 'bg-yellow-100 text-yellow-800' : 
                                           'bg-blue-100 text-blue-800');
                            ?>
                            <span class="px-2 py-1 rounded-full text-sm <?= $progressClass ?>">
                                <?= htmlspecialchars($progress) ?>
                            </span>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="px-4 py-2 text-center">No open audit plans found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div> 