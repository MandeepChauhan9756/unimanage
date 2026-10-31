<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <div class="text-uppercase text-primary fw-bold fs-7 mb-2">Your admissions workspace</div>
        <h1 class="page-title mb-1">Good <?= esc(date('H') < 12 ? 'morning' : (date('H') < 18 ? 'afternoon' : 'evening')) ?>, <?= esc(explode(' ', trim((string) session()->get('name')))[0]) ?>.</h1>
        <p class="text-muted mb-0">Keep every student conversation moving forward.</p>
    </div>
    <a href="<?= base_url('leads/create') ?>" class="btn btn-primary px-3 py-2">
        <i class="bi bi-plus-lg me-1"></i> Add a lead
    </a>
</div>

<div class="row g-3 mb-4">
    <?php
    $metricCards = [
        ['All leads', $metrics['total'], 'bi-people', 'text-primary', 'bg-primary-subtle'],
        ['New inquiries', $metrics['new'], 'bi-stars', 'text-info', 'bg-info-subtle'],
        ['In counseling', $metrics['counseling'], 'bi-chat-dots', 'text-warning', 'bg-warning-subtle'],
        ['Applications', $metrics['applications'], 'bi-file-earmark-text', 'text-success', 'bg-success-subtle'],
        ['Won / enrolled', $metrics['enrolled'], 'bi-mortarboard', 'text-success', 'bg-success-subtle'],
        ['Follow-ups overdue', $metrics['overdue'], 'bi-alarm', 'text-danger', 'bg-danger-subtle'],
    ];
    foreach ($metricCards as [$label, $value, $icon, $color, $background]): ?>
        <div class="col-6 col-xl-2">
            <div class="card card-soft h-100 p-3 p-lg-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted fs-7"><?= esc($label) ?></span>
                    <span class="<?= esc($background) ?> <?= esc($color) ?> rounded-3 d-inline-flex align-items-center justify-content-center" style="height:34px;width:34px">
                        <i class="bi <?= esc($icon) ?>"></i>
                    </span>
                </div>
                <div class="fs-3 fw-bold"><?= number_format((int) $value) ?></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-4">
    <div class="col-xl-7">
        <section class="card card-soft h-100">
            <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h5 fw-bold mb-1">Admissions pipeline</h2>
                    <p class="text-muted fs-7 mb-0">A clear view of every student's next step.</p>
                </div>
                <a href="<?= base_url('leads') ?>" class="btn btn-light btn-sm">View leads <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
            <div class="p-4">
                <?php
                $pipelineStages = [
                    'new' => ['New inquiry', '#6675ee'],
                    'contacted' => ['Contacted', '#2990cf'],
                    'counseling' => ['Counseling', '#e7a43a'],
                    'application' => ['Application', '#8b66c8'],
                    'enrolled' => ['Won / enrolled', '#39a675'],
                    'lost' => ['Closed / not proceeding', '#a8afbd'],
                ];
                $pipelineMax = max(1, (int) ($metrics['total'] ?? 0));
                foreach ($pipelineStages as $stage => [$label, $color]):
                    $count = (int) ($pipeline[$stage] ?? 0);
                    $percentage = (int) round($count / $pipelineMax * 100);
                ?>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-semibold fs-7"><?= esc($label) ?></span>
                            <span class="text-muted fs-7"><?= number_format($count) ?> <span class="ms-1"><?= $percentage ?>%</span></span>
                        </div>
                        <div class="progress" role="progressbar" aria-label="<?= esc($label) ?>" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100" style="height:8px">
                            <div class="progress-bar rounded-pill" style="width:<?= $percentage ?>%;background:<?= esc($color) ?>"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <div class="col-xl-5">
        <section class="card card-soft h-100">
            <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h5 fw-bold mb-1">Follow-up queue</h2>
                    <p class="text-muted fs-7 mb-0">Your next student conversations.</p>
                </div>
                <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis"><?= count($followUps) ?> scheduled</span>
            </div>
            <?php if ($followUps === []): ?>
                <div class="text-center p-5">
                    <div class="text-primary fs-2 mb-2"><i class="bi bi-calendar2-check"></i></div>
                    <div class="fw-semibold">You're all caught up</div>
                    <div class="text-muted fs-7 mt-1">Schedule a follow-up when you add or update a lead.</div>
                </div>
            <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($followUps as $followUp): ?>
                        <?php $isOverdue = strtotime($followUp['next_follow_up']) < time(); ?>
                        <a href="<?= base_url('leads/profile/' . $followUp['id']) ?>" class="list-group-item list-group-item-action px-4 py-3 border-0 border-bottom">
                            <div class="d-flex align-items-center gap-3">
                                <span class="avatar-sm"><?= esc(strtoupper(substr($followUp['student_name'], 0, 1))) ?></span>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold text-truncate"><?= esc($followUp['student_name']) ?></div>
                                    <div class="text-muted fs-7 text-truncate"><?= esc($followUp['interested_program'] ?: 'Program not selected') ?></div>
                                </div>
                                <div class="text-end">
                                    <div class="<?= $isOverdue ? 'text-danger' : 'text-muted' ?> fs-7 fw-semibold"><?= esc(date('M j', strtotime($followUp['next_follow_up']))) ?></div>
                                    <div class="text-muted fs-7"><?= esc(date('g:i A', strtotime($followUp['next_follow_up']))) ?></div>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <div class="col-12">
        <section class="card card-soft">
            <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h5 fw-bold mb-1">Recently added leads</h2>
                    <p class="text-muted fs-7 mb-0">New student inquiries, all in one place.</p>
                </div>
                <a href="<?= base_url('leads') ?>" class="text-primary text-decoration-none fw-semibold fs-7">Open lead manager <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead><tr><th class="ps-4">Student</th><th>Interested in</th><th>Source</th><th>Counselor</th><th>Stage</th><th class="pe-4">Added</th></tr></thead>
                    <tbody>
                    <?php if ($recentLeads === []): ?>
                        <tr><td colspan="6" class="text-center text-muted py-5">Your pipeline is ready. Add your first student inquiry to get started.</td></tr>
                    <?php else: foreach ($recentLeads as $lead): ?>
                        <tr>
                            <td class="ps-4">
                                <a class="text-decoration-none fw-semibold text-dark" href="<?= base_url('leads/profile/' . $lead['id']) ?>"><?= esc($lead['student_name']) ?></a>
                                <div class="text-muted fs-7"><?= esc($lead['phone']) ?></div>
                            </td>
                            <td><?= esc($lead['interested_program'] ?: '—') ?></td>
                            <td><?= esc(ucwords(str_replace('_', ' ', $lead['lead_source']))) ?></td>
                            <td><?= esc($lead['counselor_name'] ?: 'Unassigned') ?></td>
                            <td><span class="status-pill status-<?= esc($lead['status']) ?>"><?= $lead['status'] === 'enrolled' ? 'Won / Enrolled' : esc(ucfirst($lead['status'])) ?></span></td>
                            <td class="pe-4 text-muted fs-7"><?= esc(date('M j, Y', strtotime($lead['created_at']))) ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
<?= $this->endSection() ?>
