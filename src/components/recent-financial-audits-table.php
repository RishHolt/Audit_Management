<?php
include '../php/conn.php';

// Fetch recent GL Audits
$query = "SELECT 
    fa.AuditID,
    fa.Status,
    fa.ReviewedBy,
    fa.AuditDate,
    je.EntryID,
    je.EntryType,
    je.Amount,
    je.Description
FROM financial_audit_gl fa
JOIN journalentries je ON fa.EntryID = je.EntryID
ORDER BY fa.AuditDate DESC
LIMIT 5";

$result = $conn->query($query);
?>

<div class="overflow-x-auto">
    <table class="w-full">
        <thead>
            <tr class="bg-secondary text-white text-sm">
                <th class="px-4 py-2 whitespace-nowrap">Entry ID</th>
                <th class="px-4 py-2 whitespace-nowrap">Amount</th>
                <th class="px-4 py-2 whitespace-nowrap">Type</th>
                <th class="px-4 py-2 whitespace-nowrap">Status</th>
                <th class="px-4 py-2 whitespace-nowrap">Reviewed By</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($audit = $result->fetch_assoc()): ?>
                    <tr class="border-t hover:bg-primary transition-colors duration-200">
                        <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($audit['EntryID']) ?></td>
                        <td class="px-4 py-2 whitespace-nowrap"><?= number_format($audit['Amount'], 2) ?></td>
                        <td class="px-4 py-2 whitespace-nowrap">
                            <span class="px-2 py-1 rounded-full text-sm <?= $audit['EntryType'] === 'Debit' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' ?>">
                                <?= htmlspecialchars($audit['EntryType']) ?>
                            </span>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap">
                            <span class="px-2 py-1 rounded-full text-sm <?= 
                                $audit['Status'] === 'Reviewed' ? 'bg-green-100 text-green-800' : 
                                ($audit['Status'] === 'Flagged' ? 'bg-red-100 text-red-800' : 
                                'bg-yellow-100 text-yellow-800') ?>">
                                <?= htmlspecialchars($audit['Status']) ?>
                            </span>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap"><?= htmlspecialchars($audit['ReviewedBy']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="px-4 py-2 text-center">No recent financial audits</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
