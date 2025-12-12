<?php
/**
 * User view
 */
$this->assign('title', 'User Details');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>User Details</h2>
    <div>
        <?= $this->Html->link('Edit User', ['action' => 'edit', $user->id], ['class' => 'btn btn-primary']) ?>
        <?= $this->Html->link('Back to Users', ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4>Personal Information</h4>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">ID:</dt>
                    <dd class="col-sm-8"><?= h($user->id) ?></dd>
                    
                    <dt class="col-sm-4">First Name:</dt>
                    <dd class="col-sm-8"><?= h($user->first_name ?? 'N/A') ?></dd>
                    
                    <dt class="col-sm-4">Last Name:</dt>
                    <dd class="col-sm-8"><?= h($user->last_name ?? 'N/A') ?></dd>
                    
                    <dt class="col-sm-4">Email:</dt>
                    <dd class="col-sm-8"><?= h($user->email) ?></dd>
                    
                    <dt class="col-sm-4">Phone:</dt>
                    <dd class="col-sm-8"><?= h($user->phone ?? 'N/A') ?></dd>
                    
                    <dt class="col-sm-4">Role:</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-<?= $user->role === 'admin' ? 'danger' : ($user->role === 'landlord' ? 'primary' : 'info') ?>">
                            <?= h(ucfirst($user->role ?? 'N/A')) ?>
                        </span>
                    </dd>
                    
                    <dt class="col-sm-4">Status:</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-<?= ($user->is_active ?? true) ? 'success' : 'secondary' ?>">
                            <?= ($user->is_active ?? true) ? 'Active' : 'Inactive' ?>
                        </span>
                    </dd>
                    
                    <dt class="col-sm-4">Email Verified:</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-<?= ($user->email_verified ?? false) ? 'success' : 'warning' ?>">
                            <?= ($user->email_verified ?? false) ? 'Yes' : 'No' ?>
                        </span>
                    </dd>
                    
                    <dt class="col-sm-4">Phone Verified:</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-<?= ($user->phone_verified ?? false) ? 'success' : 'warning' ?>">
                            <?= ($user->phone_verified ?? false) ? 'Yes' : 'No' ?>
                        </span>
                    </dd>
                    
                    <dt class="col-sm-4">Created:</dt>
                    <dd class="col-sm-8"><?= $user->created ? h($user->created->format('Y-m-d H:i:s')) : 'N/A' ?></dd>
                    
                    <dt class="col-sm-4">Modified:</dt>
                    <dd class="col-sm-8"><?= $user->modified ? h($user->modified->format('Y-m-d H:i:s')) : 'N/A' ?></dd>
                </dl>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <?php if ($user->role === 'tenant' && isset($user->tenant)): ?>
            <div class="card">
                <div class="card-header">
                    <h5>Tenant Profile</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-6">Tenant ID:</dt>
                        <dd class="col-sm-6"><?= h($user->tenant->id) ?></dd>
                    </dl>
                </div>
            </div>
        <?php elseif ($user->role === 'landlord' && isset($user->landlord)): ?>
            <div class="card">
                <div class="card-header">
                    <h5>Landlord Profile</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-6">Landlord ID:</dt>
                        <dd class="col-sm-6"><?= h($user->landlord->id) ?></dd>
                        
                        <?php if (!empty($user->landlord->company_name)): ?>
                            <dt class="col-sm-6">Company Name:</dt>
                            <dd class="col-sm-6"><?= h($user->landlord->company_name) ?></dd>
                        <?php endif; ?>
                        
                        <?php if (!empty($user->landlord->tax_id)): ?>
                            <dt class="col-sm-6">Tax ID:</dt>
                            <dd class="col-sm-6"><?= h($user->landlord->tax_id) ?></dd>
                        <?php endif; ?>
                        
                        <?php if (!empty($user->landlord->address)): ?>
                            <dt class="col-sm-6">Address:</dt>
                            <dd class="col-sm-6"><?= h($user->landlord->address) ?></dd>
                        <?php endif; ?>
                        
                        <?php if (!empty($user->landlord->bank_account)): ?>
                            <dt class="col-sm-6">Bank Account:</dt>
                            <dd class="col-sm-6"><?= h($user->landlord->bank_account) ?></dd>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>
        <?php elseif ($user->role === 'admin'): ?>
            <div class="card">
                <div class="card-header">
                    <h5>Admin Account</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">This is an administrator account.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-header bg-warning">
                    <h5>No Profile</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">This user does not have a profile record yet.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

