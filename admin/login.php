<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../config/database.php';
    
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (!empty($username) && !empty($password)) {
        $db = new Database();
        $username = $db->escape($username);
        
        $result = $db->query("SELECT * FROM admin_users WHERE username = '$username' LIMIT 1");
        $user = $db->fetch($result);
        
        $isValid = false;
        
        if ($user) {
            // Try multiple hash methods for compatibility
            if (password_verify($password, $user['password'])) {
                // Modern password_hash verification
                $isValid = true;
            } elseif (hash('sha256', $password) === $user['password']) {
                // SHA256 hash verification
                $isValid = true;
            } elseif (md5($password) === $user['password']) {
                // MD5 hash verification
                $isValid = true;
            } elseif ($password === $user['password']) {
                // Plain text verification (not recommended for production)
                $isValid = true;
            }
        }
        
        if ($isValid) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_name'] = $user['full_name'];
            
            header('Location: index.php');
            exit;
        } else {
            $error = 'Username atau password salah!';
        }
    } else {
        $error = 'Semua field harus diisi!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Lab Kampus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        * {
            font-family: 'Poppins', sans-serif;
        }
        .gradient-bg {
            background: blue;
        }
        .blob {
            background : blue;
            filter: blur(40px);
            opacity: 0.5;
            animation: blob 7s infinite;
        }
        @keyframes blob {
            0%, 100% {
                border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
            }
            50% {
                border-radius: 30% 60% 70% 40% / 50% 60% 30% 60%;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center relative overflow-hidden">
        <!-- Animated Background Blobs -->
        <div class="absolute top-20 left-20 w-72 h-72 blob"></div>
        <div class="absolute bottom-20 right-20 w-96 h-96 blob" style="animation-delay: 2s;"></div>
        
        <div class="max-w-md w-full mx-4 relative z-10">
            <!-- Back to Home -->
            <div class="mb-6">
                <a href="../index.php" class="text-gray-600 hover:text-purple-600 transition flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke Beranda
                </a>
            </div>
            
            <!-- Login Card -->
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                <div class="gradient-bg p-8 text-center text-white">
                    <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-shield text-4xl"></i>
                    </div>
                    <h1 class="text-3xl font-bold mb-2">Admin Login</h1>
                    <p class="text-gray-100">Laboratorium Kampus</p>
                </div>
                
                <div class="p-8">
                    <?php if ($error): ?>
                        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                                <p class="text-red-700"><?php echo htmlspecialchars($error); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-6">
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-user mr-2"></i>Username
                            </label>
                            <input type="text" name="username" required 
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 outline-none transition"
                                   placeholder="Masukkan username">
                        </div>
                        
                        <div class="mb-6">
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-lock mr-2"></i>Password
                            </label>
                            <input type="password" name="password" required 
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 outline-none transition"
                                   placeholder="Masukkan password">
                        </div>
                        
                        <button type="submit" class="w-full gradient-bg text-white font-semibold py-3 rounded-lg hover:shadow-lg transition">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
