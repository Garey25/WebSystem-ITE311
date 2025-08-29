<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center mt-4">
    <div class="col-md-6 col-lg-5">
        <h1 class="text-center mb-4" style="color: #ffffff;">Sign In</h1>

        <?php if (session()->getFlashdata('register_success')): ?>
            <div class="alert alert-success" role="alert">
                <?= esc(session()->getFlashdata('register_success')) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('login_error')): ?>
            <div class="alert alert-danger" role="alert">
                <?= esc(session()->getFlashdata('login_error')) ?>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm" style="background-color: #181818;">
            <div class="card-body p-4">
                <form action="<?= base_url('login') ?>" method="post">
                    <div class="mb-3">
                        <label for="email" class="form-label text-light">Email</label>
                        <input 
                            type="email" 
                            class="form-control" 
                            id="email" 
                            name="email" 
                            required 
                            value="<?= esc(old('email')) ?>"
                            placeholder="Enter your email"
                            style="background-color: #2a2a2a; border: 1px solid #444; color: #fff;">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label text-light">Password</label>
                        <input 
                            type="password" 
                            class="form-control" 
                            id="password" 
                            name="password" 
                            required
                            placeholder="Enter your password"
                            style="background-color: #2a2a2a; border: 1px solid #444; color: #fff;">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary" style="background-color: #1DB954; border-color: #1DB954;">
                            Login
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <p class="text-center mt-3 small" style="color: #b3b3b3;">
            Don't have an account?
            <a href="<?= base_url('register') ?>" style="color: #1DB954; text-decoration: none;">Register</a>
        </p>
    </div>
</div>
<?= $this->endSection() ?>
