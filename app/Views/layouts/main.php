<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#111a36">
    <title><?= esc($title ?? 'Dashboard') ?> - UniManage Admissions</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --ink: #17213b;
            --muted: #78839a;
            --navy: #111a36;
            --navy-soft: #1d294a;
            --accent: #5667e8;
            --canvas: #f5f7fb;
            --line: #e9edf4;
        }

        body {
            background: var(--canvas);
            color: var(--ink);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .sidebar {
            background: var(--navy);
            min-height: 100vh;
            width: 252px;
            z-index: 1040;
        }

        .sidebar .nav-link {
            border-radius: 10px;
            color: #aab4ce;
            font-size: .91rem;
            margin: 3px 0;
            padding: 10px 13px;
            transition: background .18s ease, color .18s ease;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: var(--navy-soft);
            color: #fff;
        }

        .sidebar .nav-link.active {
            box-shadow: inset 3px 0 #8290ff;
        }

        .sidebar .nav-link i {
            font-size: 1.05rem;
            width: 23px;
        }

        .brand-mark {
            align-items: center;
            background: linear-gradient(135deg, #7784ff, #4e5bdd);
            border-radius: 11px;
            color: #fff;
            display: inline-flex;
            height: 38px;
            justify-content: center;
            width: 38px;
        }

        .nav-section {
            color: #7783a3;
            font-size: .67rem;
            letter-spacing: .12em;
        }

        .fs-7 {
            font-size: .82rem;
        }

        .app-header {
            background: rgba(255, 255, 255, .92);
            backdrop-filter: blur(12px);
        }

        .content-wrap {
            min-width: 0;
        }

        .main-content {
            padding: clamp(1rem, 2.4vw, 2rem);
        }

        .card {
            border: 1px solid var(--line);
            border-radius: 16px;
        }

        .card-soft {
            box-shadow: 0 8px 26px rgba(27, 43, 79, .045);
        }

        .page-title {
            color: var(--ink);
            font-size: clamp(1.45rem, 2vw, 1.9rem);
            font-weight: 720;
            letter-spacing: -.035em;
        }

        .form-control,
        .form-select {
            border-color: #e1e6ef;
            border-radius: 9px;
            min-height: 42px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #8993ed;
            box-shadow: 0 0 0 .2rem rgba(86, 103, 232, .12);
        }

        .btn {
            border-radius: 9px;
            font-weight: 600;
        }

        .btn-primary {
            background: var(--accent);
            border-color: var(--accent);
        }

        .btn-primary:hover {
            background: #4556d2;
            border-color: #4556d2;
        }

        .table> :not(caption)>*>* {
            border-bottom-color: #edf0f5;
            padding: .9rem .8rem;
        }

        .table thead th {
            color: #8992a6;
            font-size: .69rem;
            font-weight: 700;
            letter-spacing: .08em;
        }

        .table tbody tr:last-child td {
            border-bottom: 0;
        }

        .table-hover tbody tr:hover {
            background: #fafbfe;
        }

        .avatar-sm {
            align-items: center;
            background: #eef0ff;
            border-radius: 50%;
            color: #4c59c5;
            display: inline-flex;
            flex: 0 0 auto;
            font-size: .78rem;
            font-weight: 700;
            height: 36px;
            justify-content: center;
            width: 36px;
        }

        .status-pill {
            border-radius: 999px;
            display: inline-flex;
            font-size: .73rem;
            font-weight: 650;
            padding: .35rem .65rem;
        }

        .status-new {
            background: #eef0ff;
            color: #4a56c4;
        }

        .status-contacted {
            background: #eaf5ff;
            color: #2270a8;
        }

        .status-counseling {
            background: #fff4e3;
            color: #a56a13;
        }

        .status-application {
            background: #f1eaff;
            color: #7549b7;
        }

        .status-enrolled {
            background: #e7f7ef;
            color: #218250;
        }

        .status-lost {
            background: #f1f2f5;
            color: #737c8f;
        }

        .text-muted {
            color: var(--muted) !important;
        }

        .footer {
            color: #929bb0;
        }

        .sidebar-backdrop {
            display: none;
        }

        @media (max-width: 767.98px) {
            .sidebar {
                bottom: 0;
                left: 0;
                position: fixed;
                top: 0;
                transform: translateX(-100%);
                transition: transform .22s ease;
            }

            body.sidebar-open .sidebar {
                transform: translateX(0);
            }

            body.sidebar-open .sidebar-backdrop {
                background: rgba(15, 23, 42, .42);
                display: block;
                inset: 0;
                position: fixed;
                z-index: 1035;
            }
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">

    <div class="d-flex flex-grow-1">
        <?= $this->include('layouts/partials/_sidebar') ?>
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <div class="d-flex flex-column flex-grow-1">
            <?= $this->include('layouts/partials/_header') ?>

            <main class="main-content flex-grow-1">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <i class="bi bi-check-circle me-2"></i> <?= esc(session()->getFlashdata('success')) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i> <?= esc(session()->getFlashdata('error')) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?= $this->renderSection('content') ?>
            </main>

            <?= $this->include('layouts/partials/_footer') ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');
        const closeSidebar = () => document.body.classList.remove('sidebar-open');
        sidebarToggle?.addEventListener('click', () => document.body.classList.toggle('sidebar-open'));
        sidebarBackdrop?.addEventListener('click', closeSidebar);
    </script>
</body>

</html>