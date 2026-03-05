<?php
session_start();
require_once '../config/database.php';
require_once '../config/function.php';

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: ../pages/dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);
    
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $_SESSION['level'] = $user['level'];
        
        // Catat log login
        catatLog($conn, 'Login', 'users', $user['id_user'], 'Login ke sistem');
        
        header("Location: ../pages/dashboard.php");
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Inventaris Gudang</title>
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
            padding: 20px;
        }
        
        .login-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            max-width: 400px;
            width: 100%;
            border-top: 5px solid #f6c23e;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header i {
            font-size: 3rem;
            color: #4e73df;
            background: #eaecf4;
            width: 80px;
            height: 80px;
            line-height: 80px;
            border-radius: 50%;
            margin-bottom: 15px;
        }
        
        .login-header h3 {
            color: #4e73df;
            font-weight: 600;
            font-size: 1.75rem;
            margin-bottom: 5px;
        }
        
        .login-header p {
            color: #858796;
            font-size: 0.9rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            font-weight: 500;
            color: #4e73df;
            margin-bottom: 8px;
            display: block;
        }
        
        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 12px;
            font-size: 0.95rem;
            height: auto;
            width: 100%;
        }
        
        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.1);
            outline: none;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
            font-size: 1rem;
            cursor: pointer;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(78, 115, 223, 0.3);
        }
        
        .btn-login i {
            margin-right: 8px;
        }
        
        .alert {
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 20px;
            border: none;
            border-left: 5px solid;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border-left-color: #e74a3b;
        }
        
        .demo-login {
            background: #f8f9fc;
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            border: 1px dashed #d1d3e2;
        }
        
        .demo-login p {
            margin-bottom: 8px;
            color: #5a5c69;
            font-weight: 500;
        }
        
        .demo-login .badge {
            background: #4e73df;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8rem;
            margin-right: 5px;
            display: inline-block;
        }
        
        .footer-text {
            text-align: center;
            margin-top: 20px;
            color: #b7b9cc;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <i class="fas fa-box"></i>
            <h3>Sistem Inventaris</h3>
            <p>Sistem Manajemen Stok Barang</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" 
                       placeholder="Masukkan username" required autofocus>
            </div>
            
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" 
                       placeholder="Masukkan password" required>
            </div>
            
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
        
        <div class="demo-login">
            <p><i class="fas fa-info-circle me-2" style="color: #4e73df;"></i> Demo Login:</p>
            <div style="margin-bottom: 8px;">
                <span class="badge">Admin</span>
                <small>username: <strong>admin</strong> / password: <strong>admin123</strong></small>
            </div>
            <div>
                <span class="badge" style="background: #1cc88a;">Staff</span>
                <small>username: <strong>staff</strong> / password: <strong>staff123</strong></small>
            </div>
        </div>
        
        <div class="footer-text">
            &copy; <?php echo date('Y'); ?> Sistem Inventaris Gudang
        </div>
    </div>
</body>
</html>