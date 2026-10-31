<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <div class="text-uppercase text-primary fw-bold fs-7 mb-2">Student recruitment</div>
        <h1 class="page-title mb-1">Admissions leads</h1>
        <p class="text-muted mb-0">Organize inquiries, follow-ups and applications in one simple pipeline.</p>
    </div>
    <a href="<?= base_url('leads/create') ?>" class="btn btn-primary px-3 py-2"><i class="bi bi-plus-lg me-1"></i> Add lead</a>
</div>

<div class="row g-3 mb-4">
    <?php
    $stageCards = [
        ['All students', '', 'bi-people'],
        ['New inquiry', 'new', 'bi-stars'],
        ['In counseling', 'counseling', 'bi-chat-dots'],
        ['Application', 'application', 'bi-file-earmark-text'],
        ['Won / enrolled', 'enrolled', 'bi-mortarboard'],
    ];
    foreach ($stageCards as [$label, $value, $icon]): ?>
        <div class="col-6 col-lg">
            <a href="<?= base_url('leads') . ($value !== '' ? '?status=' . rawurlencode($value) : '') ?>" class="card card-soft h-100 p-3 text-decoration-none text-dark">
                <div class="d-flex justify-content-between align-items-center">
                    <div><div class="text-muted fs-7 mb-1"><?= esc($label) ?></div><div class="h4 fw-bold mb-0"><?= number_format($value === '' ? $metrics['total'] : ($pipeline[$value] ?? 0)) ?></div></div>
                    <span class="bg-primary-subtle text-primary rounded-3 p-2"><i class="bi <?= esc($icon) ?>"></i></span>
                </div>
                <span class="text-muted fs-7 mt-2"><?= $value !== '' ? 'View stage' : 'Across all stages' ?> <i class="bi bi-arrow-right ms-1"></i></span>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<section class="card card-soft">
    <div class="p-3 p-lg-4 border-bottom">
        <form method="get" action="<?= base_url('leads') ?>" class="row g-2 align-items-center">
            <div class="col-lg-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="search" name="search" class="form-control border-start-0 ps-0" placeholder="Search students, phone, email or program..." value="<?= esc($filters['search']) ?>">
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <select name="status" class="form-select" aria-label="Filter by stage">
                    <option value="">All stages</option>
                    <?php foreach ($statuses as $status): ?>
                        <option value="<?= esc($status) ?>" <?= $filters['status'] === $status ? 'selected' : '' ?>><?= $status === 'enrolled' ? 'Won / Enrolled' : esc(ucfirst($status)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-lg-2">
                <select name="lead_source" class="form-select" aria-label="Filter by source">
                    <option value="">All sources</option>
                    <?php foreach ($sources as $source): ?>
                        <option value="<?= esc($source) ?>" <?= $filters['lead_source'] === $source ? 'selected' : '' ?>><?= esc(ucwords(str_replace('_', ' ', $source))) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-lg-2">
                <select name="assigned_to" class="form-select" aria-label="Filter by counselor">
                    <option value="">All counselors</option>
                    <?php foreach ($counselors as $counselor): ?>
                        <option value="<?= esc($counselor['id']) ?>" <?= $filters['assigned_to'] === (string) $counselor['id'] ? 'selected' : '' ?>><?= esc($counselor['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-lg-1 d-grid">
                <button class="btn btn-dark" type="submit">Filter</button>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th class="ps-4">Student</th><th>Interested in</th><th>University / intake</th><th>Source</th><th>Counselor</th><th>Next follow-up</th><th>Stage</th><th class="text-end pe-4"> </th></tr></thead>
            <tbody>
            <?php if ($leads === []): ?>
                <tr><td colspan="8" class="text-center py-5">
                    <div class="fs-2 text-primary mb-2"><i class="bi bi-person-lines-fill"></i></div>
                    <div class="fw-semibold"><?= $filters['search'] || $filters['status'] || $filters['lead_source'] || $filters['assigned_to'] ? 'No matching leads' : 'No leads yet' ?></div>
                    <div class="text-muted fs-7 mt-1">Add an inquiry to start supporting students through their university journey.</div>
                    <?php if (! $filters['search'] && ! $filters['status'] && ! $filters['lead_source'] && ! $filters['assigned_to']): ?>
                        <a href="<?= base_url('leads/create') ?>" class="btn btn-primary btn-sm mt-3">Add your first lead</a>
                    <?php endif; ?>
                </td></tr>
            <?php else: foreach ($leads as $lead): ?>
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar-sm"><?= esc(strtoupper(substr($lead['student_name'], 0, 1))) ?></span>
                            <div class="min-w-0">
                                <a class="text-decoration-none fw-semibold text-dark" href="<?= base_url('leads/profile/' . $lead['id']) ?>"><?= esc($lead['student_name']) ?></a>
                                <div class="text-muted fs-7"><?= esc($lead['email'] ?: $lead['phone']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fw-medium"><?= esc($lead['interested_program'] ?: 'Not selected') ?></div>
                        <div class="text-muted fs-7"><?= esc($lead['study_level'] ?: '') ?></div>
                    </td>
                    <td><?= esc($lead['preferred_university'] ?: '—') ?><div class="text-muted fs-7"><?= esc($lead['intake'] ?: '') ?></div></td>
                    <td><?= esc(ucwords(str_replace('_', ' ', $lead['lead_source']))) ?></td>
                    <td><?= esc($lead['counselor_name'] ?: 'Unassigned') ?></td>
                    <td>
                        <?php if ($lead['next_follow_up']): ?>
                            <?php $isOverdue = strtotime($lead['next_follow_up']) < time() && ! in_array($lead['status'], ['enrolled', 'lost'], true); ?>
                            <span class="<?= $isOverdue ? 'text-danger fw-semibold' : 'text-muted' ?> fs-7"><?= esc(date('M j, g:i A', strtotime($lead['next_follow_up']))) ?></span>
                        <?php else: ?>
                            <span class="text-muted fs-7">Not scheduled</span>
                        <?php endif; ?>
                    </td>
                    <td><span class="status-pill status-<?= esc($lead['status']) ?>"><?= $lead['status'] === 'enrolled' ? 'Won / Enrolled' : esc(ucfirst($lead['status'])) ?></span></td>
                    <td class="text-end pe-4"><a class="btn btn-sm btn-light" aria-label="Open <?= esc($lead['student_name']) ?>" href="<?= base_url('leads/profile/' . $lead['id']) ?>"><i class="bi bi-arrow-up-right"></i></a></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($pager): ?>
        <div class="px-3 py-2 border-top d-flex justify-content-end"><?= $pager->links('default', 'bootstrap_pagination') ?></div>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>
