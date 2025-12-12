<?php
/**
 * Payment view
 */
$this->assign('title', 'Payment Details');
?>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Payment Details</h2>
            <?= $this->Html->link('Back to Payments', ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>Payment Information</h4>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Payment ID:</dt>
                    <dd class="col-sm-8"><?= h($payment->id) ?></dd>
                    
                    <dt class="col-sm-4">Amount:</dt>
                    <dd class="col-sm-8"><strong><?= h($payment->currency . ' ' . number_format($payment->amount, 2)) ?></strong></dd>
                    
                    <dt class="col-sm-4">Payment Method:</dt>
                    <dd class="col-sm-8"><?= h(ucfirst($payment->payment_method ?? 'N/A')) ?></dd>
                    
                    <dt class="col-sm-4">Status:</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-<?= $payment->payment_status === 'verified' ? 'success' : ($payment->payment_status === 'rejected' ? 'danger' : 'warning') ?>">
                            <?= h(ucfirst($payment->payment_status ?? 'Pending')) ?>
                        </span>
                    </dd>
                    
                    <dt class="col-sm-4">Paid Date:</dt>
                    <dd class="col-sm-8"><?= $payment->paid_at ? h($payment->paid_at->format('Y-m-d H:i:s')) : ($payment->created ? h($payment->created->format('Y-m-d H:i:s')) : 'N/A') ?></dd>
                    
                    <?php if ($payment->verified_at): ?>
                        <dt class="col-sm-4">Verified Date:</dt>
                        <dd class="col-sm-8"><?= h($payment->verified_at->format('Y-m-d H:i:s')) ?></dd>
                    <?php endif; ?>
                    
                    <?php if ($payment->reference): ?>
                        <dt class="col-sm-4">Reference Number:</dt>
                        <dd class="col-sm-8"><?= h($payment->reference) ?></dd>
                    <?php endif; ?>
                    
                    <?php if ($payment->remarks): ?>
                        <dt class="col-sm-4">Remarks:</dt>
                        <dd class="col-sm-8"><?= h($payment->remarks) ?></dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>

        <?php if (isset($payment->contract)): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <h4>Contract Information</h4>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Property:</dt>
                        <dd class="col-sm-8">
                            <?php if (isset($payment->contract->unit->property)): ?>
                                <?= h($payment->contract->unit->property->name) ?>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </dd>
                        
                        <dt class="col-sm-4">Unit:</dt>
                        <dd class="col-sm-8">
                            <?php if (isset($payment->contract->unit)): ?>
                                <?= h($payment->contract->unit->unit_number ?? 'N/A') ?>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </dd>
                        
                        <dt class="col-sm-4">Rent Amount:</dt>
                        <dd class="col-sm-8">
                            <?= h(($payment->contract->currency ?? 'USD') . ' ' . number_format($payment->contract->rent_amount ?? 0, 2)) ?>
                        </dd>
                    </dl>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($payment->tenant) && isset($payment->tenant->user)): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <h4>Tenant Information</h4>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Name:</dt>
                        <dd class="col-sm-8">
                            <?= h(trim(($payment->tenant->user->first_name ?? '') . ' ' . ($payment->tenant->user->last_name ?? ''))) ?>
                        </dd>
                        
                        <dt class="col-sm-4">Email:</dt>
                        <dd class="col-sm-8"><?= h($payment->tenant->user->email ?? 'N/A') ?></dd>
                        
                        <?php if ($payment->tenant->user->phone): ?>
                            <dt class="col-sm-4">Phone:</dt>
                            <dd class="col-sm-8"><?= h($payment->tenant->user->phone) ?></dd>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($payment->landlord) && isset($payment->landlord->user)): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <h4>Landlord Information</h4>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Name:</dt>
                        <dd class="col-sm-8">
                            <?php
                            $landlordName = '';
                            if (!empty($payment->landlord->company_name)) {
                                $landlordName = $payment->landlord->company_name;
                            } elseif (isset($payment->landlord->user)) {
                                $landlordName = trim(($payment->landlord->user->first_name ?? '') . ' ' . ($payment->landlord->user->last_name ?? ''));
                            }
                            echo h($landlordName ?: 'N/A');
                            ?>
                        </dd>
                        
                        <dt class="col-sm-4">Email:</dt>
                        <dd class="col-sm-8"><?= h($payment->landlord->user->email ?? 'N/A') ?></dd>
                        
                        <?php if ($payment->landlord->user->phone): ?>
                            <dt class="col-sm-4">Phone:</dt>
                            <dd class="col-sm-8"><?= h($payment->landlord->user->phone) ?></dd>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($payment->proof_path): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <h4>Proof of Payment</h4>
                </div>
                <div class="card-body">
                    <?php
                    $proofPath = WWW_ROOT . $payment->proof_path;
                    if (file_exists($proofPath)):
                        $extension = strtolower(pathinfo($payment->proof_path, PATHINFO_EXTENSION));
                        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])):
                    ?>
                        <img src="<?= $this->Url->build('/' . $payment->proof_path) ?>" alt="Proof of Payment" class="img-fluid" style="max-width: 500px;">
                    <?php else: ?>
                        <a href="<?= $this->Url->build('/' . $payment->proof_path) ?>" target="_blank" class="btn btn-primary">
                            <i class="fas fa-file-pdf"></i> View Proof Document
                        </a>
                    <?php
                        endif;
                    else:
                    ?>
                        <p class="text-muted">Proof file not found.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="mt-4">
            <?php if ($payment->receipt_path || true): ?>
                <?= $this->Html->link('Download Payment Slip', ['action' => 'downloadReceipt', $payment->id], ['class' => 'btn btn-success']) ?>
            <?php endif; ?>
            <?php if ($payment->payment_status === 'verified' && isset($payment->invoice)): ?>
                <?= $this->Html->link('Download Invoice', ['action' => 'downloadInvoice', $payment->id], ['class' => 'btn btn-info']) ?>
            <?php endif; ?>
            
            <?php if (($this->Identity->get('role') === 'landlord' || $this->Identity->get('role') === 'admin') && $payment->payment_status === 'pending'): ?>
                <?= $this->Form->postLink('Verify Payment', ['action' => 'verify', $payment->id], [
                    'class' => 'btn btn-success',
                    'confirm' => 'Are you sure you want to verify this payment?',
                    'data' => ['action' => 'verify']
                ]) ?>
                <?= $this->Form->postLink('Reject Payment', ['action' => 'verify', $payment->id], [
                    'class' => 'btn btn-danger',
                    'confirm' => 'Are you sure you want to reject this payment?',
                    'data' => ['action' => 'reject']
                ]) ?>
            <?php endif; ?>
        </div>
    </div>
</div>

