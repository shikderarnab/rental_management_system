<?php
/**
 * Add dispute view
 */
$this->assign('title', 'Submit Dispute');
?>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <h2>Submit Dispute</h2>
        
        <?= $this->Form->create($dispute) ?>
        
        <div class="mb-3">
            <?php
            $contractsOptions = ['' => 'Select Contract (Optional)'];
            if (!empty($contracts)) {
                foreach ($contracts as $contract) {
                    $propertyName = 'N/A';
                    if (isset($contract->unit) && isset($contract->unit->property)) {
                        $propertyName = $contract->unit->property->name ?? 'N/A';
                    }
                    $contractsOptions[$contract->id] = 'Contract #' . $contract->id . ' - ' . $propertyName;
                }
            }
            ?>
            <?= $this->Form->control('contract_id', [
                'type' => 'select',
                'options' => $contractsOptions,
                'class' => 'form-control',
                'label' => 'Related Contract (Optional)',
                'empty' => true
            ]) ?>
        </div>
        
        <div class="mb-3">
            <?= $this->Form->control('subject', [
                'class' => 'form-control',
                'required' => true,
                'label' => 'Subject',
                'placeholder' => 'Brief description of the dispute'
            ]) ?>
        </div>
        
        <div class="mb-3">
            <?= $this->Form->control('description', [
                'type' => 'textarea',
                'class' => 'form-control',
                'required' => true,
                'rows' => 5,
                'label' => 'Description',
                'placeholder' => 'Please provide detailed information about the dispute...'
            ]) ?>
        </div>
        
        <div class="mb-3">
            <?= $this->Form->button('Submit Dispute', ['class' => 'btn btn-primary']) ?>
            <?= $this->Html->link('Cancel', ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
        </div>
        
        <?= $this->Form->end() ?>
    </div>
</div>

