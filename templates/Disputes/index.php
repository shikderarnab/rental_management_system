<?php
/**
 * Disputes index view
 */
$this->assign('title', 'Disputes');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Disputes</h2>
    <?php if ($this->Identity->get('role') === 'tenant'): ?>
        <?= $this->Html->link('Submit Dispute', ['action' => 'add'], ['class' => 'btn btn-primary']) ?>
    <?php endif; ?>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Subject</th>
            <th>Status</th>
            <th>Related To</th>
            <th>Created</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($disputes as $dispute): ?>
            <tr>
                <td><?= h($dispute->id) ?></td>
                <td><?= h($dispute->subject) ?></td>
                <td>
                    <span class="badge bg-<?= $dispute->status === 'resolved' ? 'success' : ($dispute->status === 'closed' ? 'secondary' : ($dispute->status === 'reviewing' ? 'info' : 'warning')) ?>">
                        <?= h(ucfirst($dispute->status ?? 'Open')) ?>
                    </span>
                </td>
                <td>
                    <?php if ($dispute->contract): ?>
                        Contract #<?= h($dispute->contract->id) ?>
                    <?php elseif ($dispute->payment): ?>
                        Payment #<?= h($dispute->payment->id) ?>
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>
                <td><?= $dispute->created ? h($dispute->created->format('Y-m-d H:i')) : ($dispute->modified ? h($dispute->modified->format('Y-m-d H:i')) : 'N/A') ?></td>
                <td>
                    <?= $this->Html->link('View', ['action' => 'view', $dispute->id], ['class' => 'btn btn-sm btn-primary']) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if (empty($disputes)): ?>
    <div class="alert alert-info">
        No disputes found.
    </div>
<?php endif; ?>

