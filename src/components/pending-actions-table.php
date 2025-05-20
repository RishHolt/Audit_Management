<?php
// Ensure we have access to the database connection
if (!isset($conn)) {
    require_once '../php/conn.php';
}

// Get pending corrective actions if not already fetched
if (!isset($correctiveActions)) {
    $correctiveActions = $conn->query("
        SELECT ca.ActionID, ca.AssignedTo, ca.Task, ca.DueDate, ca.Status, 
               f.Category,
               DATEDIFF(ca.DueDate, CURRENT_DATE) as days_until_due
        FROM correctiveactions ca 
        JOIN findings f ON ca.FindingID = f.FindingID 
        WHERE ca.Status = 'Pending' 
        ORDER BY 
            CASE f.Category 
                WHEN 'Non-Compliant' THEN 1 
                WHEN 'Observation' THEN 2 
                ELSE 3 
            END,
            ca.DueDate ASC 
        LIMIT 5");
}
?>

<div class="overflow-x-auto">
    <table class="w-full">
        <thead class="bg-secondary text-white">
            <tr>
                <th class="px-4 py-2 whitespace-nowrap">
                    <div class="flex items-center justify-start gap-2">
                        <box-icon name='id-card' color='white'></box-icon>
                        Action ID
                    </div>
                </th>
                <th class="px-4 py-2 whitespace-nowrap">
                    <div class="flex items-center justify-start gap-2">
                        <box-icon name='user-circle' color='white'></box-icon>
                        Assigned To
                    </div>
                </th>
                <th class="px-4 py-2 whitespace-nowrap">
                    <div class="flex items-center justify-start gap-2">
                        <box-icon name='list-check' color='white'></box-icon>
                        Task
                    </div>
                </th>
                <th class="px-4 py-2 whitespace-nowrap">
                    <div class="flex items-center justify-start gap-2">
                        <box-icon name='calendar-event' color='white'></box-icon>
                        Due Date
                    </div>
                </th>
                <th class="px-4 py-2 whitespace-nowrap">
                    <div class="flex items-center justify-start gap-2">
                        <box-icon name='bookmark' color='white'></box-icon>
                        Finding Category
                    </div>
                </th>
                <th class="px-4 py-2 whitespace-nowrap">
                    <div class="flex items-center justify-start gap-2">
                        <box-icon name='badge-check' color='white'></box-icon>
                        Status
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php if ($correctiveActions && $correctiveActions->num_rows > 0): ?>
                <?php while ($action = $correctiveActions->fetch_assoc()): ?>
                    <tr class="border-t hover:bg-primary transition-colors duration-200">
                        <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($action['ActionID']) ?></td>
                        <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($action['AssignedTo']) ?></td>
                        <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($action['Task']) ?></td>
                        <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($action['DueDate']) ?></td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded-full text-sm 
                                <?= $action['Category'] === 'Non-Compliant' ? 'bg-red-100 text-red-800' : 
                                    ($action['Category'] === 'Observation' ? 'bg-yellow-100 text-yellow-800' : 
                                    'bg-green-100 text-green-800') ?>">
                                <?= htmlspecialchars($action['Category']) ?>
                            </span>
                        </td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded-full text-sm bg-yellow-100 text-yellow-800">
                                <?= htmlspecialchars($action['Status']) ?>
                            </span>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="px-4 py-2 text-center">No pending corrective actions found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div> 