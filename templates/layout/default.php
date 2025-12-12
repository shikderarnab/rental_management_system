<?php
/**
 * Default layout template
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?= $this->fetch('title') ?> - Rental Management System
    </title>
    <?= $this->Html->meta('icon') ?>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <?= $this->Html->link('Rental Management', ['controller' => 'Dashboard', 'action' => 'index'], ['class' => 'navbar-brand']) ?>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if ($this->Identity->isLoggedIn()): ?>
                        <li class="nav-item">
                            <?= $this->Html->link('Dashboard', ['controller' => 'Dashboard', 'action' => 'index'], ['class' => 'nav-link']) ?>
                        </li>
                        <?php if ($this->Identity->get('role') === 'landlord' || $this->Identity->get('role') === 'admin'): ?>
                            <li class="nav-item">
                                <?= $this->Html->link('Properties', ['controller' => 'Properties', 'action' => 'index'], ['class' => 'nav-link']) ?>
                            </li>
                            <li class="nav-item">
                                <?= $this->Html->link('Contracts', ['controller' => 'Contracts', 'action' => 'index'], ['class' => 'nav-link']) ?>
                            </li>
                            <li class="nav-item">
                                <?= $this->Html->link('Payments', ['controller' => 'Payments', 'action' => 'index'], ['class' => 'nav-link']) ?>
                            </li>
                        <?php endif; ?>
                        <?php if ($this->Identity->get('role') === 'admin'): ?>
                            <li class="nav-item">
                                <?= $this->Html->link('Users', ['controller' => 'Users', 'action' => 'index'], ['class' => 'nav-link']) ?>
                            </li>
                        <?php endif; ?>
                        <?php if ($this->Identity->get('role') === 'tenant'): ?>
                            <li class="nav-item">
                                <?= $this->Html->link('My Contracts', ['controller' => 'Contracts', 'action' => 'index'], ['class' => 'nav-link']) ?>
                            </li>
                            <li class="nav-item">
                                <?= $this->Html->link('My Payments', ['controller' => 'Payments', 'action' => 'index'], ['class' => 'nav-link']) ?>
                            </li>
                            <li class="nav-item">
                                <?= $this->Html->link('Disputes', ['controller' => 'Disputes', 'action' => 'index'], ['class' => 'nav-link']) ?>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <?= h($this->Identity->get('first_name')) ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><?= $this->Html->link('Profile', ['controller' => 'Profile', 'action' => 'index'], ['class' => 'dropdown-item']) ?></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><?= $this->Html->link('Logout', ['controller' => 'Users', 'action' => 'logout'], ['class' => 'dropdown-item']) ?></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <?= $this->Html->link('Login', ['controller' => 'Users', 'action' => 'login'], ['class' => 'nav-link']) ?>
                        </li>
                        <li class="nav-item">
                            <?= $this->Html->link('Register', ['controller' => 'Users', 'action' => 'register'], ['class' => 'nav-link']) ?>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container-fluid mt-4">
        <?= $this->Flash->render() ?>
        <?= $this->fetch('content') ?>
    </main>

    <footer class="mt-5 py-4 bg-light text-center">
        <div class="container">
            <p class="text-muted">
                &copy; <?= date('Y') ?> Rental Management System - Developed by
                <?= $this->Html->link('Tasin', 'https://github.com/tasinjaber', [
                    'class' => 'text-decoration-none',
                    'target' => '_blank',
                    'rel' => 'noopener noreferrer',
                ]) ?>
                &
                <?= $this->Html->link('Arnab', 'https://github.com/shikderarnab', [
                    'class' => 'text-decoration-none',
                    'target' => '_blank',
                    'rel' => 'noopener noreferrer',
                ]) ?>.
                All rights reserved.
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->fetch('scriptBottom') ?>
</body>
</html>

