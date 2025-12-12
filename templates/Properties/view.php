<?php
/**
 * Property view
 */
$this->assign('title', 'Property Details');
?>

<div class="mb-4">
    <?= $this->Html->link('← Back to Properties', ['action' => 'index'], ['class' => 'btn btn-sm btn-secondary']) ?>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3><?= h($property->name) ?></h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Name:</dt>
                    <dd class="col-sm-8"><?= h($property->name) ?></dd>
                    
                    <dt class="col-sm-4">Address:</dt>
                    <dd class="col-sm-8"><?= h($property->address) ?></dd>
                    
                    <dt class="col-sm-4">City:</dt>
                    <dd class="col-sm-8"><?= h($property->city) ?></dd>
                    
                    <dt class="col-sm-4">State:</dt>
                    <dd class="col-sm-8"><?= h($property->state ?? 'N/A') ?></dd>
                    
                    <dt class="col-sm-4">Postal Code:</dt>
                    <dd class="col-sm-8"><?= h($property->postal_code ?? 'N/A') ?></dd>
                    
                    <dt class="col-sm-4">Country:</dt>
                    <dd class="col-sm-8"><?= h($property->country ?? 'N/A') ?></dd>
                    
                    <?php if (isset($property->landlord)): ?>
                        <dt class="col-sm-4">Landlord:</dt>
                        <dd class="col-sm-8">
                            <?php 
                            $landlordName = '';
                            if (!empty($property->landlord->company_name)) {
                                $landlordName = $property->landlord->company_name;
                            } elseif (isset($property->landlord->user)) {
                                $landlordName = trim(($property->landlord->user->first_name ?? '') . ' ' . ($property->landlord->user->last_name ?? ''));
                            }
                            echo h($landlordName ?: 'Landlord #' . ($property->landlord_id ?? 'N/A'));
                            ?>
                        </dd>
                    <?php endif; ?>
                    
                    <dt class="col-sm-4">Total Units:</dt>
                    <dd class="col-sm-8"><?= count($property->units ?? []) ?></dd>
                    
                    <dt class="col-sm-4">Created:</dt>
                    <dd class="col-sm-8"><?= $property->created ? h($property->created->format('Y-m-d H:i')) : 'N/A' ?></dd>
                </dl>
                
                <div class="mt-4">
                    <?php if ($this->Identity->get('role') === 'landlord' || $this->Identity->get('role') === 'admin'): ?>
                        <?= $this->Html->link('Edit Property', ['action' => 'edit', $property->id], ['class' => 'btn btn-primary']) ?>
                        <?= $this->Html->link('Add Unit', ['controller' => 'Units', 'action' => 'add', $property->id], ['class' => 'btn btn-success']) ?>
                        <?= $this->Html->link('View Units', ['controller' => 'Units', 'action' => 'index', $property->id], ['class' => 'btn btn-info']) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <?php if (!empty($property->units)): ?>
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Units (<?= count($property->units) ?>)</h5>
                    <?php if ($this->Identity->get('role') === 'landlord' || $this->Identity->get('role') === 'admin'): ?>
                        <?= $this->Html->link('Add Unit', ['controller' => 'Units', 'action' => 'add', $property->id], ['class' => 'btn btn-sm btn-success']) ?>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php foreach ($property->units as $unit): ?>
                        <div class="mb-3 pb-3 border-bottom">
                            <strong>Unit <?= h($unit->unit_number) ?></strong><br>
                            <small class="text-muted">
                                Type: <?= h($unit->type ?? 'N/A') ?><br>
                                Rent: <?= h(($unit->currency ?? 'USD') . ' ' . number_format($unit->rent_amount ?? 0, 2)) ?><br>
                                Status: <span class="badge bg-<?= ($unit->is_available ?? true) ? 'success' : 'secondary' ?>">
                                    <?= ($unit->is_available ?? true) ? 'Available' : 'Occupied' ?>
                                </span>
                            </small><br>
                            <?= $this->Html->link('View Unit', ['controller' => 'Units', 'action' => 'view', $unit->id], ['class' => 'btn btn-sm btn-outline-primary mt-2']) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-body text-center">
                    <p class="text-muted">No units added yet.</p>
                    <?php if ($this->Identity->get('role') === 'landlord' || $this->Identity->get('role') === 'admin'): ?>
                        <?= $this->Html->link('Add First Unit', ['controller' => 'Units', 'action' => 'add', $property->id], ['class' => 'btn btn-success']) ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

