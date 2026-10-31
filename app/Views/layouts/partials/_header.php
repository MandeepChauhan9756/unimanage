<nav class="navbar navbar-expand-lg navbar-light app-header border-bottom px-3 px-md-4 py-2 sticky-top">
    <div class="d-flex align-items-center w-100">
        <button class="btn btn-light d-md-none me-3" type="button" id="sidebarToggle" aria-label="Open navigation">
            <i class="bi bi-list fs-4"></i>
        </button>
        <div class="d-none d-sm-block">
            <div class="fw-semibold">Admissions CRM</div>
            <div class="text-muted fs-7">University counseling workspace</div>
        </div>

        <div class="ms-auto d-flex align-items-center">
            <span class="badge bg-light text-secondary border me-3 px-3 py-2 rounded-pill text-uppercase">
                <?= esc(session()->get('role') === 'faculty' ? 'Counselor' : session()->get('role')) ?>
            </span>

            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="avatar-sm me-2"><?= esc(strtoupper(substr((string) session()->get('name'), 0, 1))) ?></span>
                    <span class="fw-medium d-none d-sm-inline"><?= esc(session()->get('name')) ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="dropdownUser">
                    <li class="px-3 py-2">
                        <div class="fw-semibold"><?= esc(session()->get('name')) ?></div>
                        <small class="text-muted"><?= esc(session()->get('email')) ?></small>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="<?= base_url('logout') ?>" method="post">
                            <?= csrf_field() ?>
                            <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Sign out</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>