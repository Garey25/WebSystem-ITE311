<?= $this->extend('template') ?>

<?= $this->section('title') ?>Contact<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <h2>Contact Us</h2>
    <p>If you have any questions, feel free to reach out to us at <strong>student@example.com</strong>.</p>

<style>
body {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
    overflow: hidden;
}

body::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    animation: rotate 20s linear infinite;
    z-index: -1;
}

@keyframes rotate {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.container {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 24px;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.2);
    position: relative;
    z-index: 1;
}

h1, h2, h3, h4, h5, h6, p, a, span, div {
    color: #000000 !important;
}
</style>
<?= $this->endSection() ?>
