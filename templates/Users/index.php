<?php
/**
 * Users index view
 */
$this->assign('title', 'Users');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Users</h2>
    <?= $this->Html->link('Add User', ['action' => 'add'], ['class' => 'btn btn-primary']) ?>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Role</th>
            <th>Status</th>
            <th>Profile</th>
            <th>Created</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= h($user->id) ?></td>
                <td><?= h(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?></td>
                <td><?= h($user->email) ?></td>
                <td><?= h($user->phone ?? 'N/A') ?></td>
                <td>
                    <span class="badge bg-<?= $user->role === 'admin' ? 'danger' : ($user->role === 'landlord' ? 'primary' : 'info') ?>">
                        <?= h(ucfirst($user->role ?? 'N/A')) ?>
                    </span>
                </td>
                <td>
                    <span class="badge bg-<?= ($user->is_active ?? true) ? 'success' : 'secondary' ?>">
                        <?= ($user->is_active ?? true) ? 'Active' : 'Inactive' ?>
                    </span>
                </td>
                <td>
                    <?php if ($user->role === 'tenant' && isset($user->tenant)): ?>
                        <span class="badge bg-info">Tenant Profile</span>
                    <?php elseif ($user->role === 'landlord' && isset($user->landlord)): ?>
                        <span class="badge bg-primary">Landlord Profile</span>
                    <?php elseif ($user->role === 'admin'): ?>
                        <span class="badge bg-danger">Admin</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark">No Profile</span>
                    <?php endif; ?>
                </td>
                <td><?= $user->created ? h($user->created->format('Y-m-d')) : 'N/A' ?></td>
                <td>
                    <?= $this->Html->link('View', ['action' => 'view', $user->id], ['class' => 'btn btn-sm btn-info']) ?>
                    <?= $this->Html->link('Edit', ['action' => 'edit', $user->id], ['class' => 'btn btn-sm btn-primary']) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if (empty($users)): ?>
    <div class="alert alert-info">
        No users found.
    </div>
<?php endif; ?>

