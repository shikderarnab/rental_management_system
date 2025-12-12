<?php
/**
 * Add contract view
 */
$this->assign('title', 'Create Contract');
?>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <h2>Create Contract</h2>
        
        <div class="mb-3">
            <?= $this->Html->link('← Back to Contracts', ['action' => 'index'], ['class' => 'btn btn-sm btn-secondary']) ?>
        </div>
        
        <?= $this->Form->create($contract, ['type' => 'file']) ?>
        
        <div class="mb-3">
            <?= $this->Form->control('unit_id', [
                'type' => 'select',
                'options' => $unitsList,
                'class' => 'form-control',
                'required' => true,
                'default' => $unitId ?? null,
                'label' => 'Unit'
            ]) ?>
        </div>
        
        <div class="mb-3">
            <?= $this->Form->control('tenant_id', [
                'type' => 'select',
                'options' => $tenantsList ?? [],
                'class' => 'form-control',
                'required' => empty($tenantsList),
                'label' => 'Tenant',
                'empty' => empty($tenantsList) ? 'No tenants available' : 'Select Tenant',
                'disabled' => empty($tenantsList)
            ]) ?>
            <?php if (empty($tenantsList)): ?>
                <small class="form-text text-danger">
                    <strong>No tenants found.</strong> Please register tenant accounts first. 
                    <?= $this->Html->link('Register Tenant', ['controller' => 'Users', 'action' => 'register'], ['class' => 'btn btn-sm btn-outline-primary mt-2']) ?>
                </small>
            <?php endif; ?>
        </div>
        
        <?php if ($this->Identity->get('role') === 'admin'): ?>
            <div class="mb-3">
                <?= $this->Form->control('landlord_id', [
                    'type' => 'select',
                    'options' => $landlordsList,
                    'class' => 'form-control',
                    'required' => true,
                    'label' => 'Landlord'
                ]) ?>
            </div>
        <?php endif; ?>
        
        <div class="mb-3">
            <?= $this->Form->control('start_date', [
                'type' => 'date',
                'class' => 'form-control',
                'required' => true,
                'label' => 'Start Date'
            ]) ?>
        </div>
        
        <div class="mb-3">
            <?= $this->Form->control('end_date', [
                'type' => 'date',
                'class' => 'form-control',
                'label' => 'End Date (Optional)'
            ]) ?>
        </div>
        
        <div class="row">
            <div class="col-md-8">
                <div class="mb-3">
                    <?= $this->Form->control('rent_amount', [
                        'type' => 'number',
                        'class' => 'form-control',
                        'required' => true,
                        'min' => 0,
                        'step' => 0.01,
                        'label' => 'Rent Amount'
                    ]) ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <?= $this->Form->control('currency', [
                        'type' => 'select',
                        'options' => ['USD' => 'USD', 'EUR' => 'EUR', 'GBP' => 'GBP'],
                        'class' => 'form-control',
                        'default' => 'USD',
                        'label' => 'Currency'
                    ]) ?>
                </div>
            </div>
        </div>
        
        <div class="mb-3">
            <?= $this->Form->control('status', [
                'type' => 'select',
                'options' => [
                    'draft' => 'Draft',
                    'pending_signature' => 'Pending Signature',
                    'active' => 'Active',
                    'expired' => 'Expired',
                    'terminated' => 'Terminated'
                ],
                'class' => 'form-control',
                'default' => 'draft',
                'label' => 'Status'
            ]) ?>
        </div>
        
        <div class="mb-3">
            <?= $this->Form->control('agreement_file', [
                'type' => 'file',
                'class' => 'form-control',
                'label' => 'Agreement File (PDF)',
                'accept' => '.pdf'
            ]) ?>
        </div>
        
        <?= $this->Form->button('Create Contract', ['class' => 'btn btn-primary']) ?>
        <?= $this->Form->end() ?>
    </div>
</div>

