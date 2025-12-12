<?php
/**
 * Payments index view
 */
$this->assign('title', 'Payments');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Payments</h2>
    <?php if ($this->Identity->get('role') === 'tenant'): ?>
        <?= $this->Html->link('Make Payment', ['action' => 'add'], ['class' => 'btn btn-primary']) ?>
    <?php endif; ?>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Amount</th>
            <th>Method</th>
            <th>Status</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($payments as $payment): ?>
            <tr>
                <td><?= h($payment->id) ?></td>
                <td><?= h($payment->currency . ' ' . number_format($payment->amount, 2)) ?></td>
                <td><?= h(ucfirst($payment->payment_method)) ?></td>
                <td>
                    <span class="badge bg-<?= $payment->payment_status === 'verified' ? 'success' : ($payment->payment_status === 'rejected' ? 'danger' : 'warning') ?>">
                        <?= h(ucfirst($payment->payment_status)) ?>
                    </span>
                </td>
                <td><?= $payment->paid_at ? h($payment->paid_at->format('Y-m-d H:i')) : ($payment->created ? h($payment->created->format('Y-m-d H:i')) : 'N/A') ?></td>
                <td>
                    <?= $this->Html->link('View', ['action' => 'view', $payment->id], ['class' => 'btn btn-sm btn-primary']) ?>
                    <?= $this->Html->link('Download Slip', ['action' => 'downloadReceipt', $payment->id], ['class' => 'btn btn-sm btn-success']) ?>
                    <?php if ($payment->payment_status === 'verified'): ?>
                        <?= $this->Html->link('Download Invoice', ['action' => 'downloadInvoice', $payment->id], ['class' => 'btn btn-sm btn-info']) ?>
                    <?php endif; ?>
                    <?php if (($this->Identity->get('role') === 'landlord' || $this->Identity->get('role') === 'admin') && $payment->payment_status === 'pending'): ?>
                        <?= $this->Form->postLink('Verify', ['action' => 'verify', $payment->id], [
                            'class' => 'btn btn-sm btn-success',
                            'data' => ['action' => 'verify']
                        ]) ?>
                        <?= $this->Form->postLink('Reject', ['action' => 'verify', $payment->id], [
                            'class' => 'btn btn-sm btn-danger',
                            'data' => ['action' => 'reject']
                        ]) ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if (empty($payments)): ?>
    <div class="alert alert-info">
        No payments found.
    </div>
<?php endif; ?>

