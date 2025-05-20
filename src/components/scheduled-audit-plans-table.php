<?php
// Ensure we have access to the database connection
if (!isset($conn)) {
    require_once '../php/conn.php';
}

// Debug connection
if ($conn->connect_error) {
    echo "<!-- Connection failed: " . $conn->connect_error . " -->";
}

// Get scheduled audit plans if not already fetched
if (!isset($scheduledPlans)) {
    $scheduledPlans = $conn->query("
        SELECT 
            ap.PlanID,
            ap.Title,
            ap.Department,
            ap.ScheduledDate,
            DATEDIFF(ap.ScheduledDate, CURRENT_DATE) as days_until
        FROM auditplan ap
        WHERE ap.Status = 'Scheduled'
        ORDER BY ap.ScheduledDate ASC 
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
                        <box-icon name='time' color='white'></box-icon>
                        Days Until
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php if ($scheduledPlans && $scheduledPlans->num_rows > 0): ?>
                <?php while ($plan = $scheduledPlans->fetch_assoc()): ?>
                    <tr class="border-t hover:bg-primary transition-colors duration-200">
                        <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($plan['PlanID']) ?></td>
                        <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($plan['Title']) ?></td>
                        <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($plan['Department']) ?></td>
                        <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($plan['ScheduledDate']) ?></td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded-full text-sm 
                                <?= $plan['days_until'] <= 7 ? 'bg-red-100 text-red-800' : 
                                    ($plan['days_until'] <= 14 ? 'bg-yellow-100 text-yellow-800' : 
                                    'bg-green-100 text-green-800') ?>">
                                <?= $plan['days_until'] ?> days
                            </span>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="px-4 py-2 text-center">No scheduled audit plans found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div> 