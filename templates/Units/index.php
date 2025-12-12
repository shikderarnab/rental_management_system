<?php
/**
 * Units index view
 */
$this->assign('title', 'Units');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Units</h2>
    <?php if ($this->Identity->get('role') === 'landlord' || $this->Identity->get('role') === 'admin'): ?>
        <?php if ($propertyId): ?>
            <?= $this->Html->link('Add Unit', ['action' => 'add', $propertyId], ['class' => 'btn btn-primary']) ?>
        <?php else: ?>
            <?= $this->Html->link('Add Unit', ['action' => 'add'], ['class' => 'btn btn-primary']) ?>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php if ($propertyId): ?>
    <div class="mb-3">
        <?= $this->Html->link('← Back to Property', ['controller' => 'Properties', 'action' => 'view', $propertyId], ['class' => 'btn btn-sm btn-secondary']) ?>
    </div>
<?php endif; ?>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Unit Number</th>
            <th>Property</th>
            <th>Type</th>
            <th>Bedrooms</th>
            <th>Bathrooms</th>
            <th>Rent Amount</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($units as $unit): ?>
            <tr>
                <td><?= h($unit->unit_number) ?></td>
                <td><?= h($unit->property->name ?? 'N/A') ?></td>
                <td><?= h($unit->type ?? 'N/A') ?></td>
                <td><?= h($unit->bedrooms ?? 0) ?></td>
                <td><?= h($unit->bathrooms ?? 0) ?></td>
                <td><?= h(($unit->currency ?? 'USD') . ' ' . number_format($unit->rent_amount ?? 0, 2)) ?></td>
                <td>
                    <span class="badge bg-<?= ($unit->is_available ?? true) ? 'success' : 'secondary' ?>">
                        <?= ($unit->is_available ?? true) ? 'Available' : 'Occupied' ?>
                    </span>
                </td>
                <td>
                    <?= $this->Html->link('View', ['action' => 'view', $unit->id], ['class' => 'btn btn-sm btn-primary']) ?>
                    <?php if ($this->Identity->get('role') === 'landlord' || $this->Identity->get('role') === 'admin'): ?>
                        <?= $this->Html->link('Edit', ['action' => 'edit', $unit->id], ['class' => 'btn btn-sm btn-secondary']) ?>
                        <?= $this->Html->link('Assign Tenant', ['action' => 'assignTenant', $unit->id], ['class' => 'btn btn-sm btn-success']) ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if (empty($units)): ?>
    <div class="alert alert-info">
        No units found.
    </div>
<?php endif; ?>

