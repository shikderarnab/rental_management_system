<?php
/**
 * Register view
 */
$this->assign('title', 'Register');
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Register</h3>
            </div>
            <div class="card-body">
                <?= $this->Form->create($user) ?>
                <div class="mb-3">
                    <?= $this->Form->control('email', [
                        'class' => 'form-control',
                        'required' => true,
                        'label' => 'Email Address'
                    ]) ?>
                </div>
                <div class="mb-3">
                    <?= $this->Form->control('password', [
                        'type' => 'password',
                        'class' => 'form-control',
                        'required' => true,
                        'label' => 'Password'
                    ]) ?>
                </div>
                <div class="mb-3">
                    <?= $this->Form->control('first_name', [
                        'class' => 'form-control',
                        'required' => true,
                        'label' => 'First Name'
                    ]) ?>
                </div>
                <div class="mb-3">
                    <?= $this->Form->control('last_name', [
                        'class' => 'form-control',
                        'required' => true,
                        'label' => 'Last Name'
                    ]) ?>
                </div>
                <div class="mb-3">
                    <?= $this->Form->control('phone', [
                        'class' => 'form-control',
                        'label' => 'Phone (Optional)'
                    ]) ?>
                </div>
                <div class="mb-3">
                    <?= $this->Form->control('role', [
                        'type' => 'select',
                        'options' => ['tenant' => 'Tenant', 'landlord' => 'Landlord'],
                        'class' => 'form-control',
                        'default' => 'tenant',
                        'label' => 'Role'
                    ]) ?>
                </div>
                <div class="mb-3">
                    <?= $this->Form->button('Register', ['class' => 'btn btn-primary w-100']) ?>
                </div>
                <?= $this->Form->end() ?>
                <p class="text-center">
                    Already have an account? <?= $this->Html->link('Login', ['action' => 'login']) ?>
                </p>
            </div>
        </div>
    </div>
</div>

