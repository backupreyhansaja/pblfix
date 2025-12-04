<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Laboratorium Kampus</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        .gradient-bg {
            background: blue;
        }
        
        .gradient-text {
            background: blue;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .hover-scale {
            transition: transform 0.3s ease;
        }
        
        .hover-scale:hover {
            transform: scale(1.05);
        }
        
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        .nav-link {
            position: relative;
            transition: color 0.3s ease;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 50%;
            background: blue;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        
        .nav-link:hover::after {
            width: 100%;
        }
        
        .blob {
            background: blue;
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

        .scroll-top {
        position: fixed;
        bottom: 25px;
        right: 25px;
        width: 50px;
        height: 50px;
        background: #fffcfcff;
        color: #2563EB ;
        border: none;
        border-radius: 50%;
        font-size: 22px;
        cursor: pointer;
        display: none;
        align-items: center;
        justify-content: center;
        transition: 0.3s;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }

    .scroll-top:hover {
        background: #ffffffff;
    }

    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 gradient-bg rounded-lg flex items-center justify-center">
                        <i class="fas fa-flask text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold gradient-text">Lab Kampus</h1>
                        <p class="text-xs text-gray-600">Innovation Center</p>
                    </div>
                </div>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="<?php echo isset($pageInPages) ? '../' : ''; ?>index.php" class="nav-link text-gray-700 hover:text-blue-600 font-medium">Beranda</a>
                    
                    <!-- Dropdown Menu -->
                    <div class="relative group">
                        <button class="nav-link text-gray-700 hover:text-blue-600 font-medium flex items-center">
                            Informasi
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </button>
                        <div class="absolute left-0 mt-2 w-56 bg-white rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform group-hover:translate-y-0 translate-y-2">
                            <a href="<?php echo isset($pageInPages) ? '' : 'pages/'; ?>visi-misi.php" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition rounded-t-lg">
                                <i class="fas fa-eye mr-2"></i>Visi & Misi
                            </a>
                            <a href="<?php echo isset($pageInPages) ? '' : 'pages/'; ?>sejarah.php" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                                <i class="fas fa-book mr-2"></i>Sejarah
                            </a>
                            <a href="<?php echo isset($pageInPages) ? '' : 'pages/'; ?>gallery.php" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                                <i class="fas fa-images mr-2"></i>Gallery
                            </a>
                            <a href="<?php echo isset($pageInPages) ? '' : 'pages/'; ?>struktur_organisasi.php" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                                <i class="fas fa-sitemap mr-2"></i>Struktur Organisasi
                            </a>
                            <a href="<?php echo isset($pageInPages) ? '' : 'pages/'; ?>produk.php" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                                <i class="fas fa-box mr-2"></i>Produk
                            </a>
                            <a href="<?php echo isset($pageInPages) ? '' : 'pages/'; ?>partner_sponsor.php" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition rounded-b-lg">
                                <i class="fas fa-handshake mr-2"></i>Partner & Sponsor
                            </a>
                        </div>
                    </div>
                    
                    <a href="<?php echo isset($pageInPages) ? '' : 'pages/'; ?>news.php" class="nav-link text-gray-700 hover:text-blue-600 font-medium">Berita</a>
                    <a href="<?php echo isset($pageInPages) ? '../' : ''; ?>index.php#contact" class="nav-link text-gray-700 hover:text-blue-600 font-medium">Kontak</a>
                    <a href="<?php echo isset($pageInPages) ? '../' : ''; ?>admin/login.php" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover-scale">
                        <i class="fas fa-lock mr-2"></i>Admin
                    </a>
                </div>
                
                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" class="md:hidden text-gray-700">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
            
            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden mt-4 pb-4">
                <a href="<?php echo isset($pageInPages) ? '../' : ''; ?>index.php" class="block py-2 text-gray-700 hover:text-blue-600">Beranda</a>
                
                <!-- Mobile Dropdown -->
                <div class="mt-2">
                    <button id="mobile-dropdown-btn" class="w-full text-left py-2 text-gray-700 hover:text-blue-600 font-medium flex items-center justify-between">
                        <span>Informasi</span>
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                    <div id="mobile-dropdown" class="hidden pl-4 mt-1 space-y-1">
                        <a href="<?php echo isset($pageInPages) ? '' : 'pages/'; ?>visi-misi.php" class="block py-2 text-gray-600 hover:text-blue-600">
                            <i class="fas fa-eye mr-2"></i>Visi & Misi
                        </a>
                        <a href="<?php echo isset($pageInPages) ? '' : 'pages/'; ?>sejarah.php" class="block py-2 text-gray-600 hover:text-blue-600">
                            <i class="fas fa-book mr-2"></i>Sejarah
                        </a>
                        <a href="<?php echo isset($pageInPages) ? '' : 'pages/'; ?>gallery.php" class="block py-2 text-gray-600 hover:text-blue-600">
                            <i class="fas fa-images mr-2"></i>Gallery
                        </a>
                        <a href="<?php echo isset($pageInPages) ? '' : 'pages/'; ?>struktur_organisasi.php" class="block py-2 text-gray-600 hover:text-blue-600">
                            <i class="fas fa-sitemap mr-2"></i>Struktur Organisasi
                        </a>
                        <a href="<?php echo isset($pageInPages) ? '' : 'pages/'; ?>produk.php" class="block py-2 text-gray-600 hover:text-blue-600">
                            <i class="fas fa-box mr-2"></i>Produk
                        </a>
                        <a href="<?php echo isset($pageInPages) ? '' : 'pages/'; ?>partner_sponsor.php" class="block py-2 text-gray-600 hover:text-blue-600">
                            <i class="fas fa-handshake mr-2"></i>Partner & Sponsor
                        </a>
                    </div>
                </div>
                
                <a href="news.php" class="block py-2 text-gray-700 hover:text-blue-600">Berita</a>
                <a href="index.php#contact" class="block py-2 text-gray-700 hover:text-blue-600">Kontak</a>
                <a href="admin/login.php" class="block py-2 text-blue-600 font-semibold">
                    <i class="fas fa-lock mr-2"></i>Admin Login
                </a>
            </div>
        </div>
    </nav>
    
    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
        
        // Mobile dropdown toggle
        document.getElementById('mobile-dropdown-btn').addEventListener('click', function() {
            document.getElementById('mobile-dropdown').classList.toggle('hidden');
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-chevron-down');
            icon.classList.toggle('fa-chevron-up');
        });
        
        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
