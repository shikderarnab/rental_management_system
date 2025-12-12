<?php
/**
 * Add unit view
 */
$this->assign('title', 'Add Unit');
?>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <h2>Add Unit</h2>
        
        <?php 
        $propertyId = $propertyId ?? $this->request->getParam('pass.0') ?? null;
        if ($propertyId): ?>
            <div class="mb-3">
                <?= $this->Html->link('← Back to Units', ['action' => 'index', $propertyId], ['class' => 'btn btn-sm btn-secondary']) ?>
            </div>
        <?php endif; ?>
        
        <?= $this->Form->create($unit) ?>
        
        <div class="mb-3">
            <?= $this->Form->control('property_id', [
                'type' => 'select',
                'options' => $properties,
                'class' => 'form-control',
                'required' => true,
                'default' => $propertyId ?? null,
                'label' => 'Property'
            ]) ?>
        </div>
        
        <div class="mb-3">
            <?= $this->Form->control('unit_number', [
                'class' => 'form-control',
                'required' => true,
                'label' => 'Unit Number'
            ]) ?>
        </div>
        
        <div class="mb-3">
            <?= $this->Form->control('type', [
                'type' => 'select',
                'options' => [
                    'apartment' => 'Apartment',
                    'house' => 'House',
                    'studio' => 'Studio',
                    'condo' => 'Condo',
                    'townhouse' => 'Townhouse'
                ],
                'class' => 'form-control',
                'label' => 'Type',
                'empty' => 'Select Type'
            ]) ?>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <?= $this->Form->control('bedrooms', [
                        'type' => 'number',
                        'class' => 'form-control',
                        'min' => 0,
                        'default' => 0,
                        'label' => 'Bedrooms'
                    ]) ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <?= $this->Form->control('bathrooms', [
                        'type' => 'number',
                        'class' => 'form-control',
                        'min' => 0,
                        'step' => 0.5,
                        'default' => 0,
                        'label' => 'Bathrooms'
                    ]) ?>
                </div>
            </div>
        </div>
        
        <div class="mb-3">
            <?= $this->Form->control('area', [
                'type' => 'number',
                'class' => 'form-control',
                'min' => 0,
                'step' => 0.01,
                'label' => 'Area (sq ft)'
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
            <?= $this->Form->control('is_available', [
                'type' => 'checkbox',
                'class' => 'form-check-input',
                'default' => true,
                'label' => 'Available'
            ]) ?>
        </div>
        
        <?= $this->Form->button('Add Unit', ['class' => 'btn btn-primary']) ?>
        <?= $this->Form->end() ?>
    </div>
</div>

