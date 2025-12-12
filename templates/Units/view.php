<?php
/**
 * Unit view
 */
$this->assign('title', 'Unit Details');
?>

<div class="mb-4">
    <?= $this->Html->link('← Back to Units', ['action' => 'index', $unit->property_id], ['class' => 'btn btn-sm btn-secondary']) ?>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3>Unit <?= h($unit->unit_number) ?></h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Property:</dt>
                    <dd class="col-sm-8"><?= h($unit->property->name ?? 'N/A') ?></dd>
                    
                    <dt class="col-sm-4">Unit Number:</dt>
                    <dd class="col-sm-8"><?= h($unit->unit_number) ?></dd>
                    
                    <dt class="col-sm-4">Type:</dt>
                    <dd class="col-sm-8"><?= h($unit->type ?? 'N/A') ?></dd>
                    
                    <dt class="col-sm-4">Bedrooms:</dt>
                    <dd class="col-sm-8"><?= h($unit->bedrooms ?? 0) ?></dd>
                    
                    <dt class="col-sm-4">Bathrooms:</dt>
                    <dd class="col-sm-8"><?= h($unit->bathrooms ?? 0) ?></dd>
                    
                    <dt class="col-sm-4">Area:</dt>
                    <dd class="col-sm-8"><?= $unit->area ? h($unit->area . ' sq ft') : 'N/A' ?></dd>
                    
                    <dt class="col-sm-4">Rent Amount:</dt>
                    <dd class="col-sm-8"><?= h(($unit->currency ?? 'USD') . ' ' . number_format($unit->rent_amount ?? 0, 2)) ?></dd>
                    
                    <dt class="col-sm-4">Status:</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-<?= ($unit->is_available ?? true) ? 'success' : 'secondary' ?>">
                            <?= ($unit->is_available ?? true) ? 'Available' : 'Occupied' ?>
                        </span>
                    </dd>
                    
                    <dt class="col-sm-4">Created:</dt>
                    <dd class="col-sm-8"><?= $unit->created ? h($unit->created->format('Y-m-d H:i')) : 'N/A' ?></dd>
                </dl>
                
                <div class="mt-4">
                    <?php if ($this->Identity->get('role') === 'landlord' || $this->Identity->get('role') === 'admin'): ?>
                        <?= $this->Html->link('Edit', ['action' => 'edit', $unit->id], ['class' => 'btn btn-primary']) ?>
                        <?= $this->Html->link('Assign Tenant', ['action' => 'assignTenant', $unit->id], ['class' => 'btn btn-success']) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <?php if (!empty($unit->contracts)): ?>
            <div class="card">
                <div class="card-header">
                    <h5>Contracts</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($unit->contracts as $contract): ?>
                        <div class="mb-3">
                            <strong>Contract #<?= h($contract->id) ?></strong><br>
                            <small class="text-muted">
                                Status: <span class="badge bg-<?= $contract->status === 'active' ? 'success' : 'secondary' ?>">
                                    <?= h(ucfirst($contract->status)) ?>
                                </span>
                            </small><br>
                            <?= $this->Html->link('View Contract', ['controller' => 'Contracts', 'action' => 'view', $contract->id], ['class' => 'btn btn-sm btn-outline-primary mt-2']) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

