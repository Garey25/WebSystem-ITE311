<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center mt-4">
    <div class="col-md-7 col-lg-6">
        <h1 class="text-center mb-4" style="color: #ffffff;">Create Account</h1>

        <?php if (session()->getFlashdata('register_error')): ?>
            <div class="alert alert-danger" role="alert">
                <?= esc(session()->getFlashdata('register_error')) ?>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm" style="background-color: #181818;">
            <div class="card-body p-4">
                <form action="<?= base_url('register') ?>" method="post">
                    <div class="mb-3">
                        <label for="name" class="form-label text-light">Name</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="name" 
                            name="name" 
                            required 
                            value="<?= esc(old('name')) ?>"
                            placeholder="Enter your name"
                            style="background-color: #2a2a2a; border: 1px solid #444; color: #fff;">
                    </div>
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
                    <div class="mb-3">
                        <label for="password_confirm" class="form-label text-light">Confirm Password</label>
                        <input 
                            type="password" 
                            class="form-control" 
                            id="password_confirm" 
                            name="password_confirm" 
                            required
                            placeholder="Re-enter your password"
                            style="background-color: #2a2a2a; border: 1px solid #444; color: #fff;">
                    </div>
                    <button type="submit" class="btn btn-primary w-100" style="background-color: #1DB954; border-color: #1DB954;">
                        Create Account
                    </button>
                </form>

                <!-- Already have an account? -->
                <div class="text-center mt-3">
                    <p class="mb-0 text-light">Already have an account? 
                        <a href="<?= base_url('login') ?>" style="color: #1DB954; text-decoration: none;">Log in here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
