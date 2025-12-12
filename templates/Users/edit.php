<?php
/**
 * Edit User view (Admin only)
 */
$this->assign('title', 'Edit User');
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3>Edit User</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <?= $this->Html->link('← Back to User Details', ['action' => 'view', $user->id], ['class' => 'btn btn-sm btn-secondary']) ?>
                </div>
                
                <?= $this->Form->create($user) ?>
                
                <h4 class="mb-4">Edit <?= ucfirst($user->role) ?> User</h4>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $this->Form->control('email', [
                            'class' => 'form-control',
                            'required' => true,
                            'label' => 'Email Address'
                        ]) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $this->Form->control('password', [
                            'type' => 'password',
                            'class' => 'form-control',
                            'label' => 'New Password',
                            'placeholder' => 'Leave blank to keep current password',
                            'value' => '',
                            'required' => false
                        ]) ?>
                        <small class="form-text text-muted">Leave blank if you don't want to change the password</small>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $this->Form->control('first_name', [
                            'class' => 'form-control',
                            'required' => true,
                            'label' => 'First Name'
                        ]) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $this->Form->control('last_name', [
                            'class' => 'form-control',
                            'required' => true,
                            'label' => 'Last Name'
                        ]) ?>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $this->Form->control('phone', [
                            'class' => 'form-control',
                            'label' => 'Phone Number'
                        ]) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $this->Form->control('role', [
                            'type' => 'select',
                            'options' => [
                                'admin' => 'Admin',
                                'landlord' => 'Landlord',
                                'tenant' => 'Tenant'
                            ],
                            'class' => 'form-control',
                            'required' => true,
                            'label' => 'Role'
                        ]) ?>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <?= $this->Form->control('is_active', [
                            'type' => 'checkbox',
                            'class' => 'form-check-input',
                            'label' => 'Active'
                        ]) ?>
                    </div>
                    <div class="col-md-4 mb-3">
                        <?= $this->Form->control('email_verified', [
                            'type' => 'checkbox',
                            'class' => 'form-check-input',
                            'label' => 'Email Verified'
                        ]) ?>
                    </div>
                    <div class="col-md-4 mb-3">
                        <?= $this->Form->control('phone_verified', [
                            'type' => 'checkbox',
                            'class' => 'form-check-input',
                            'label' => 'Phone Verified'
                        ]) ?>
                    </div>
                </div>
                
                <?php if ($user->role === 'landlord'): ?>
                    <!-- Landlord-specific fields -->
                    <hr class="my-4">
                    <h5 class="mb-3">Landlord Information</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="company_name" class="form-label">Company Name</label>
                            <input type="text" name="company_name" id="company_name" class="form-control" 
                                   value="<?= h($landlordData->company_name ?? '') ?>" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tax_id" class="form-label">Tax ID</label>
                            <input type="text" name="tax_id" id="tax_id" class="form-control" 
                                   value="<?= h($landlordData->tax_id ?? '') ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea name="address" id="address" class="form-control" rows="3"><?= h($landlordData->address ?? '') ?></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="bank_account" class="form-label">Bank Account</label>
                            <input type="text" name="bank_account" id="bank_account" class="form-control" 
                                   value="<?= h($landlordData->bank_account ?? '') ?>" />
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="mt-4">
                    <?= $this->Form->button('Update User', ['class' => 'btn btn-primary']) ?>
                    <?= $this->Html->link('Cancel', ['action' => 'view', $user->id], ['class' => 'btn btn-secondary']) ?>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

