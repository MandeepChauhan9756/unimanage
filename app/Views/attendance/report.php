<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <a href="<?= base_url('attendance') ?>" class="text-muted text-decoration-none fs-7"><i class="bi bi-arrow-left me-1"></i> Attendance records</a>
    <div class="text-uppercase text-primary fw-bold fs-7 mt-3 mb-2">Academic insights</div>
    <h1 class="page-title mb-1">Attendance report</h1>
    <p class="text-muted mb-0">Review attendance by subject for the selected date range.</p>
</div>

<section class="card card-soft mb-4 p-3 p-lg-4">
    <form method="get" action="<?= base_url('attendance/report') ?>" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label fw-semibold" for="subject_id">Subject</label>
            <select class="form-select" id="subject_id" name="subject_id">
                <option value="">All subjects</option>
                <?php foreach ($subjects as $subject): ?>
                    <option value="<?= esc($subject['id']) ?>" <?= $filters['subject_id'] === (string) $subject['id'] ? 'selected' : '' ?>><?= esc($subject['code'] . ' · ' . $subject['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold" for="date_from">From</label>
            <input class="form-control" type="date" id="date_from" name="date_from" value="<?= esc($filters['date_from']) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold" for="date_to">To</label>
            <input class="form-control" type="date" id="date_to" name="date_to" value="<?= esc($filters['date_to']) ?>">
        </div>
        <div class="col-md-2 d-grid"><button class="btn btn-primary" type="submit"><i class="bi bi-funnel me-1"></i> Apply</button></div>
    </form>
</section>

<section class="card card-soft">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th class="ps-4">Subject</th><th>Total classes</th><th>Present</th><th>Absent</th><th>Late</th><th class="pe-4">Attendance rate</th></tr></thead>
            <tbody>
            <?php if ($summary === []): ?>
                <tr><td colspan="6" class="text-center text-muted py-5"><i class="bi bi-bar-chart fs-3 d-block mb-2"></i>No attendance records match these filters.</td></tr>
            <?php else: foreach ($summary as $row): ?>
                <?php $rate = (int) $row['total_classes'] > 0 ? (int) round(((int) $row['present_count'] + (int) $row['late_count']) / (int) $row['total_classes'] * 100) : 0; ?>
                <tr>
                    <td class="ps-4"><div class="fw-semibold"><?= esc($row['subject_name']) ?></div><code class="fs-7"><?= esc($row['subject_code']) ?></code></td>
                    <td><?= number_format((int) $row['total_classes']) ?></td>
                    <td><span class="text-success fw-semibold"><?= number_format((int) $row['present_count']) ?></span></td>
                    <td><span class="text-danger fw-semibold"><?= number_format((int) $row['absent_count']) ?></span></td>
                    <td><span class="text-warning-emphasis fw-semibold"><?= number_format((int) $row['late_count']) ?></span></td>
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
<?= $this->endSection() ?>
