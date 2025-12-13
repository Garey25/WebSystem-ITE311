<?= $this->extend('template') ?>

<?= $this->section('content') ?>

<h1 class="fw-bold mb-3">My Enrollments</h1>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <?php if (empty($enrollments)): ?>
            <div class="alert alert-info mb-0">You have no enrollments yet.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Status</th>
                            <th>Enrolled At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($enrollments as $e): ?>
                            <tr>
                                <td><?= esc($e['course_title'] ?? $e['course_id'] ?? '') ?></td>
                                <td><?= esc($e['status'] ?? '') ?></td>
                                <td><?= esc($e['enrolled_at'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
