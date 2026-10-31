<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <div class="text-uppercase text-primary fw-bold fs-7 mb-2">Student portal</div>
    <h1 class="page-title mb-1">My attendance</h1>
    <p class="text-muted mb-0">Your attendance records and subject-wise progress. Only your own records are shown here.</p>
</div>

<section class="card card-soft p-3 p-lg-4 mb-4">
    <form method="get" action="<?= base_url('attendance/my-attendance') ?>" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label fw-semibold" for="subject_id">Subject</label>
            <select class="form-select" name="subject_id" id="subject_id">
                <option value="">All subjects</option>
                <?php foreach ($subjects as $subject): ?>
                    <option value="<?= esc($subject['id']) ?>" <?= $filters['subject_id'] === (string) $subject['id'] ? 'selected' : '' ?>><?= esc($subject['code'] . ' · ' . $subject['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold" for="date_from">From</label>
            <input class="form-control" type="date" name="date_from" id="date_from" value="<?= esc($filters['date_from']) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold" for="date_to">To</label>
            <input class="form-control" type="date" name="date_to" id="date_to" value="<?= esc($filters['date_to']) ?>">
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-primary flex-grow-1" type="submit">Filter</button>
            <a class="btn btn-light" href="<?= base_url('attendance/my-attendance') ?>">Clear</a>
        </div>
    </form>
</section>

<div class="row g-3 mb-4">
    <?php foreach ([
        ['Attendance rate', $stats['attendance_rate'] . '%', 'primary'],
        ['Classes attended', $stats['present_count'] + $stats['late_count'], 'success'],
        ['Absent', $stats['absent_count'], 'danger'],
        ['Total class records', $stats['total_classes'], 'secondary'],
    ] as [$label, $value, $color]): ?>
        <div class="col-6 col-xl-3">
            <section class="card card-soft h-100 p-3 p-lg-4">
                <div class="text-muted fs-7 mb-2"><?= esc($label) ?></div>
                <div class="h4 fw-bold text-<?= esc($color) ?> mb-0"><?= esc((string) $value) ?></div>
            </section>
        </div>
    <?php endforeach; ?>
</div>

<section class="card card-soft mb-4">
    <div class="p-3 p-lg-4 border-bottom">
        <h2 class="h5 fw-bold mb-1">Subject-wise attendance</h2>
        <p class="text-muted fs-7 mb-0">Late is counted as attended; excused sessions are included in total sessions.</p>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th class="ps-4">Subject</th><th>Total sessions</th><th>Present</th><th>Late</th><th>Absent</th><th>Excused</th><th class="pe-4">Attendance rate</th></tr></thead>
            <tbody>
            <?php if ($summary === []): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">No attendance summary is available yet.</td></tr>
            <?php else: foreach ($summary as $row): ?>
                <?php $rate = (int) $row['total_classes'] > 0 ? (int) round(((int) $row['present_count'] + (int) $row['late_count']) / (int) $row['total_classes'] * 100) : 0; ?>
                <tr>
                    <td class="ps-4"><div class="fw-semibold"><?= esc($row['subject_name']) ?></div><code class="fs-7"><?= esc($row['subject_code']) ?></code></td>
                    <td><?= number_format((int) $row['total_classes']) ?></td>
                    <td class="text-success fw-semibold"><?= number_format((int) $row['present_count']) ?></td>
                    <td class="text-warning-emphasis fw-semibold"><?= number_format((int) $row['late_count']) ?></td>
                    <td class="text-danger fw-semibold"><?= number_format((int) $row['absent_count']) ?></td>
                    <td class="text-muted fw-semibold"><?= number_format((int) $row['excused_count']) ?></td>
                    <td class="pe-4" style="min-width:170px">
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" role="progressbar" aria-label="Attendance rate" aria-valuenow="<?= $rate ?>" aria-valuemin="0" aria-valuemax="100" style="height:7px">
                                <div class="progress-bar <?= $rate >= 75 ? 'bg-success' : 'bg-warning' ?>" style="width:<?= $rate ?>%"></div>
                            </div>
                            <span class="fw-semibold fs-7"><?= $rate ?>%</span>
                        </div>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="card card-soft">
    <div class="p-3 p-lg-4 border-bottom"><h2 class="h5 fw-bold mb-0">Attendance history</h2></div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th class="ps-4">Date</th><th>Subject</th><th>Academic year</th><th class="pe-4">Status</th></tr></thead>
            <tbody>
            <?php if ($records === []): ?>
                <tr><td colspan="4" class="text-center text-muted py-4">No attendance records match these filters.</td></tr>
            <?php else: foreach ($records as $record): ?>
                <?php $statusColors = ['Present' => 'success', 'Absent' => 'danger', 'Late' => 'warning', 'Excused' => 'secondary']; $statusColor = $statusColors[$record['status']] ?? 'secondary'; ?>
                <tr>
                    <td class="ps-4 fw-medium"><?= esc(date('M j, Y', strtotime($record['date']))) ?></td>
                    <td><div class="fw-semibold"><?= esc($record['subject_name']) ?></div><code class="fs-7"><?= esc($record['subject_code']) ?></code></td>
                    <td><?= esc($record['year_label']) ?></td>
                    <td class="pe-4"><span class="badge bg-<?= esc($statusColor) ?>-subtle text-<?= esc($statusColor) ?> border border-<?= esc($statusColor) ?>-subtle rounded-pill"><?= esc($record['status']) ?></span></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?= $this->endSection() ?>
