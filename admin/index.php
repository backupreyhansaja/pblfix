<?php
require_once 'includes/auth.php';
require_once '../config/database.php';

$pageTitle = 'Dashboard';
$db = new Database();

// Get statistics
$staffCount = $db->numRows($db->query("SELECT id FROM dosen"));
$mahasiswaCount = $db->numRows($db->query("SELECT id FROM dosen WHERE 1=0"));
$beritaCount = $db->numRows($db->query("SELECT id FROM berita"));
$galleryCount = $db->numRows($db->query("SELECT id FROM gallery"));
$messagesCount = $db->numRows($db->query("SELECT id FROM contact_messages WHERE is_read = FALSE"));

include 'includes/header.php';
?>

<!-- Welcome Banner -->
<div class="rounded-xl shadow-lg p-6 md:p-8 mb-6 md:mb-8 text-white" style="background-color : blue">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between">
        <div class="mb-4 md:mb-0">
            <h2 class="text-2xl md:text-3xl font-bold mb-2">Selamat Datang, Admin! 👋</h2>
            <p class="text-purple-100 text-sm md:text-base">Berikut ringkasan data laboratorium Anda</p>
        </div>
        <div class="text-left md:text-right">
            <p class="text-purple-100 text-xs md:text-sm">Terakhir login</p>
            <p class="font-semibold text-sm md:text-base"><?php echo date('d M Y, H:i'); ?></p>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 md:gap-6 mb-6 md:mb-8">
        
    <!-- Mahasiswa Card -->
    <div class="bg-white rounded-xl shadow-md p-4 md:p-6 hover:shadow-lg transition transform hover:scale-105 duration-200">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div class="flex-1 min-w-0">
                <p class="text-gray-600 text-xs md:text-sm mb-1 truncate">Total Mahasiswa</p>
                <h3 class="text-2xl md:text-3xl font-bold text-gray-800"><?php echo $mahasiswaCount; ?></h3>
            </div>
            <div class="w-12 h-12 md:w-14 md:h-14 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 ml-3">
                <i class="fas fa-user-graduate text-green-600 text-xl md:text-2xl"></i>
            </div>
        </div>
        <a href="mahasiswa.php" class="text-green-600 text-xs md:text-sm inline-flex items-center hover:underline">
            Lihat Detail <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>
    
    <!-- Berita Card -->
    <div class="bg-white rounded-xl shadow-md p-4 md:p-6 hover:shadow-lg transition transform hover:scale-105 duration-200">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div class="flex-1 min-w-0">
                <p class="text-gray-600 text-xs md:text-sm mb-1 truncate">Total Berita</p>
                <h3 class="text-2xl md:text-3xl font-bold text-gray-800"><?php echo $beritaCount; ?></h3>
            </div>
            <div class="w-12 h-12 md:w-14 md:h-14 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0 ml-3">
                <i class="fas fa-newspaper text-orange-600 text-xl md:text-2xl"></i>
            </div>
        </div>
        <a href="berita.php" class="text-orange-600 text-xs md:text-sm inline-flex items-center hover:underline">
            Lihat Detail <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>
    
    <!-- Gallery Card -->
    <div class="bg-white rounded-xl shadow-md p-4 md:p-6 hover:shadow-lg transition transform hover:scale-105 duration-200">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div class="flex-1 min-w-0">
                <p class="text-gray-600 text-xs md:text-sm mb-1 truncate">Total Galeri</p>
                <h3 class="text-2xl md:text-3xl font-bold text-gray-800"><?php echo $galleryCount; ?></h3>
            </div>
            <div class="w-12 h-12 md:w-14 md:h-14 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0 ml-3">
                <i class="fas fa-images text-purple-600 text-xl md:text-2xl"></i>
            </div>
        </div>
        <a href="gallery.php" class="text-purple-600 text-xs md:text-sm inline-flex items-center hover:underline">
            Lihat Detail <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>
    
    <!-- Messages Card -->
    <div class="bg-white rounded-xl shadow-md p-4 md:p-6 hover:shadow-lg transition transform hover:scale-105 duration-200">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div class="flex-1 min-w-0">    
                <p class="text-gray-600 text-xs md:text-sm mb-1 truncate">Pesan Baru</p>
                <h3 class="text-2xl md:text-3xl font-bold text-gray-800"><?php echo $messagesCount; ?></h3>
            </div>
            <div class="w-12 h-12 md:w-14 md:h-14 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0 ml-3">
                <i class="fas fa-envelope text-red-600 text-xl md:text-2xl"></i>
            </div>
        </div>
        <a href="messages.php" class="text-red-600 text-xs md:text-sm inline-flex items-center hover:underline">
            Lihat Detail <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>
</div>

<!-- Recent Messages -->
<div class="bg-white rounded-xl shadow-md p-4 md:p-6">
    <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-4 md:mb-6 flex items-center">
        <i class="fas fa-envelope text-blue-500 mr-2"></i>
        <span>Pesan Terbaru</span>
    </h3>
    <?php
    $recentMessages = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5");
    $messages = $db->fetchAll($recentMessages);
    
    if ($messages && count($messages) > 0):
    ?>
        <div class="space-y-3">
            <?php foreach ($messages as $msg): ?>
                <div class="flex items-start p-3 md:p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-purple-600 rounded-full flex items-center justify-center text-white mr-3 md:mr-4 flex-shrink-0">
                        <i class="fas fa-user text-xs md:text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-1 gap-1">
                            <h4 class="font-semibold text-gray-800 text-sm md:text-base truncate"><?php echo htmlspecialchars($msg['name']); ?></h4>
                            <span class="text-xs text-gray-500 flex-shrink-0"><?php echo date('d M Y', strtotime($msg['created_at'])); ?></span>
                        </div>
                        <p class="text-xs md:text-sm text-gray-600 mb-1 truncate"><?php echo htmlspecialchars($msg['email']); ?></p>
                        <p class="text-xs md:text-sm text-gray-700 line-clamp-2"><?php echo htmlspecialchars($msg['message']); ?></p>
                    </div>
                    <?php if (!$msg['is_read']): ?>
                        <span class="ml-2 w-2 h-2 bg-red-500 rounded-full flex-shrink-0 mt-1"></span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <a href="messages.php" class="block text-center mt-4 text-purple-600 hover:text-purple-700 font-semibold text-sm md:text-base">
            Lihat Semua Pesan <i class="fas fa-arrow-right ml-1"></i>
        </a>
    <?php else: ?>
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-inbox text-3xl md:text-4xl mb-3"></i>
            <p class="text-sm md:text-base">Belum ada pesan</p>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
