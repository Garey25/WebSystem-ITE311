<?= $this->extend('template') ?>

<?= $this->section('content') ?>
	<div class="d-flex justify-content-between align-items-center mb-4">
		<h1 class="mb-0">Upload Material - <?= esc($course['title']) ?></h1>
		<a href="<?= site_url('dashboard') ?>" class="btn btn-outline-secondary">Back</a>
	</div>

	<?php if (session('success')): ?>
		<div class="alert alert-success"><?= esc(session('success')) ?></div>
	<?php endif; ?>
	<?php if (session('error')): ?>
		<div class="alert alert-danger"><?= esc(session('error')) ?></div>
	<?php endif; ?>

	<form action="" method="post" enctype="multipart/form-data">
		<?= csrf_field() ?>
		<div class="mb-3">
			<label class="form-label">Choose file</label>
			<input type="file" name="material" class="form-control" required>
			<div class="form-text">Allowed: pdf, ppt, pptx, doc, docx, zip, rar, txt. Max 10MB.</div>
		</div>
		<button type="submit" class="btn btn-primary">Upload</button>
	</form>
<?= $this->endSection() ?>


