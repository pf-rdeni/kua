<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KUA System</title>
    <!-- Google Font: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('template/backend/plugins/fontawesome-free/css/all.min.css') ?>">
    <!-- AdminLTE (Bootstrap 4) -->
    <link rel="stylesheet" href="<?= base_url('template/backend/dist/css/adminlte.min.css') ?>">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            margin: 0;
            overflow: hidden;
        }
        .login-container {
            height: 100vh;
            width: 100vw;
            display: flex;
            flex-wrap: wrap;
        }
        .left-panel {
            background: linear-gradient(135deg, #145A32 0%, #28B463 100%);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 50%;
            height: 100%;
            padding: 2rem;
            position: relative;
        }
        .left-panel::before {
             /* Creative decoration circle */
            content: '';
            position: absolute;
            top: -10%;
            left: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }
        .left-panel::after {
            content: '';
            position: absolute;
            bottom: -10%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }
        .right-panel {
            background: white;
            width: 50%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            border-top-left-radius: 30px; /* Optional overlap effect for mobile if stacked */
            border-bottom-left-radius: 30px;
        }

        .brand-logo {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .welcome-text {
            font-size: 2rem;
            font-weight: 600;
        }
        .sub-text {
            opacity: 0.8;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .login-form {
            width: 100%;
            max-width: 400px;
        }
        .login-title {
            color: #145A32;
            font-size: 2.5rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 0.5rem;
        }
        .login-subtitle {
            color: #666;
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-control-custom {
            background-color: #e8f0fe;
            border: none;
            border-radius: 50px;
            padding: 1.5rem 1.5rem;
            margin-bottom: 1rem;
            font-size: 1rem;
        }
        .form-control-custom:focus {
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(40, 180, 99, 0.25);
            color: #333;
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            z-index: 10;
        }

        .btn-custom {
            background: #145A32;
            color: white;
            border-radius: 50px;
            padding: 0.8rem;
            font-weight: 600;
            width: 100%;
            margin-top: 1rem;
            transition: all 0.3s;
        }
        .btn-custom:hover {
            background: #0e3e23;
            transform: translateY(-2px);
        }

        .sign-in-promo {
            border: 2px solid white;
            border-radius: 50px;
            padding: 0.5rem 2rem;
            margin-top: 2rem;
            display: inline-block;
            font-weight: 500;
            text-decoration: none;
            color: white;
            transition: all 0.3s;
        }
        .sign-in-promo:hover {
            background: white;
            color: #145A32;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            body {
                overflow: auto;
            }
            .login-container {
                flex-direction: column;
                height: auto;
            }
            .left-panel {
                width: 100%;
                height: 40vh;
                border-bottom-left-radius: 0;
                border-bottom-right-radius: 50px; /* Curve effect */
                z-index: 10;
            }
            .right-panel {
                width: 100%;
                height: auto;
                min-height: 60vh;
                border-top-left-radius: 0;
                border-bottom-left-radius: 0;
                padding-top: 3rem;
            }
            .left-panel::after {
                display: none; /* Simplify background on mobile */
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <!-- Left Side (Brand) -->
    <div class="left-panel">
        <div class="brand-logo">
            <i class="fas fa-mosque"></i>
        </div>
        <div class="welcome-text">SIM KUA</div>
        <p class="sub-text">Sistem Informasi Manajemen Pembantu KUA
        
        <div class="text-center d-none d-md-block">
            <p style="opacity: 0.9;">Selamat datang kembali!</p>
            <small>Silakan login untuk mengakses dashboard.</small>
        </div>
    </div>

    <!-- Right Side (Login Form) -->
    <div class="right-panel">
        <div class="login-form">
            <h2 class="login-title">Welcome</h2>
            <p class="login-subtitle">Login in to your account to continue</p>

            <?= view('App\Views\Auth\_message_block') ?>

            <form action="<?= url_to('login') ?>" method="post">
                <?= csrf_field() ?>

                <?php if ($config->validFields === ['email']): ?>
                    <div class="form-group">
                        <input type="email" class="form-control form-control-custom <?php if (session('errors.login')) : ?>is-invalid<?php endif ?>"
                               name="login" placeholder="<?=lang('Auth.email')?>">
                        <div class="invalid-feedback">
                            <?= session('errors.login') ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="form-group">
                        <input type="text" class="form-control form-control-custom <?php if (session('errors.login')) : ?>is-invalid<?php endif ?>"
                               name="login" placeholder="<?=lang('Auth.emailOrUsername')?>">
                        <div class="invalid-feedback">
                            <?= session('errors.login') ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="form-group password-container">
                    <input type="password" name="password" id="password" class="form-control form-control-custom <?php if (session('errors.password')) : ?>is-invalid<?php endif ?>" placeholder="<?=lang('Auth.password')?>">
                    <i class="fas fa-eye toggle-password" id="togglePasswordBtn" onclick="togglePassword()"></i>
                    <div class="invalid-feedback">
                        <?= session('errors.password') ?>
                    </div>
                </div>

                <?php if ($config->allowRemembering): ?>
                    <div class="form-check mb-3">
                        <input type="checkbox" name="remember" class="form-check-input" id="remember" <?php if (old('remember')) : ?> checked <?php endif ?>>
                        <label class="form-check-label text-muted" for="remember"><?=lang('Auth.rememberMe')?></label>
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn btn-custom btn-block">
                    LOG IN
                </button>

                <?php if ($config->activeResetter): ?>
                    <div class="text-center mt-3">
                        <a href="<?= url_to('forgot') ?>" class="text-muted"><small><?=lang('Auth.forgotYourPassword')?></small></a>
                    </div>
                <?php endif; ?>
                
                <?php if ($config->allowRegistration) : ?>
                    <div class="text-center mt-2">
                        <small>Don't have an account? <a href="<?= url_to('register') ?>" class="text-success font-weight-bold">Sign up</a></small>
                    </div>
                <?php endif; ?>

            </form>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const constToggle = document.getElementById('togglePasswordBtn');
    const password = document.getElementById('password');
    
    // Toggle the type attribute
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    
    // Toggle the eye / eye slash icon
    constToggle.classList.toggle('fa-eye');
    constToggle.classList.toggle('fa-eye-slash');
}
</script>

</body>
</html>
