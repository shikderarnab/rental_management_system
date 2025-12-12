<?php
/**
 * Profile view
 */
$this->assign('title', 'My Profile');
?>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <h2>My Profile</h2>
        
        <?= $this->Form->create($user) ?>
        
        <div class="card">
            <div class="card-header">
                <h4>Personal Information</h4>
            </div>
            <div class="card-body">
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
                        <?= $this->Form->control('email', [
                            'class' => 'form-control',
                            'required' => true,
                            'type' => 'email',
                            'label' => 'Email Address'
                        ]) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $this->Form->control('phone', [
                            'class' => 'form-control',
                            'label' => 'Phone Number'
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h4>Change Password <small class="text-muted">(Optional)</small></h4>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <?= $this->Form->control('password', [
                        'type' => 'password',
                        'class' => 'form-control',
                        'label' => 'New Password',
                        'placeholder' => 'Leave blank to keep current password',
                        'value' => '',
                        'required' => false
                    ]) ?>
                    <small class="form-text text-muted">
                        <i class="bi bi-info-circle"></i> Leave blank if you don't want to change your password. 
                        Password update is optional - you can update other information without changing your password.
                    </small>
                </div>
            </div>
        </div>
        
        <?php if ($user->role === 'tenant' && isset($user->tenant)): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <h4>Tenant Information</h4>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Tenant ID:</dt>
                        <dd class="col-sm-8"><?= h($user->tenant->id) ?></dd>
                    </dl>
                </div>
            </div>
        <?php elseif ($user->role === 'landlord' && isset($user->landlord)): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <h4>Landlord Information</h4>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Landlord ID:</dt>
                        <dd class="col-sm-8"><?= h($user->landlord->id) ?></dd>
                        <?php if (!empty($user->landlord->company_name)): ?>
                            <dt class="col-sm-4">Company Name:</dt>
                            <dd class="col-sm-8"><?= h($user->landlord->company_name) ?></dd>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="mt-4">
            <?= $this->Form->button('Update Profile', ['class' => 'btn btn-primary']) ?>
            <?= $this->Html->link('Cancel', ['controller' => 'Dashboard', 'action' => 'index'], ['class' => 'btn btn-secondary']) ?>
        </div>
        
        <?= $this->Form->end() ?>
    </div>
</div>

