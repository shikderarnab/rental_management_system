<?php
/**
 * Add payment view
 */
$this->assign('title', 'Make Payment');
?>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <h2>Make Payment</h2>
        
        <?= $this->Form->create($payment, ['type' => 'file']) ?>
        
        <div class="mb-3">
            <?php
            // Build contracts options array manually
            $contractsOptions = [];
            foreach ($contracts as $contract) {
                $propertyName = $contract->unit->property->name ?? 'N/A';
                $unitNumber = $contract->unit->unit_number ?? 'N/A';
                $contractsOptions[$contract->id] = $propertyName . ' - Unit ' . $unitNumber;
            }
            ?>
            <?= $this->Form->control('contract_id', [
                'type' => 'select',
                'options' => $contractsOptions,
                'class' => 'form-control',
                'required' => true,
                'label' => 'Contract'
            ]) ?>
        </div>
        
        <div class="mb-3">
            <?= $this->Form->control('amount', [
                'class' => 'form-control',
                'required' => true,
                'type' => 'number',
                'step' => '0.01',
                'min' => '0.01'
            ]) ?>
        </div>
        
        <div class="mb-3">
            <?= $this->Form->control('currency', [
                'type' => 'select',
                'options' => ['USD' => 'USD', 'EUR' => 'EUR', 'GBP' => 'GBP'],
                'class' => 'form-control',
                'default' => 'USD'
            ]) ?>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Payment Method <span class="text-danger">*</span></label>
            <div class="form-check">
                <input type="radio" name="payment_method" value="cash" id="payment_method_cash" class="form-check-input" required>
                <label class="form-check-label" for="payment_method_cash">Cash</label>
            </div>
            <div class="form-check">
                <input type="radio" name="payment_method" value="bank" id="payment_method_bank" class="form-check-input" required>
                <label class="form-check-label" for="payment_method_bank">Bank Transfer</label>
            </div>
            <div class="form-check">
                <input type="radio" name="payment_method" value="online" id="payment_method_online" class="form-check-input" disabled>
                <label class="form-check-label text-muted" for="payment_method_online">Online Payment <small>(Coming Soon)</small></label>
            </div>
        </div>
        
        <div class="mb-3">
            <?= $this->Form->control('reference', [
                'class' => 'form-control',
                'label' => 'Reference Number (for bank transfer)'
            ]) ?>
        </div>
        
        <div class="mb-3">
            <?= $this->Form->control('proof', [
                'type' => 'file',
                'class' => 'form-control',
                'label' => 'Upload Proof (Receipt/Transfer Slip)',
                'accept' => 'image/*,.pdf'
            ]) ?>
        </div>
        
        <div class="mb-3">
            <?= $this->Form->control('remarks', [
                'type' => 'textarea',
                'class' => 'form-control',
                'rows' => 3
            ]) ?>
        </div>
        
        <?= $this->Form->button('Submit Payment', ['class' => 'btn btn-primary']) ?>
        <?= $this->Form->end() ?>
    </div>
</div>

