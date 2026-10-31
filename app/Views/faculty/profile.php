<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <a href="<?= base_url('faculty') ?>" class="text-decoration-none text-muted fs-7 mb-2 d-inline-block">
        <i class="bi bi-arrow-left me-1"></i> Back to Faculty List
    </a>
    <h3 class="fw-bold text-dark mb-0">Faculty Profile</h3>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center p-4">
            <div class="mx-auto mb-3 bg-primary text-white d-flex align-items-center justify-content-center fw-bold fs-1 rounded-circle" style="width: 90px; height: 90px;">
                <?= strtoupper(substr($faculty['full_name'], 0, 1)) ?>
            </div>
            <h4 class="fw-bold mb-1"><?= esc($faculty['full_name']) ?></h4>
            <p class="text-muted fs-7 mb-2"><code><?= esc($faculty['employee_id']) ?></code></p>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill mb-3 mx-auto">
                <?= esc($faculty['designation']) ?>
            </span>
            <hr>
            <div class="text-start fs-7">
                <p class="mb-2"><strong>Department:</strong> <?= esc($faculty['department_name']) ?></p>
                <p class="mb-2"><strong>Email:</strong> <?= esc($faculty['email']) ?></p>
                <p class="mb-2"><strong>Phone:</strong> <?= esc($faculty['phone']) ?></p>
                <p class="mb-2"><strong>Joining Date:</strong> <?= esc($faculty['joining_date']) ?></p>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-semibold text-primary mb-3"><i class="bi bi-journal-check me-2"></i>Assigned Subjects</h5>
            <?php if (empty($assignedSubjects)): ?>
                <p class="text-muted fs-7">No subjects currently assigned to this faculty member.</p>
            <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($assignedSubjects as $sub): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <span class="fw-semibold"><?= esc($sub['subject_name']) ?></span>
                                <small class="text-muted d-block">Code: <?= esc($sub['subject_code']) ?></small>
                            </div>
                            <span class="badge bg-light text-dark border"><?= esc($sub['year_label']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>