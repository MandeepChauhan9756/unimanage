<?php $role = session()->get('role'); ?>
<aside class="sidebar text-white d-flex flex-column flex-shrink-0 p-3">
    <a href="<?= base_url('dashboard') ?>" class="d-flex align-items-center gap-2 px-2 mb-4 text-white text-decoration-none">
        <span class="brand-mark"><i class="bi bi-mortarboard-fill"></i></span>
        <span class="fw-bold fs-5">UniManage</span>
    </a>

    <div class="nav-section text-uppercase fw-bold px-3 mb-2">Workspace</div>
    <ul class="nav flex-column mb-4">
        <?php if (in_array($role, ['admin', 'faculty'], true)): ?>
            <li class="nav-item">
                <a href="<?= base_url('dashboard') ?>" class="nav-link <?= url_is('dashboard') ? 'active' : '' ?>">
                    <i class="bi bi-grid-1x2-fill me-2"></i> Overview
                </a>
            </li>
            <li>
                <a href="<?= base_url('leads') ?>" class="nav-link <?= url_is('leads') || (url_is('leads/*') && ! url_is('leads/create')) ? 'active' : '' ?>">
                    <i class="bi bi-funnel me-2"></i> Admissions leads
                </a>
            </li>
            <li>
                <a href="<?= base_url('leads/create') ?>" class="nav-link <?= url_is('leads/create') ? 'active' : '' ?>">
                    <i class="bi bi-person-plus me-2"></i> Add a lead
                </a>
            </li>
        <?php endif; ?>
    </ul>

    <?php if (in_array($role, ['admin', 'faculty', 'accountant'], true)): ?>
        <div class="nav-section text-uppercase fw-bold px-3 mb-2">University records</div>
        <ul class="nav flex-column mb-4">
            <li>
                <a href="<?= base_url('students') ?>" class="nav-link <?= url_is('students*') ? 'active' : '' ?>">
                    <i class="bi bi-mortarboard me-2"></i> Enrolled students
                </a>
            </li>
            <?php if (in_array($role, ['admin', 'accountant'], true)): ?>
                <li>
                    <a href="<?= base_url('fees') ?>" class="nav-link <?= url_is('fees*') ? 'active' : '' ?>">
                        <i class="bi bi-receipt me-2"></i> Fees & payments
                    </a>
                </li>
            <?php endif; ?>
            <?php if (in_array($role, ['admin', 'faculty'], true)): ?>
                <li>
                    <a href="<?= base_url('academics/departments') ?>" class="nav-link <?= url_is('academics/departments') ? 'active' : '' ?>">
                        <i class="bi bi-building me-2"></i> Departments
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('academics/courses') ?>" class="nav-link <?= url_is('academics/courses') ? 'active' : '' ?>">
                        <i class="bi bi-journal-text me-2"></i> Courses
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('academics/subjects') ?>" class="nav-link <?= url_is('academics/subjects') ? 'active' : '' ?>">
                        <i class="bi bi-book me-2"></i> Subjects
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('faculty') ?>" class="nav-link <?= url_is('faculty*') ? 'active' : '' ?>">
                        <i class="bi bi-person-badge me-2"></i> Team
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('attendance') ?>" class="nav-link <?= url_is('attendance*') ? 'active' : '' ?>">
                        <i class="bi bi-calendar-check me-2"></i> Attendance
                    </a>
                </li>
                <!-- <li>
                    <a href="<?= base_url('timetable') ?>" class="nav-link <?= url_is('timetable*') ? 'active' : '' ?>">
                        <i class="bi bi-clock-history me-2"></i> Timetable
                    </a>
                </li> -->
            <?php endif; ?>
        </ul>
    <?php endif; ?>

    <?php if ($role === 'student'): ?>
        <div class="nav-section text-uppercase fw-bold px-3 mb-2">Student portal</div>
        <ul class="nav flex-column mb-4">
            <li>
                <a href="<?= base_url('dashboard') ?>" class="nav-link <?= url_is('dashboard') ? 'active' : '' ?>">
                    <i class="bi bi-grid-1x2-fill me-2"></i> Student dashboard
                </a>
            </li>
            <li>
                <a href="<?= base_url('students/my-profile') ?>" class="nav-link <?= url_is('students/my-profile') || url_is('students/profile/*') ? 'active' : '' ?>">
                    <i class="bi bi-person-vcard me-2"></i> My student profile
                </a>
            </li>
            <li>
                <a href="<?= base_url('fees/my-fees') ?>" class="nav-link <?= url_is('fees*') ? 'active' : '' ?>">
                    <i class="bi bi-receipt me-2"></i> My fees
                </a>
            </li>
            <li>
                <a href="<?= base_url('attendance/my-attendance') ?>" class="nav-link <?= url_is('attendance/my-attendance') ? 'active' : '' ?>">
                    <i class="bi bi-calendar-check me-2"></i> My attendance
                </a>
            </li>
        </ul>
    <?php endif; ?>

    <div class="mt-auto">
        <div class="nav-section text-uppercase fw-bold px-3 mb-2">Account</div>
        <ul class="nav flex-column">
            <li>
                <form action="<?= base_url('logout') ?>" method="post">
                    <?= csrf_field() ?>
                    <button type="submit" class="nav-link border-0 bg-transparent text-start w-100">
                        <i class="bi bi-box-arrow-right me-2"></i> Sign out
                    </button>
                </form>
            </li>
        </ul>
    </div>
</aside>