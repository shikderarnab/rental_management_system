<?php
/**
 * Dashboard view
 */
$this->assign('title', 'Dashboard');
?>

<div class="row">
    <div class="col-12">
        <h1>Dashboard</h1>
        <p>Welcome, <?= h($user->first_name . ' ' . $user->last_name) ?>!</p>
    </div>
</div>

<?php if ($user->role === 'admin' || $user->role === 'landlord'): ?>
    <!-- Statistics Cards -->
    <div class="row mt-4">
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Properties</h5>
                            <h2 class="mb-0"><?= $data['total_properties'] ?? 0 ?></h2>
                        </div>
                        <i class="bi bi-building fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Contracts</h5>
                            <h2 class="mb-0"><?= $data['total_contracts'] ?? 0 ?></h2>
                        </div>
                        <i class="bi bi-file-earmark-text fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Active Contracts</h5>
                            <h2 class="mb-0"><?= $data['active_contracts'] ?? 0 ?></h2>
                        </div>
                        <i class="bi bi-check-circle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Pending Payments</h5>
                            <h2 class="mb-0"><?= $data['pending_payments'] ?? 0 ?></h2>
                        </div>
                        <i class="bi bi-clock-history fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($user->role === 'admin'): ?>
        <div class="row mt-2">
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-secondary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-0">Total Payments</h5>
                                <h2 class="mb-0"><?= $data['total_payments'] ?? 0 ?></h2>
                            </div>
                            <i class="bi bi-cash-stack fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-dark">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-0">Tenants</h5>
                                <h2 class="mb-0"><?= $data['total_tenants'] ?? 0 ?></h2>
                            </div>
                            <i class="bi bi-people fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-danger">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-0">Landlords</h5>
                                <h2 class="mb-0"><?= $data['total_landlords'] ?? 0 ?></h2>
                            </div>
                            <i class="bi bi-person-badge fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white" style="background-color: #6f42c1;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-0">Total Revenue</h5>
                                <h2 class="mb-0"><?= number_format($data['total_revenue']->total ?? 0, 2) ?></h2>
                            </div>
                            <i class="bi bi-graph-up fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="row mt-2">
            <div class="col-md-4 mb-3">
                <div class="card text-white" style="background-color: #6f42c1;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-0">Total Revenue</h5>
                                <h2 class="mb-0"><?= number_format($data['total_revenue']->total ?? 0, 2) ?></h2>
                            </div>
                            <i class="bi bi-graph-up fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-0">Verified Payments</h5>
                                <h2 class="mb-0"><?= $data['verified_payments'] ?? 0 ?></h2>
                            </div>
                            <i class="bi bi-check-circle fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Charts Section -->
    <?php if (!empty($chartData)): ?>
        <div class="row mt-4">
            <!-- Monthly Revenue Chart -->
            <?php if (!empty($chartData['monthly_revenue'])): ?>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-graph-up"></i> Monthly Revenue (Last 6 Months)</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="revenueChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Payment Status Chart -->
            <?php if (!empty($chartData['payment_status'])): ?>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-pie-chart"></i> Payment Status Distribution</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="paymentStatusChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Contract Status Chart -->
            <?php if (!empty($chartData['contract_status'])): ?>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Contract Status Distribution</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="contractStatusChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Top 5 Sections -->
    <?php if (!empty($topData)): ?>
        <div class="row mt-4">
            <!-- Top 5 Properties by Revenue -->
            <?php if (!empty($topData['top_properties'])): ?>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-trophy"></i> Top 5 Properties by Revenue</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Rank</th>
                                            <th>Property</th>
                                            <th class="text-end">Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $rank = 1; foreach ($topData['top_properties'] as $property): ?>
                                            <tr>
                                                <td>
                                                    <?php if ($rank == 1): ?>
                                                        <span class="badge bg-warning text-dark">🥇</span>
                                                    <?php elseif ($rank == 2): ?>
                                                        <span class="badge bg-secondary">🥈</span>
                                                    <?php elseif ($rank == 3): ?>
                                                        <span class="badge bg-danger">🥉</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-light text-dark"><?= $rank ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= h($property->property_name ?? 'N/A') ?></td>
                                                <td class="text-end"><strong><?= number_format($property->total_revenue ?? 0, 2) ?></strong></td>
                                            </tr>
                                        <?php $rank++; endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Top 5 Tenants by Payment -->
            <?php if (!empty($topData['top_tenants'])): ?>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-people"></i> Top 5 Tenants by Payment</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Rank</th>
                                            <th>Tenant</th>
                                            <th class="text-end">Total Paid</th>
                                            <th class="text-end">Payments</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $rank = 1; foreach ($topData['top_tenants'] as $tenantData): ?>
                                            <tr>
                                                <td>
                                                    <?php if ($rank == 1): ?>
                                                        <span class="badge bg-warning text-dark">🥇</span>
                                                    <?php elseif ($rank == 2): ?>
                                                        <span class="badge bg-secondary">🥈</span>
                                                    <?php elseif ($rank == 3): ?>
                                                        <span class="badge bg-danger">🥉</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-light text-dark"><?= $rank ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $tenantName = 'Tenant #' . ($tenantData->tenant_id ?? 'N/A');
                                                    if (isset($tenantData->tenant) && isset($tenantData->tenant->user)) {
                                                        $firstName = $tenantData->tenant->user->first_name ?? '';
                                                        $lastName = $tenantData->tenant->user->last_name ?? '';
                                                        $tenantName = trim($firstName . ' ' . $lastName) ?: $tenantName;
                                                    }
                                                    echo h($tenantName);
                                                    ?>
                                                </td>
                                                <td class="text-end"><strong><?= number_format($tenantData->total_paid ?? 0, 2) ?></strong></td>
                                                <td class="text-end"><?= $tenantData->payment_count ?? 0 ?></td>
                                            </tr>
                                        <?php $rank++; endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Top 5 Properties by Units -->
            <?php if (!empty($topData['top_properties_by_units'])): ?>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-building"></i> Top 5 Properties by Units</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Rank</th>
                                            <th>Property</th>
                                            <th class="text-end">Units</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $rank = 1; foreach ($topData['top_properties_by_units'] as $property): ?>
                                            <tr>
                                                <td>
                                                    <?php if ($rank == 1): ?>
                                                        <span class="badge bg-warning text-dark">🥇</span>
                                                    <?php elseif ($rank == 2): ?>
                                                        <span class="badge bg-secondary">🥈</span>
                                                    <?php elseif ($rank == 3): ?>
                                                        <span class="badge bg-danger">🥉</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-light text-dark"><?= $rank ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= h($property->name ?? 'N/A') ?></td>
                                                <td class="text-end"><strong><?= $property->unit_count ?? 0 ?></strong></td>
                                            </tr>
                                        <?php $rank++; endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Recent Payments -->
            <?php if (!empty($topData['recent_payments'])): ?>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Payments</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Tenant</th>
                                            <th>Property</th>
                                            <th class="text-end">Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($topData['recent_payments'] as $payment): ?>
                                            <tr>
                                                <td>
                                                    <?php
                                                    $tenantName = 'Tenant #' . ($payment->tenant_id ?? 'N/A');
                                                    if (isset($payment->tenant) && isset($payment->tenant->user)) {
                                                        $firstName = $payment->tenant->user->first_name ?? '';
                                                        $lastName = $payment->tenant->user->last_name ?? '';
                                                        $tenantName = trim($firstName . ' ' . $lastName) ?: $tenantName;
                                                    }
                                                    echo h($tenantName);
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $propertyName = 'N/A';
                                                    if (isset($payment->contract) && isset($payment->contract->unit) && isset($payment->contract->unit->property)) {
                                                        $propertyName = $payment->contract->unit->property->name ?? 'N/A';
                                                    }
                                                    echo h($propertyName);
                                                    ?>
                                                </td>
                                                <td class="text-end"><strong><?= number_format($payment->amount ?? 0, 2) ?></strong></td>
                                                <td>
                                                    <span class="badge bg-<?= $payment->payment_status === 'verified' ? 'success' : ($payment->payment_status === 'pending' ? 'warning' : 'danger') ?>">
                                                        <?= h(ucfirst($payment->payment_status ?? 'N/A')) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <script>
        // Monthly Revenue Chart
        <?php if (!empty($chartData['monthly_revenue'])): ?>
        const revenueCtx = document.getElementById('revenueChart');
        if (revenueCtx) {
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: <?= json_encode($chartData['monthly_revenue']['months']) ?>,
                    datasets: [{
                        label: 'Revenue',
                        data: <?= json_encode($chartData['monthly_revenue']['revenues']) ?>,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }
        <?php endif; ?>

        // Payment Status Chart
        <?php if (!empty($chartData['payment_status'])): ?>
        const paymentStatusCtx = document.getElementById('paymentStatusChart');
        if (paymentStatusCtx) {
            new Chart(paymentStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Pending', 'Verified', 'Rejected'],
                    datasets: [{
                        data: [
                            <?= $chartData['payment_status']['pending'] ?? 0 ?>,
                            <?= $chartData['payment_status']['verified'] ?? 0 ?>,
                            <?= $chartData['payment_status']['rejected'] ?? 0 ?>
                        ],
                        backgroundColor: [
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(255, 99, 132, 0.8)'
                        ],
                        borderColor: [
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 99, 132, 1)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
        <?php endif; ?>

        // Contract Status Chart
        <?php if (!empty($chartData['contract_status'])): ?>
        const contractStatusCtx = document.getElementById('contractStatusChart');
        if (contractStatusCtx) {
            new Chart(contractStatusCtx, {
                type: 'bar',
                data: {
                    labels: ['Active', 'Pending Signature', 'Expired', 'Terminated'],
                    datasets: [{
                        label: 'Contracts',
                        data: [
                            <?= $chartData['contract_status']['active'] ?? 0 ?>,
                            <?= $chartData['contract_status']['pending_signature'] ?? 0 ?>,
                            <?= $chartData['contract_status']['expired'] ?? 0 ?>,
                            <?= $chartData['contract_status']['terminated'] ?? 0 ?>
                        ],
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(153, 102, 255, 0.8)'
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(255, 99, 132, 1)',
                            'rgba(153, 102, 255, 1)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }
        <?php endif; ?>
    </script>

<?php elseif ($user->role === 'tenant'): ?>
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Active Contracts</h5>
                    <h2><?= $data['active_contracts'] ?? 0 ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Pending Payments</h5>
                    <h2><?= $data['pending_payments'] ?? 0 ?></h2>
                </div>
            </div>
        </div>
    </div>
    
    <?php if (!empty($data['payment_history'])): ?>
        <div class="row mt-4">
            <div class="col-12">
                <h3>Recent Payments</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['payment_history'] as $payment): ?>
                            <tr>
                                <td><?= h($payment->currency . ' ' . number_format($payment->amount, 2)) ?></td>
                                <td><span class="badge bg-<?= $payment->payment_status === 'verified' ? 'success' : 'warning' ?>"><?= h(ucfirst($payment->payment_status)) ?></span></td>
                                <td><?= $payment->created ? h($payment->created->format('Y-m-d')) : ($payment->paid_at ? h($payment->paid_at->format('Y-m-d')) : 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>
