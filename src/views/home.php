<div class="row">
    <div class="col-md-12">
        <div class="card mb-4 osrs-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-shield-check me-2"></i>OSRS Server Status</h5>
                <small class="text-muted" id="last-updated">Last checked: <?= $status['last_checked'] ?></small>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center">
                            <div class="status-indicator <?= strtolower($status['current_status']) === 'online' ? 'online' : (strtolower($status['current_status']) === 'error' ? 'error' : 'maintenance') ?>"></div>
                            <h2 class="status-badge ms-3 <?= strtolower($status['current_status']) === 'online' ? 'text-success' : (strtolower($status['current_status']) === 'error' ? 'text-danger' : 'text-warning') ?>">
                                <?= $status['current_status'] ?>
                            </h2>
                        </div>
                        <p class="mt-3">The Old School RuneScape servers are currently <strong><?= strtolower($status['current_status']) ?></strong>.</p>
                        <p class="text-muted">Next automatic check in <span id="next-check-countdown" class="fw-bold">...</span></p>
                        <p class="small text-muted mt-2"><i class="bi bi-info-circle me-1"></i> Status information is sourced from the official Jagex status page which is updated manually by Jagex employees.</p>

                        <?php if (isset($status['error'])): ?>
                            <div class="alert alert-danger mt-3">
                                <strong>Error Details:</strong> <?= $status['error'] ?>
                                <?php if (isset($status['error_details'])): ?>
                                    <button class="btn btn-sm btn-outline-danger mt-2" type="button" data-bs-toggle="collapse" data-bs-target="#errorDetails">
                                        <i class="bi bi-code-slash me-1"></i>Show Technical Details
                                    </button>
                                    <div class="collapse mt-2" id="errorDetails">
                                        <div class="card card-body bg-light">
                                            <pre class="mb-0"><?= htmlspecialchars(json_encode($status['error_details'], JSON_PRETTY_PRINT)) ?></pre>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="d-flex flex-column">
                            <button id="force-refresh" class="btn btn-primary mb-2">
                                <i class="bi bi-arrow-clockwise me-2"></i>Check Again Now
                            </button>
                            <a href="<?= $source_url ?>" target="_blank" class="btn btn-outline-secondary">
                                <i class="bi bi-link-45deg me-2"></i>View Jagex's Game Status Page
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($status['maintenance_data'])): ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 osrs-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-tools me-2"></i>Maintenance Information</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-dark">
                            <tr>
                                <th>Date</th>
                                <th>Issue</th>
                                <th>Downtime</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($status['maintenance_data'] as $maintenance): ?>
                                <tr>
                                    <td class="fw-bold"><?= $maintenance['date'] ?></td>
                                    <td><?= $maintenance['issue'] ?></td>
                                    <td><?= $maintenance['downtime'] ?></td>
                                    <td>
                                        <span class="badge rounded-pill <?= strtolower($maintenance['status']) === 'complete' ? 'bg-success' : 'bg-warning' ?>">
                                            <?= $maintenance['status'] ?>
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
    </div>
<?php elseif (strtolower($status['current_status']) !== 'error'): ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 osrs-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Server Information</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle-fill me-2"></i>No maintenance or downtime is currently scheduled for Old School RuneScape.
                    </div>
                    <p class="text-muted">Regular weekly game updates typically occur on Wednesdays at:</p>
                    <ul>
                        <li><strong>10:45 UTC</strong>: 45-minute Shutdown Timer begins</li>
                        <li><strong>11:30 UTC</strong>: Game goes offline</li>
                        <li><strong>~12:00 UTC</strong>: Servers come back online</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-12">
        <div class="card osrs-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Downtime History</h5>
                <button class="btn btn-link p-0 text-decoration-none toggle-history" type="button" data-bs-toggle="collapse" data-bs-target="#historicalStatusContent" aria-expanded="false" aria-controls="historicalStatusContent">
                    <i class="bi bi-chevron-down"></i>
                </button>
            </div>
            <div class="collapse" id="historicalStatusContent">
                <div class="card-body">
                    <?php if (empty($history)): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle-fill me-2"></i>No downtime has been recorded since monitoring began.
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-3">
                            <i class="bi bi-info-circle me-1"></i> This table only shows instances where the servers were not in the "Online" state.
                        </p>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                <tr>
                                    <th>Time (UTC)</th>
                                    <th>Status</th>
                                    <th>Duration</th>
                                    <th>Maintenance</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $previousStatus = null;
                                $previousTime = null;

                                foreach ($history as $index => $item):
                                    // Calculate duration if possible (only for downtime items)
                                    $duration = '';
                                    if (isset($history[$index + 1])) {
                                        $currentTime = new DateTime($item['checked_at']);
                                        $nextTime = new DateTime($history[$index + 1]['checked_at']);
                                        $interval = $currentTime->diff($nextTime);

                                        // Format the duration
                                        if ($interval->days > 0) {
                                            $duration = $interval->format('%d days, %h hours');
                                        } elseif ($interval->h > 0) {
                                            $duration = $interval->format('%h hours, %i minutes');
                                        } else {
                                            $duration = $interval->format('%i minutes');
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td class="text-nowrap"><?= $item['checked_at'] ?></td>
                                        <td>
                                            <span class="status-pill <?= strtolower($item['current_status']) === 'online' ? 'online' : (strtolower($item['current_status']) === 'error' ? 'error' : 'maintenance') ?>">
                                                <?= $item['current_status'] ?>
                                            </span>
                                        </td>
                                        <td><?= $duration ?: 'Unknown' ?></td>
                                        <td>
                                            <?php if (!empty($item['maintenance_data'])): ?>
                                                <button class="btn btn-sm btn-outline-primary show-details"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#historyModal"
                                                        data-history-id="<?= $item['id'] ?>">
                                                    <i class="bi bi-eye me-1"></i>View Details
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted">None reported</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for history details -->
<div class="modal fade" id="historyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="bi bi-info-circle me-2"></i>Maintenance Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="historyModalBody">
                <!-- Content will be dynamically loaded -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Store the history data for the modal
    const historyData = <?= json_encode($history) ?>;

    // Toggle button icon update
    document.querySelector('.toggle-history').addEventListener('click', function() {
        const icon = this.querySelector('i');
        if (icon.classList.contains('bi-chevron-down')) {
            icon.classList.replace('bi-chevron-down', 'bi-chevron-up');
        } else {
            icon.classList.replace('bi-chevron-up', 'bi-chevron-down');
        }
    });

    // Modal details
    document.querySelectorAll('.show-details').forEach(button => {
        button.addEventListener('click', function() {
            const historyId = this.getAttribute('data-history-id');
            const historyItem = historyData.find(item => item.id == historyId);

            if (historyItem && historyItem.maintenance_data && historyItem.maintenance_data.length > 0) {
                let tableContent = `
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Date</th>
                            <th>Issue</th>
                            <th>Downtime</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>`;

                historyItem.maintenance_data.forEach(item => {
                    tableContent += `
                    <tr>
                        <td class="fw-bold">${item.date}</td>
                        <td>${item.issue}</td>
                        <td>${item.downtime}</td>
                        <td>
                            <span class="badge rounded-pill ${item.status.toLowerCase() === 'complete' ? 'bg-success' : 'bg-warning'}">
                                ${item.status}
                            </span>
                        </td>
                    </tr>`;
                });

                tableContent += `
                    </tbody>
                </table>`;

                document.getElementById('historyModalBody').innerHTML = tableContent;
            } else {
                document.getElementById('historyModalBody').innerHTML = '<div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>No maintenance data available for this entry.</div>';
            }
        });
    });
</script>