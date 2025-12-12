<?php
/**
 * Dispute view
 */
$this->assign('title', 'Dispute Details');
?>

<div class="row">
    <div class="col-md-10 offset-md-1">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Dispute Details</h2>
            <?= $this->Html->link('Back to Disputes', ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
        </div>

        <div class="card">
            <div class="card-header">
                <h4><?= h($dispute->subject) ?></h4>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Status:</dt>
                    <dd class="col-sm-9">
                        <span class="badge bg-<?= $dispute->status === 'resolved' ? 'success' : ($dispute->status === 'closed' ? 'secondary' : ($dispute->status === 'reviewing' ? 'info' : 'warning')) ?>">
                            <?= h(ucfirst($dispute->status ?? 'Open')) ?>
                        </span>
                    </dd>
                    
                    <dt class="col-sm-3">Description:</dt>
                    <dd class="col-sm-9"><?= nl2br(h($dispute->description)) ?></dd>
                    
                    <?php if ($dispute->contract): ?>
                        <dt class="col-sm-3">Related Contract:</dt>
                        <dd class="col-sm-9">
                            <?= $this->Html->link('Contract #' . $dispute->contract->id, ['controller' => 'Contracts', 'action' => 'view', $dispute->contract->id]) ?>
                        </dd>
                    <?php endif; ?>
                    
                    <?php if ($dispute->payment): ?>
                        <dt class="col-sm-3">Related Payment:</dt>
                        <dd class="col-sm-9">
                            <?= $this->Html->link('Payment #' . $dispute->payment->id, ['controller' => 'Payments', 'action' => 'view', $dispute->payment->id]) ?>
                        </dd>
                    <?php endif; ?>
                    
                    <dt class="col-sm-3">Created:</dt>
                    <dd class="col-sm-9"><?= $dispute->created ? h($dispute->created->format('Y-m-d H:i:s')) : 'N/A' ?></dd>
                </dl>
            </div>
        </div>

        <?php if (!empty($dispute->dispute_messages)): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <h4>Messages</h4>
                </div>
                <div class="card-body">
                    <?php foreach ($dispute->dispute_messages as $msg): ?>
                        <div class="mb-3 p-3 border rounded">
                            <strong><?= isset($msg->user) ? h(($msg->user->first_name ?? '') . ' ' . ($msg->user->last_name ?? '')) : 'User' ?></strong>
                            <small class="text-muted"><?= $msg->created ? h($msg->created->format('Y-m-d H:i')) : '' ?></small>
                            <p class="mt-2 mb-0"><?= nl2br(h($msg->message)) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($dispute->status !== 'closed'): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <h4>Add Message</h4>
                </div>
                <div class="card-body">
                    <?= $this->Form->create($message) ?>
                    <?= $this->Form->control('message', [
                        'type' => 'textarea',
                        'class' => 'form-control',
                        'rows' => 4,
                        'label' => 'Your Message',
                        'required' => true
                    ]) ?>
                    
                    <?php if ($this->Identity->get('role') === 'admin' || $this->Identity->get('role') === 'landlord'): ?>
                        <div class="mb-3 mt-3">
                            <?= $this->Form->control('status', [
                                'type' => 'select',
                                'options' => [
                                    'open' => 'Open',
                                    'reviewing' => 'Reviewing',
                                    'resolved' => 'Resolved',
                                    'closed' => 'Closed'
                                ],
                                'class' => 'form-control',
                                'label' => 'Update Status',
                                'empty' => 'Keep Current Status'
                            ]) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?= $this->Form->button('Send Message', ['class' => 'btn btn-primary']) ?>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

