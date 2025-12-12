<?php
/**
 * Properties index view
 */
$this->assign('title', 'Properties');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Properties</h2>
    <?php if ($this->Identity->get('role') === 'landlord' || $this->Identity->get('role') === 'admin'): ?>
        <?= $this->Html->link('Add Property', ['action' => 'add'], ['class' => 'btn btn-primary']) ?>
    <?php endif; ?>
</div>

<div class="row">
    <?php foreach ($properties as $property): ?>
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?= h($property->name) ?></h5>
                    <p class="card-text">
                        <strong>Address:</strong> <?= h($property->address) ?><br>
                        <strong>City:</strong> <?= h($property->city) ?><br>
                        <strong>Units:</strong> <?= count($property->units ?? []) ?>
                        <?php if (empty($property->units)): ?>
                            <span class="badge bg-warning text-dark ms-2">No units yet</span>
                        <?php endif; ?>
                    </p>
                    <div class="d-flex gap-2 flex-wrap">
                        <?= $this->Html->link('View', ['action' => 'view', $property->id], ['class' => 'btn btn-sm btn-primary']) ?>
                        <?php if ($this->Identity->get('role') === 'landlord' || $this->Identity->get('role') === 'admin'): ?>
                            <?= $this->Html->link('Edit', ['action' => 'edit', $property->id], ['class' => 'btn btn-sm btn-secondary']) ?>
                            <?php if (empty($property->units)): ?>
                                <?= $this->Html->link('Add Unit', ['controller' => 'Units', 'action' => 'add', $property->id], ['class' => 'btn btn-sm btn-success']) ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php if (empty($properties)): ?>
    <div class="alert alert-info">
        No properties found.
    </div>
<?php endif; ?>

