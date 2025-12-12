<?php
/**
 * Contract view
 */
$this->assign('title', 'Contract Details');
?>

<div class="mb-4">
    <?= $this->Html->link('← Back to Contracts', ['action' => 'index'], ['class' => 'btn btn-sm btn-secondary']) ?>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3>Contract #<?= h($contract->id) ?></h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Unit:</dt>
                    <dd class="col-sm-8">
                        <?= h(($contract->unit->property->name ?? 'N/A') . ' - Unit ' . ($contract->unit->unit_number ?? 'N/A')) ?>
                    </dd>
                    
                    <dt class="col-sm-4">Tenant:</dt>
                    <dd class="col-sm-8">
                        <?php 
                        $tenantName = '';
                        if (isset($contract->tenant->user)) {
                            $tenantName = trim(($contract->tenant->user->first_name ?? '') . ' ' . ($contract->tenant->user->last_name ?? ''));
                            if ($tenantName) {
                                echo h($tenantName);
                                if (isset($contract->tenant->user->email)) {
                                    echo ' <small class="text-muted">(' . h($contract->tenant->user->email) . ')</small>';
                                }
                            }
                        }
                        if (!$tenantName) {
                            echo h('Tenant #' . ($contract->tenant_id ?? 'N/A'));
                        }
                        ?>
                    </dd>
                    
                    <dt class="col-sm-4">Landlord:</dt>
                    <dd class="col-sm-8">
                        <?php 
                        $landlordName = '';
                        if (isset($contract->landlord)) {
                            if (!empty($contract->landlord->company_name)) {
                                $landlordName = $contract->landlord->company_name;
                            } elseif (isset($contract->landlord->user)) {
                                $landlordName = trim(($contract->landlord->user->first_name ?? '') . ' ' . ($contract->landlord->user->last_name ?? ''));
                            }
                            if ($landlordName && isset($contract->landlord->user->email)) {
                                echo h($landlordName) . ' <small class="text-muted">(' . h($contract->landlord->user->email) . ')</small>';
                            } elseif ($landlordName) {
                                echo h($landlordName);
                            }
                        }
                        if (!$landlordName) {
                            echo h('Landlord #' . ($contract->landlord_id ?? 'N/A'));
                        }
                        ?>
                    </dd>
                    
                    <dt class="col-sm-4">Start Date:</dt>
                    <dd class="col-sm-8"><?= $contract->start_date ? h($contract->start_date->format('Y-m-d')) : 'N/A' ?></dd>
                    
                    <dt class="col-sm-4">End Date:</dt>
                    <dd class="col-sm-8"><?= $contract->end_date ? h($contract->end_date->format('Y-m-d')) : 'N/A' ?></dd>
                    
                    <dt class="col-sm-4">Rent Amount:</dt>
                    <dd class="col-sm-8"><?= h(($contract->currency ?? 'USD') . ' ' . number_format($contract->rent_amount ?? 0, 2)) ?></dd>
                    
                    <dt class="col-sm-4">Status:</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-<?= $contract->status === 'active' ? 'success' : ($contract->status === 'pending_signature' ? 'warning' : 'secondary') ?>">
                            <?= h(ucfirst($contract->status ?? 'N/A')) ?>
                        </span>
                    </dd>
                    
                    <?php if ($contract->agreement_file): ?>
                        <dt class="col-sm-4">Agreement File:</dt>
                        <dd class="col-sm-8">
                            <?= $this->Html->link('Download Agreement', '/' . $contract->agreement_file, ['class' => 'btn btn-sm btn-outline-primary', 'target' => '_blank']) ?>
                        </dd>
                    <?php endif; ?>
                    
                    <?php if ($contract->signed_file): ?>
                        <dt class="col-sm-4">Signed File:</dt>
                        <dd class="col-sm-8">
                            <?= $this->Html->link('Download Signed Contract', '/' . $contract->signed_file, ['class' => 'btn btn-sm btn-outline-success', 'target' => '_blank']) ?>
                        </dd>
                    <?php endif; ?>
                    
                    <dt class="col-sm-4">Created:</dt>
                    <dd class="col-sm-8"><?= $contract->created ? h($contract->created->format('Y-m-d H:i')) : 'N/A' ?></dd>
                </dl>
                
                <div class="mt-4">
                    <?php if ($this->Identity->get('role') === 'landlord' || $this->Identity->get('role') === 'admin'): ?>
                        <?= $this->Html->link('Edit Contract', ['action' => 'edit', $contract->id], ['class' => 'btn btn-primary']) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <?php if (!empty($contract->signatures)): ?>
            <div class="card mb-3">
                <div class="card-header">
                    <h5>Signatures</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($contract->signatures as $signature): ?>
                        <div class="mb-3">
                            <strong>Signature #<?= h($signature->id) ?></strong><br>
                            <small class="text-muted">
                                Type: <?= h($signature->signature_type ?? 'N/A') ?><br>
                                Signed: <?= $signature->signed_at ? h($signature->signed_at->format('Y-m-d H:i')) : 'N/A' ?>
                            </small>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if ($this->Identity->get('role') === 'tenant' && $contract->status === 'pending_signature'): ?>
            <div class="card">
                <div class="card-body">
                    <?= $this->Html->link('Sign Contract', ['action' => 'sign', $contract->id], ['class' => 'btn btn-success btn-block']) ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

