<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#111a36">
    <title>Sign in - UniManage Admissions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --ink: #17213b; --muted: #78839a; --accent: #5667e8; }
        body {
            align-items: center;
            background: radial-gradient(ellipse at 18% 0%, #283969 0, #111a36 50%, #0b1229 100%);
            color: var(--ink);
            display: flex;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            justify-content: center;
            min-height: 100vh;
            padding: 1.25rem;
        }
        .login-shell { max-width: 1000px; width: 100%; }
        .login-card { border: 0; border-radius: 22px; box-shadow: 0 28px 80px rgba(0, 0, 0, .28); overflow: hidden; }
        .brand-panel { background: linear-gradient(150deg, #5667e8, #3547bb 76%, #273892); color: #fff; min-height: 570px; padding: 3rem; position: relative; }
        .brand-panel:after { border: 1px solid rgba(255,255,255,.12); border-radius: 50%; content: ""; height: 360px; position: absolute; right: -120px; top: 235px; width: 360px; }
        .brand-panel:before { border: 1px solid rgba(255,255,255,.12); border-radius: 50%; content: ""; height: 500px; position: absolute; right: -190px; top: 165px; width: 500px; }
        .brand-logo { align-items: center; background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.2); border-radius: 13px; display: inline-flex; height: 44px; justify-content: center; width: 44px; }
        .brand-copy { margin-top: 6rem; max-width: 340px; position: relative; z-index: 1; }
        .brand-copy h1 { font-size: clamp(2rem, 3.2vw, 2.8rem); font-weight: 720; letter-spacing: -.04em; line-height: 1.1; }
        .login-content { padding: clamp(2rem, 5vw, 4.5rem); }
        .form-control { border-color: #e1e6ef; border-radius: 10px; min-height: 48px; }
        .form-control:focus { border-color: #8993ed; box-shadow: 0 0 0 .2rem rgba(86, 103, 232, .12); }
        .btn-primary { background: var(--accent); border-color: var(--accent); border-radius: 10px; font-weight: 650; min-height: 48px; }
        .btn-primary:hover { background: #4556d2; border-color: #4556d2; }
        .fs-7 { font-size: .84rem; }
        @media (max-width: 767.98px) { .brand-panel { min-height: 0; padding: 1.5rem; } .brand-copy { margin-top: 2rem; } .brand-copy p { margin-bottom: 0; } .login-content { padding: 2rem 1.5rem; } }
    </style>
</head>
<body>
<main class="login-shell">
    <div class="card login-card">
        <div class="row g-0">
            <section class="col-md-5 brand-panel d-flex flex-column">
                <a href="<?= base_url('login') ?>" class="d-flex align-items-center gap-2 text-decoration-none text-white fw-bold fs-5">
                    <span class="brand-logo"><i class="bi bi-mortarboard-fill fs-5"></i></span> UniManage
                </a>
                <div class="brand-copy">
                    <div class="text-uppercase small fw-semibold mb-3" style="letter-spacing:.13em;color:#d9ddff">Admissions CRM</div>
                    <h1>Every student's next step, made clearer.</h1>
                    <p class="mt-3 mb-0 text-white-50">A calmer workspace for university counseling, applications and meaningful follow-ups.</p>
                </div>
                <div class="mt-auto pt-5 small text-white-50">Built for teams that help students find their future.</div>
            </section>
            <section class="col-md-7 bg-white">
                <div class="login-content">
                    <div class="mb-4">
                        <div class="text-uppercase text-primary fw-bold fs-7 mb-2">Welcome back</div>
                        <h2 class="h3 fw-bold mb-2">Sign in to your workspace</h2>
                        <p class="text-secondary mb-0">Use your team account to continue.</p>
                    </div>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger py-2 fs-7" role="alert"><?= esc(session()->getFlashdata('error')) ?></div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success py-2 fs-7" role="status"><?= esc(session()->getFlashdata('success')) ?></div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger py-2 fs-7" role="alert"><?php foreach (session()->getFlashdata('errors') as $error): ?><div><?= esc($error) ?></div><?php endforeach; ?></div>
                    <?php endif; ?>

                    <form action="<?= base_url('login') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Work email</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="you@organization.com" value="<?= esc(old('email')) ?>" autocomplete="username" required>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" autocomplete="current-password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Sign in <i class="bi bi-arrow-right ms-1"></i></button>
                    </form>

                    <div class="border-top mt-4 pt-3 text-center text-secondary fs-7">
                        Need access? Contact your admissions administrator.
                    </div>
                </div>
            </section>
        </div>
    </div>
</main>
</body>
</html>
