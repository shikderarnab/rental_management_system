<?php
/**
 * Add User view (Admin only)
 */
$this->assign('title', 'Add User');
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3>Add New User</h3>
            </div>
            <div class="card-body">
                <?php if (!$selectedRole): ?>
                    <!-- Step 1: Role Selection -->
                    <div id="role-selection">
                        <h4 class="mb-4">Select User Role</h4>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="card role-card" data-role="admin" style="cursor: pointer; border: 2px solid #ddd;">
                                    <div class="card-body text-center">
                                        <h5>Admin</h5>
                                        <p class="text-muted small">System administrator</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card role-card" data-role="landlord" style="cursor: pointer; border: 2px solid #ddd;">
                                    <div class="card-body text-center">
                                        <h5>Landlord</h5>
                                        <p class="text-muted small">Property owner/manager</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card role-card" data-role="tenant" style="cursor: pointer; border: 2px solid #ddd;">
                                    <div class="card-body text-center">
                                        <h5>Tenant</h5>
                                        <p class="text-muted small">Rental tenant</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <form id="role-form" method="get" action="<?= $this->Url->build(['action' => 'add']) ?>">
                            <input type="hidden" name="role" id="selected-role" value="">
                        </form>
                    </div>
                <?php else: ?>
                    <!-- Step 2: User Form Based on Role -->
                    <div id="user-form">
                        <div class="mb-3">
                            <a href="<?= $this->Url->build(['action' => 'add']) ?>" class="btn btn-sm btn-secondary">
                                ← Change Role
                            </a>
                        </div>
                        
                        <?= $this->Form->create($user) ?>
                        <?= $this->Form->hidden('role', ['value' => $selectedRole]) ?>
                        
                        <h4 class="mb-4">Create <?= ucfirst($selectedRole) ?> User</h4>
                        
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
                                    'required' => true,
                                    'label' => 'Password'
                                ]) ?>
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
                                <?= $this->Form->control('is_active', [
                                    'type' => 'checkbox',
                                    'class' => 'form-check-input',
                                    'label' => 'Active',
                                    'default' => true
                                ]) ?>
                            </div>
                        </div>
                        
                        <?php if ($selectedRole === 'landlord'): ?>
                            <!-- Landlord-specific fields -->
                            <hr class="my-4">
                            <h5 class="mb-3">Landlord Information</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="company_name" class="form-label">Company Name</label>
                                    <input type="text" name="company_name" id="company_name" class="form-control" />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="tax_id" class="form-label">Tax ID</label>
                                    <input type="text" name="tax_id" id="tax_id" class="form-control" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea name="address" id="address" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="bank_account" class="form-label">Bank Account</label>
                                    <input type="text" name="bank_account" id="bank_account" class="form-control" />
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mt-4">
                            <?= $this->Form->button('Create User', ['class' => 'btn btn-primary']) ?>
                            <?= $this->Html->link('Cancel', ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
                        </div>
                        <?= $this->Form->end() ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleCards = document.querySelectorAll('.role-card');
    const selectedRoleInput = document.getElementById('selected-role');
    const roleForm = document.getElementById('role-form');
    
    roleCards.forEach(card => {
        card.addEventListener('click', function() {
            const role = this.getAttribute('data-role');
            selectedRoleInput.value = role;
            roleForm.submit();
        });
        
        card.addEventListener('mouseenter', function() {
            this.style.borderColor = '#007bff';
            this.style.boxShadow = '0 2px 4px rgba(0,123,255,0.2)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.borderColor = '#ddd';
            this.style.boxShadow = 'none';
        });
    });
});
</script>

<style>
.role-card:hover {
    transform: translateY(-2px);
    transition: all 0.2s;
}
</style>

