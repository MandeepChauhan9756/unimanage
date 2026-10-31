<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-0">Faculty Directory</h3>
        <p class="text-muted fs-7 mb-0">Manage university professors and academic staff.</p>
    </div>
    <?php if (session()->get('role') === 'admin'): ?>
        <div>
            <a href="<?= base_url('faculty/assign-subject') ?>" class="btn btn-outline-primary me-2">
                <i class="bi bi-journal-plus me-1"></i> Assign Subject
            </a>
            <a href="<?= base_url('faculty/create') ?>" class="btn btn-primary">
                <i class="bi bi-person-plus me-1"></i> Add Faculty Member
            </a>
        </div>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light fs-7 text-uppercase">
                <tr>
                    <th class="ps-3">Faculty Member</th>
                    <th>Employee ID</th>
                    <th>Department</th>
                    <th>Designation</th>
                    <th>Joining Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($faculty as $f): ?>
                    <tr>
                        <td class="ps-3">
                            <span class="fw-semibold text-dark d-block"><?= esc($f['full_name']) ?></span>
                            <small class="text-muted"><?= esc($f['email']) ?></small>
                        </td>
                        <td><code><?= esc($f['employee_id']) ?></code></td>
                        <td><?= esc($f['department_name']) ?></td>
                        <td><?= esc($f['designation']) ?></td>
                        <td><?= esc($f['joining_date']) ?></td>
                        <td>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">Active</span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>