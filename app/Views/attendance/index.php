<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-0">Attendance Logs</h3>
        <p class="text-muted fs-7 mb-0">Review student class attendance and participation.</p>
    </div>
    <?php if (in_array(session()->get('role'), ['admin', 'faculty'])): ?>
        <div class="d-flex gap-2">
            <a href="<?= base_url('attendance/report') ?>" class="btn btn-outline-secondary"><i class="bi bi-bar-chart me-1"></i> View report</a>
            <a href="<?= base_url('attendance/mark-student') ?>" class="btn btn-outline-primary"><i class="bi bi-person-check me-1"></i> One student</a>
            <a href="<?= base_url('attendance/mark') ?>" class="btn btn-primary"><i class="bi bi-check2-square me-1"></i> Mark class</a>
        </div>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light fs-7 text-uppercase">
                <tr>
                    <th class="ps-3">Date</th>
                    <th>Student Name</th>
                    <th>Enrollment No</th>
                    <th>Subject</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($attendance)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No attendance records found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($attendance as $row): ?>
                        <tr>
                            <td class="ps-3 fw-medium"><?= esc($row['date']) ?></td>
                            <td><?= esc($row['student_name']) ?></td>
                            <td><code><?= esc($row['enrollment_no']) ?></code></td>
                            <td><?= esc($row['subject_name']) ?></td>
                            <td>
                                <?php if ($row['status'] === 'Present'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">Present</span>
                                <?php elseif ($row['status'] === 'Absent'): ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill">Absent</span>
                                <?php elseif ($row['status'] === 'Late'): ?>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 rounded-pill">Late</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-1 rounded-pill">Excused</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($pager): ?>
        <div class="card-footer bg-white py-3 d-flex justify-content-end">
            <?= $pager->links('default', 'bootstrap_pagination') ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>