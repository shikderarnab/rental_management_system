<?php
/**
 * Contracts index view
 */
$this->assign('title', 'Contracts');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Contracts</h2>
    <?php if ($this->Identity->get('role') === 'landlord' || $this->Identity->get('role') === 'admin'): ?>
        <?= $this->Html->link('Create Contract', ['action' => 'add'], ['class' => 'btn btn-primary']) ?>
    <?php endif; ?>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Unit</th>
            <th>Tenant</th>
            <?php if ($this->Identity->get('role') === 'admin'): ?>
                <th>Landlord</th>
            <?php endif; ?>
            <th>Rent Amount</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($contracts as $contract): ?>
            <tr>
                <td><?= h($contract->id) ?></td>
                <td><?= h(($contract->unit->property->name ?? 'N/A') . ' - Unit ' . ($contract->unit->unit_number ?? 'N/A')) ?></td>
                <td>
                    <?php 
                    $tenantName = '';
                    if (isset($contract->tenant->user)) {
                        $tenantName = trim(($contract->tenant->user->first_name ?? '') . ' ' . ($contract->tenant->user->last_name ?? ''));
                    }
                    echo h($tenantName ?: 'Tenant #' . ($contract->tenant_id ?? 'N/A'));
                    ?>
                </td>
                <?php if ($this->Identity->get('role') === 'admin'): ?>
                    <td>
                        <?php 
                        $landlordName = '';
                        if (isset($contract->landlord)) {
                            if (!empty($contract->landlord->company_name)) {
                                $landlordName = $contract->landlord->company_name;
                            } elseif (isset($contract->landlord->user)) {
                                $landlordName = trim(($contract->landlord->user->first_name ?? '') . ' ' . ($contract->landlord->user->last_name ?? ''));
                            }
                        }
                        echo h($landlordName ?: 'Landlord #' . ($contract->landlord_id ?? 'N/A'));
                        ?>
                    </td>
                <?php endif; ?>
                <td><?= h(($contract->currency ?? 'USD') . ' ' . number_format($contract->rent_amount ?? 0, 2)) ?></td>
                <td>
                    <span class="badge bg-<?= $contract->status === 'active' ? 'success' : 'secondary' ?>">
                        <?= h(ucfirst($contract->status ?? 'N/A')) ?>
                    </span>
                </td>
                <td>
                    <?= $this->Html->link('View', ['action' => 'view', $contract->id], ['class' => 'btn btn-sm btn-primary']) ?>
                    <?php if ($this->Identity->get('role') === 'landlord' || $this->Identity->get('role') === 'admin'): ?>
                        <?= $this->Html->link('Edit', ['action' => 'edit', $contract->id], ['class' => 'btn btn-sm btn-secondary']) ?>
                    <?php endif; ?>
                    <?php if ($this->Identity->get('role') === 'tenant' && $contract->status === 'pending_signature'): ?>
                        <?= $this->Html->link('Sign', ['action' => 'sign', $contract->id], ['class' => 'btn btn-sm btn-success']) ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if (empty($contracts)): ?>
    <div class="alert alert-info">
        No contracts found.
    </div>
<?php endif; ?>

