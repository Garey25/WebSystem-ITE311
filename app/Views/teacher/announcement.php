<?= $this->extend('template') ?>

<?= $this->section('content') ?>

<div class="dashboard-wrapper">
    <div class="card dashboard-hero shadow-sm border-0 mb-4">
        <div class="card-body d-lg-flex justify-content-between align-items-center gap-4">
            <div>
                <p class="text-uppercase text-muted fw-semibold mb-1">Teacher Dashboard</p>
                <h1 class="fw-bold mb-2">Announcement</h1>
                <p class="text-muted mb-0">Send an announcement to students enrolled in this course.</p>
            </div>
            <div class="mt-3 mt-lg-0 text-lg-end">
                <p class="mb-1 text-muted text-uppercase small">Course</p>
                <h5 class="mb-0"><?= esc($courseLabel ?? 'Course') ?></h5>
            </div>
        </div>
    </div>

    <div class="card dashboard-card shadow-sm border-0">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Compose</h5>
            <a href="<?= site_url('dashboard') ?>" class="btn btn-outline-primary btn-sm">Back</a>
        </div>
        <div class="card-body">
            <form method="post" action="<?= site_url('teacher/announcement/send') ?>">
                <input type="hidden" name="course_id" value="<?= esc($courseId ?? 0) ?>">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

                <div class="mb-3">
                    <label for="message" class="form-label">Message</label>
                    <textarea id="message" name="message" class="form-control" rows="5" placeholder="Type your announcement..." required><?= esc($message ?? '') ?></textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Send Announcement</button>
                    <a href="<?= site_url('dashboard') ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.dashboard-wrapper {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.dashboard-hero {
    background: linear-gradient(120deg, #003049, rgba(0, 48, 73, 0.8));
    color: #EAE2B7;
    border-radius: 1.25rem;
    border: 1px solid rgba(247, 127, 0, 0.3);
}

.dashboard-card {
    background: rgba(0, 48, 73, 0.6);
    border: 1px solid rgba(247, 127, 0, 0.3);
    border-radius: 1.25rem;
    color: #EAE2B7;
}

.dashboard-card .card-header {
    border-bottom: 1px solid rgba(247, 127, 0, 0.2);
    background: transparent;
}
</style>

<?= $this->endSection() ?>
