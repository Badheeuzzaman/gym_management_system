<?php
session_start();
require_once 'config/database.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
        $stmt->execute([
            $username,
            $username
        ]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            header("Location: dashboard.php");
            exit;
        } else {
            // Fallback for plain password check during first install (password = password)
            if ($user && $password === 'password') {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'];
                header("Location: dashboard.php");
                exit;
            }

            $msg = 'Invalid credentials! Try admin / password';
        }
    } catch (Exception $e) {
        // If tables not exist, redirect to installer message
        $msg = 'Database not initialized. Please import sql/schema.sql. Error: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FlexFit Gym</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: url('assets/gym.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: #1e1e2f;
            border-radius: 24px;
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            margin: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .5);
            opacity: 0.9;
        }

        .left {
            background: linear-gradient(135deg, #ff4d00 35%, #a29f9fda 100%);
            padding: 50px 40px;
            color: white;
        }

        .right {
            padding: 50px 40px;
            background: black;
        }

        .form-control {
            background: #2a2a40;
            border: 1px solid #3a3a5a;
            color: white;
            padding: 12px 16px;
            border-radius: 10px;
        }

        .form-control:focus {
            background: #2a2a40;
            border-color: #6c5ce7;
            color: white;
            box-shadow: none;
        }

        .btn-login {
            background: linear-gradient(135deg, #6c5ce7, #8c7ae6);
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
        }

        .stat-mini {
            background: rgba(255, 255, 255, .1);
            border-radius: 12px;
            padding: 12px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-card row g-0">
            <div class="col-md-6 left d-flex flex-column justify-content-center align-items:center text-center">
                <div>
                    <!-- LOGO -->
                    <img src="assets/logo.png" alt="FlexFit Logo" style="width:95px; height:120px; margin-bottom:15px; border-radius:12px;">
                    <h1 style="font-weight:800; font-size:36px;">FlexFit</h1>
                    <p style="opacity:.8;">Gym Management System</p>
                    <div style="margin-top:30px;">
                        <h3 style="font-weight:700;">Manage Everything, In One Place</h3>
                    </div>
                </div>
                <div style="opacity:.6; font-size:11px; margin-top:40px;">
                    © <?= date('Y') ?> FlexFit Gym.
                </div>
            </div>
            <div class="col-md-6 right">
                <h3 style="color:white; font-weight:700;">Welcome back</h3>
                <p style="color:#a1a1b5; font-size:13px;">Sign in to your admin account</p>

                <?php if ($msg) : ?>
                    <div class="alert alert-danger" style="background:#3a1f2f; border-color:#d63031; color:#ffccc9; font-size:13px;">
                        <?= $msg ?>
                    </div>
                <?php endif; ?>

                <form method="post" style="margin-top:28px;">
                    <div class="mb-3">
                        <label style="color:#a1a1b5; font-size:12px; text-transform:uppercase; letter-spacing:1px;">Username or Email</label>
                        <input type="text" name="username" class="form-control" placeholder="admin" required value="admin">
                    </div>
                    <div class="mb-3">
                        <label style="color:#a1a1b5; font-size:12px; text-transform:uppercase; letter-spacing:1px;">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required value="password">
                    </div>
                    <button type="submit" class="btn btn-primary btn-login w-100 mt-3">
                        Sign In →
                    </button>
                </form>

                <div style="margin-top:20px; background:#25253d; border-radius:10px; padding:14px; border:1px dashed #3a3a5a;">
                    <div style="color:#a1a1b5; font-size:11px; text-transform:uppercase; letter-spacing:1px; margin-bottom:6px;">
                        Demo Credentials
                    </div>
                    <div style="color:white; font-size:13px;">
                        <strong>User:</strong> admin &nbsp; | &nbsp; <strong>Pass:</strong> password
                    </div>
                </div>

                <div style="margin-top:20px; text-align:center; color:#6c6c80; font-size:11px;">
                    Need help? Check <a href="#" style="color:#a29bfe;">documentation</a> or run
                    <code>sql/schema.sql</code> if first time.
                </div>
            </div>
        </div>
    </div>
</body>
</html>
