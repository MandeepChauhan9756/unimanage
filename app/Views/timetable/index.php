<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-0">Class Timetable</h3>
        <p class="text-muted fs-7 mb-0">Weekly schedule of lectures, classes, and rooms.</p>
    </div>
    <?php if (session()->get('role') === 'admin'): ?>
        <a href="<?= base_url('timetable/create') ?>" class="btn btn-primary">
            <i class="bi bi-calendar-plus me-1"></i> Add Timetable Slot
        </a>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light fs-7 text-uppercase">
                <tr>
                    <th class="ps-3">Day</th>
                    <th>Time</th>
                    <th>Subject</th>
                    <th>Classroom</th>
                    <th>Faculty</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($timetables)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No timetable entries scheduled.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($timetables as $t): ?>
                        <tr>
                            <td class="ps-3 fw-semibold"><?= esc($t['day_of_week']) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= date('h:i A', strtotime($t['start_time'])) ?> - <?= date('h:i A', strtotime($t['end_time'])) ?></span></td>
                            <td class="fw-medium"><?= esc($t['subject_name']) ?></td>
                            <td><?= esc($t['classroom']) ?></td>
                            <td><?= esc($t['faculty_name']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>