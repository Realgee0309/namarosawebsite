<?php
require_once __DIR__ . '/../includes/db.php';

// Already logged in → redirect to dashboard
if (isAdmin()) {
    redirect(BASE_URL . 'admin/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $stmt = $pdo->prepare('SELECT id, username, full_name, password_hash, active FROM admins WHERE username = ? OR email = ? LIMIT 1');
        $stmt->execute([$username, $username]);
        $admin = $stmt->fetch();

        if ($admin && $admin['active'] && password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_name'] = $admin['full_name'] ?: $admin['username'];
            $_SESSION['admin_user'] = $admin['username'];

            // Update last login
            $pdo->prepare('UPDATE admins SET last_login = NOW() WHERE id = ?')->execute([$admin['id']]);

            redirect(BASE_URL . 'admin/index.php');
        } else {
            $error = 'Invalid username or password.';
            // Small delay to slow brute force
            sleep(1);
        }
    } else {
        $error = 'Please enter your username and password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Login — Namarosa Hardware</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css?v=1.0.5" />
    <style>
        body   { background: #0f1924; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .login-box {
            background: var(--slate);
            border-radius: var(--radius-lg);
            padding: 2.5rem;
            width: 100%;
            max-width: 400px;
            border: 1px solid rgba(255,255,255,0.08);
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        }
        .login-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-logo .logo-icon {
            width: 56px; height: 56px;
            background: var(--orange);
            border-radius: 12px;
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.6rem;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.75rem;
        }
        .login-logo h1 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.6rem;
            color: var(--white);
            letter-spacing: 0.04em;
        }
        .login-logo p { color: var(--orange); font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700; }
        .login-error {
            background: rgba(220,38,38,0.15);
            border: 1px solid rgba(220,38,38,0.4);
            color: #fca5a5;
            padding: 0.75rem 1rem;
            border-radius: var(--radius);
            font-size: 0.88rem;
            margin-bottom: 1.25rem;
        }
        .login-field { margin-bottom: 1.25rem; }
        .login-field label { display: block; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: rgba(255,255,255,0.7); margin-bottom: 0.4rem; }
        .login-field .input-wrap { position: relative; }
        .login-field .input-wrap i { position: absolute; left: 0.9rem; top: 50%; transform: translateY(-50%); color: rgba(255,255,255,0.3); font-size: 0.9rem; }
        .login-field input {
            width: 100%;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            color: var(--white);
            padding: 0.75rem 0.9rem 0.75rem 2.5rem;
            border-radius: var(--radius);
            font-family: 'Inter', sans-serif;
            font-size: 0.92rem;
            transition: border-color 200ms;
        }
        .login-field input:focus { outline: none; border-color: var(--orange); background: rgba(255,255,255,0.09); }
        .login-field input::placeholder { color: rgba(255,255,255,0.3); }
        .login-btn {
            width: 100%;
            background: var(--orange);
            color: var(--white);
            border: none;
            padding: 0.85rem;
            border-radius: var(--radius);
            font-family: 'Inter', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            cursor: pointer;
            transition: background 200ms;
            margin-top: 0.5rem;
        }
        .login-btn:hover { background: var(--orange-dark); }
        .login-back { text-align: center; margin-top: 1.25rem; }
        .login-back a { color: rgba(255,255,255,0.4); font-size: 0.8rem; text-decoration: none; transition: color 200ms; }
        .login-back a:hover { color: var(--orange); }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-logo">
            <img src="<?= BASE_URL ?>assets/images/logo.png" alt="Namarosa Hardware Logo" class="login-logo-img" style="height: 60px; width: auto; object-fit: contain; margin: 0 auto 0.75rem; display: block;" />
            <h1>Namarosa Hardware</h1>
            <p>Admin Dashboard</p>
        </div>

        <?php if ($error): ?>
            <div class="login-error"><i class="fas fa-exclamation-triangle"></i> <?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>admin/login.php" novalidate>
            <div class="login-field">
                <label for="username">Username or Email</label>
                <div class="input-wrap">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username"
                           value="<?= e($_POST['username'] ?? '') ?>"
                           placeholder="admin"
                           autocomplete="username"
                           required />
                </div>
            </div>
            <div class="login-field">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password"
                           placeholder="••••••••"
                           autocomplete="current-password"
                           required />
                </div>
            </div>
            <button type="submit" class="login-btn">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </button>
        </form>

        <div class="login-back">
            <a href="<?= BASE_URL ?>index.php"><i class="fas fa-arrow-left"></i> Back to website</a>
        </div>
    </div>
</body>
</html>
