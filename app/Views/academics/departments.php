<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-0">Department Management</h3>
        <p class="text-muted fs-7 mb-0">Manage university academic departments.</p>
    </div>
    <?php if (session()->get('role') === 'admin'): ?>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDeptModal">
            <i class="bi bi-plus-lg me-1"></i> Add Department
        </button>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light fs-7 text-uppercase">
                <tr>
                    <th class="ps-3">Code</th>
                    <th>Department Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($departments as $dept): ?>
                    <tr>
                        <td class="ps-3"><code><?= esc($dept['code']) ?></code></td>
                        <td class="fw-semibold"><?= esc($dept['name']) ?></td>
                        <td>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">Active</span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addDeptModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= base_url('academics/departments/store') ?>" method="post">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add New Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Department Code (e.g. CS, ME)</label>
                        <input type="text" name="code" class="form-control" required placeholder="CS">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Department Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="Computer Science & Engineering">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-semibold">Save Department</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>