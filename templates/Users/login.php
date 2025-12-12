<?php
/**
 * Login view
 */
$this->assign('title', 'Login');
?>

<div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3>Login</h3>
            </div>
            <div class="card-body">
                <?= $this->Form->create() ?>
                <div class="mb-3">
                    <?= $this->Form->control('email', ['class' => 'form-control', 'required' => true]) ?>
                </div>
                <div class="mb-3">
                    <?= $this->Form->control('password', ['class' => 'form-control', 'required' => true]) ?>
                </div>
                <div class="mb-3">
                    <?= $this->Form->button('Login', ['class' => 'btn btn-primary w-100']) ?>
                </div>
                <?= $this->Form->end() ?>
                <p class="text-center">
                    Don't have an account? <?= $this->Html->link('Register', ['action' => 'register']) ?>
                </p>
            </div>
        </div>
    </div>
</div>

