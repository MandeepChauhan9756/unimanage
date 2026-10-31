<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex align-items-center justify-content-center" style="min-height:60vh">
    <section class="card card-soft p-4 p-md-5 text-center" style="max-width:520px">
        <div class="bg-primary-subtle text-primary rounded-4 d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="height:58px;width:58px">
            <i class="bi bi-shield-lock fs-3"></i>
        </div>
        <h1 class="page-title h3 mb-2">Admissions staff only</h1>
        <p class="text-muted mb-4">This workspace is for authorized university admissions counselors. Contact your administrator if you need access.</p>
        <a href="<?= base_url('logout') ?>" class="btn btn-outline-secondary align-self-center">Sign out</a>
    </section>
</div>
<?= $this->endSection() ?>
