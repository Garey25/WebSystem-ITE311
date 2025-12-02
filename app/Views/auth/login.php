<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="modern-auth-container">
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <div class="logo-section">
                    <div class="logo-icon">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h1 class="auth-title">Welcome Back</h1>
                    <p class="auth-subtitle">Sign in to your account</p>
                </div>
            </div>

            <?php if (session()->getFlashdata('register_success')): ?>
                <div class="alert alert-success" role="alert">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <?= esc(session()->getFlashdata('register_success')) ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('login_error')): ?>
                <div class="alert alert-danger" role="alert">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 9V13M12 17H12.01M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <?= esc(session()->getFlashdata('login_error')) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($validation) && $validation->getErrors()): ?>
                <div class="alert alert-danger" role="alert">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 9V13M12 17H12.01M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <ul class="mb-0">
                        <?php foreach ($validation->getErrors() as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= site_url('login') ?>" method="post" class="auth-form">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <polyline points="22,6 12,13 2,6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <input 
                            type="email" 
                            class="form-input" 
                            id="email" 
                            name="email" 
                            required 
                            value="<?= esc(old('email')) ?>"
                            placeholder="Enter your email address">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                            <circle cx="12" cy="16" r="1" fill="currentColor"/>
                            <path d="M7 11V7A5 5 0 0 1 17 7V11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <input 
                            type="password" 
                            class="form-input" 
                            id="password" 
                            name="password" 
                            required
                            placeholder="Enter your password">
                    </div>
                </div>

                <button type="submit" class="auth-button">
                    <span>Sign In</span>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 12H19M12 5L19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </form>

            <div class="auth-footer">
                <p class="signup-text">
                    Don't have an account?
                    <a href="<?= base_url('register') ?>" class="signup-link">Create Account</a>
                </p>
            </div>
        </div>
    </div>
</div>

<style>
.modern-auth-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #003049 0%, rgba(0, 48, 73, 0.9) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    position: relative;
    overflow: hidden;
}

.modern-auth-container::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    animation: rotate 20s linear infinite;
}

@keyframes rotate {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.auth-wrapper {
    width: 100%;
    max-width: 420px;
    position: relative;
    z-index: 1;
}

.auth-card {
    background: rgba(234, 226, 183, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 24px;
    padding: 40px;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
    border: 2px solid rgba(247, 127, 0, 0.3);
}

.auth-header {
    text-align: center;
    margin-bottom: 32px;
}

.logo-section {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 16px;
}

.logo-icon {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, #F77F00 0%, #FCBF49 100%);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #003049;
    box-shadow: 0 8px 16px rgba(247, 127, 0, 0.4);
}

.auth-title {
    font-size: 2rem;
    font-weight: 700;
    color: #003049;
    margin: 0;
    letter-spacing: -0.025em;
}

.auth-subtitle {
    color: #003049;
    font-size: 1rem;
    margin: 0;
    font-weight: 400;
    opacity: 0.8;
}

.alert {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    border-radius: 12px;
    margin-bottom: 24px;
    font-size: 14px;
    font-weight: 500;
}

.alert-success {
    background: rgba(252, 191, 73, 0.2);
    color: #003049;
    border: 1px solid #FCBF49;
}

.alert-danger {
    background: rgba(214, 40, 40, 0.2);
    color: #003049;
    border: 1px solid #D62828;
}

.auth-form {
    margin-bottom: 32px;
}

.form-group {
    margin-bottom: 24px;
}

.form-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #003049;
    margin-bottom: 8px;
}

.input-wrapper {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #F77F00;
    z-index: 1;
}

.form-input {
    width: 100%;
    padding: 16px 16px 16px 48px;
    border: 2px solid rgba(247, 127, 0, 0.3);
    border-radius: 12px;
    font-size: 16px;
    background: rgba(255, 255, 255, 0.9);
    color: #003049;
    transition: all 0.3s ease;
    box-sizing: border-box;
}

.form-input:focus {
    outline: none;
    border-color: #F77F00;
    box-shadow: 0 0 0 3px rgba(247, 127, 0, 0.2);
    background: #ffffff;
}

.form-input::placeholder {
    color: rgba(0, 48, 73, 0.5);
}

.auth-button {
    width: 100%;
    background: linear-gradient(135deg, #F77F00 0%, #FCBF49 100%);
    color: #003049;
    border: none;
    border-radius: 12px;
    padding: 16px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    box-shadow: 0 4px 12px rgba(247, 127, 0, 0.4);
}

.auth-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(247, 127, 0, 0.5);
    background: linear-gradient(135deg, #FCBF49 0%, #F77F00 100%);
}

.auth-button:active {
    transform: translateY(0);
}

.auth-footer {
    text-align: center;
}

.signup-text {
    color: #003049;
    font-size: 14px;
    margin: 0;
    opacity: 0.8;
}

.signup-link {
    color: #F77F00;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
}

.signup-link:hover {
    color: #FCBF49;
    text-decoration: underline;
}

@media (max-width: 480px) {
    .auth-card {
        padding: 32px 24px;
        margin: 0 16px;
    }
    
    .auth-title {
        font-size: 1.75rem;
    }
}
</style>
<?= $this->endSection() ?>
