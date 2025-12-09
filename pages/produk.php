<?php
require_once '../config/database.php';
$pageTitle = 'Produk';
$pageInPages = true;

// Fetch data
$db = new Database();

// Get Produk (only fields we need)
$produkResult = $db->query("SELECT id, nama, gambar, link, deskripsi FROM produk ORDER BY created_at DESC");
$produk = $db->fetchAll($produkResult);

include '../includes/header.php';
?>

<!-- Page Header -->
<section class="relative py-20 bg-gradient-to-br from-blue-600 to-blue-800 text-white">
    <div class="container mx-auto px-6 text-center">
        <div data-aos="fade-up">
            <i class="fas fa-box text-6xl mb-4 opacity-90"></i>
            <h1 class="text-5xl md:text-6xl font-bold mb-4">Produk Kami</h1>
            <p class="text-xl text-blue-100 max-w-2xl mx-auto">Hasil Penelitian dan Inovasi Laboratorium</p>
        </div>
    </div>
</section>

<!-- Products Section -->
<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-6">
        <?php if (!empty($produk)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($produk as $item): ?>
                    <div class="card-hover bg-white rounded-xl shadow-lg overflow-hidden" data-aos="fade-up">
                        <!-- Clickable product area -->
                        <?php $hasLink = !empty($item['link']); ?>
                        <?php if ($hasLink): ?>
                            <a href="<?= htmlspecialchars($item['link']) ?>" target="_blank" class="block group">
                        <?php else: ?>
                            <div class="block group">
                        <?php endif; ?>

                        <div class="relative overflow-hidden h-56 flex items-center justify-center bg-gray-100">
                            <?php if (!empty($item['gambar'])): ?>
                                <img src="../uploads/produk/<?= htmlspecialchars($item['gambar']) ?>" 
                                     alt="<?= htmlspecialchars($item['nama']) ?>"
                                     class="max-h-48 object-contain transition-transform duration-300 group-hover:scale-105">
                            <?php else: ?>
                                <div class="w-full h-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                                    <i class="fas fa-box-open text-white text-6xl opacity-50"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="p-4">
                            <h3 class="text-lg font-bold mb-2 text-gray-800">
                                <?= $hasLink ? '<span class="underline">' . htmlspecialchars($item['nama']) . '</span>' : htmlspecialchars($item['nama']) ?>
                            </h3>
                            <?php if (!empty($item['deskripsi'])): ?>
                                <p class="text-gray-600 text-sm mb-2 line-clamp-3"><?= htmlspecialchars($item['deskripsi']) ?></p>
                            <?php endif; ?>
                            <?php if ($hasLink): ?>
                                <div class="mt-2">
                                    <a href="<?= htmlspecialchars($item['link']) ?>" target="_blank" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                        <i class="fas fa-external-link-alt mr-2"></i>Buka Produk
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($hasLink): ?>
                            </a>
                        <?php else: ?>
                            </div>
                        <?php endif; ?>

                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Empty State with Default Products -->
            <div class="max-w-6xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Product 1: LMS -->
                    <div class="card-hover bg-white rounded-xl shadow-lg overflow-hidden" data-aos="fade-up">
                        <div class="relative overflow-hidden group h-64 bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                            <i class="fas fa-graduation-cap text-white text-6xl opacity-50"></i>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-3 text-gray-800">Learning Management System</h3>
                            <p class="text-gray-600 text-sm mb-4">Platform pembelajaran online yang interaktif dan adaptif untuk mendukung kegiatan pembelajaran di institusi pendidikan.</p>
                            <div class="mb-4">
                                <div class="flex flex-wrap gap-2">
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">PHP</span>
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">Laravel</span>
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">Vue.js</span>
                                </div>
                            </div>
                            <span class="inline-flex items-center text-green-600 text-sm">
                                <i class="fas fa-check-circle mr-2"></i>Completed
                            </span>
                        </div>
                    </div>
                    
                    <!-- Product 2: Smart Farming -->
                    <div class="card-hover bg-white rounded-xl shadow-lg overflow-hidden" data-aos="fade-up" data-aos-delay="100">
                        <div class="relative overflow-hidden group h-64 bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center">
                            <i class="fas fa-leaf text-white text-6xl opacity-50"></i>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-3 text-gray-800">Smart Farming IoT System</h3>
                            <p class="text-gray-600 text-sm mb-4">Sistem monitoring dan kontrol pertanian berbasis IoT untuk optimalisasi hasil panen dan efisiensi sumber daya.</p>
                            <div class="mb-4">
                                <div class="flex flex-wrap gap-2">
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">IoT</span>
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">Arduino</span>
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">Python</span>
                                </div>
                            </div>
                            <span class="inline-flex items-center text-blue-600 text-sm">
                                <i class="fas fa-sync-alt mr-2"></i>Ongoing
                            </span>
                        </div>
                    </div>
                    
                    <!-- Product 3: Blockchain System -->
                    <div class="card-hover bg-white rounded-xl shadow-lg overflow-hidden" data-aos="fade-up" data-aos-delay="200">
                        <div class="relative overflow-hidden group h-64 bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center">
                            <i class="fas fa-link text-white text-6xl opacity-50"></i>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-3 text-gray-800">Blockchain Supply Chain</h3>
                            <p class="text-gray-600 text-sm mb-4">Sistem supply chain terdesentralisasi menggunakan teknologi blockchain untuk transparansi dan keamanan data.</p>
                            <div class="mb-4">
                                <div class="flex flex-wrap gap-2">
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">Ethereum</span>
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">Solidity</span>
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">Web3</span>
                                </div>
                            </div>
                            <span class="inline-flex items-center text-green-600 text-sm">
                                <i class="fas fa-check-circle mr-2"></i>Completed
                            </span>
                        </div>
                    </div>
                    
                    <!-- Product 4: AI Assistant -->
                    <div class="card-hover bg-white rounded-xl shadow-lg overflow-hidden" data-aos="fade-up" data-aos-delay="300">
                        <div class="relative overflow-hidden group h-64 bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center">
                            <i class="fas fa-brain text-white text-6xl opacity-50"></i>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-3 text-gray-800">AI Learning Assistant</h3>
                            <p class="text-gray-600 text-sm mb-4">Asisten pembelajaran berbasis AI yang dapat membantu siswa dalam memahami materi dan memberikan rekomendasi pembelajaran.</p>
                            <div class="mb-4">
                                <div class="flex flex-wrap gap-2">
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">Machine Learning</span>
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">TensorFlow</span>
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">NLP</span>
                                </div>
                            </div>
                            <span class="inline-flex items-center text-blue-600 text-sm">
                                <i class="fas fa-sync-alt mr-2"></i>Ongoing
                            </span>
                        </div>
                    </div>
                    
                    <!-- Product 5: SIEM Security -->
                    <div class="card-hover bg-white rounded-xl shadow-lg overflow-hidden" data-aos="fade-up" data-aos-delay="400">
                        <div class="relative overflow-hidden group h-64 bg-gradient-to-br from-yellow-400 to-yellow-600 flex items-center justify-center">
                            <i class="fas fa-shield-alt text-white text-6xl opacity-50"></i>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-3 text-gray-800">SIEM Security System</h3>
                            <p class="text-gray-600 text-sm mb-4">Sistem keamanan berbasis Wazuh untuk monitoring, deteksi ancaman, dan manajemen keamanan informasi.</p>
                            <div class="mb-4">
                                <div class="flex flex-wrap gap-2">
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">Wazuh</span>
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">Security</span>
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">Monitoring</span>
                                </div>
                            </div>
                            <span class="inline-flex items-center text-yellow-600 text-sm">
                                <i class="fas fa-clock mr-2"></i>Planning
                            </span>
                        </div>
                    </div>
                    
                    <!-- Product 6: Mobile App -->
                    <div class="card-hover bg-white rounded-xl shadow-lg overflow-hidden" data-aos="fade-up" data-aos-delay="500">
                        <div class="relative overflow-hidden group h-64 bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center">
                            <i class="fas fa-mobile-alt text-white text-6xl opacity-50"></i>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-3 text-gray-800">Infrastructure Reporting App</h3>
                            <p class="text-gray-600 text-sm mb-4">Aplikasi mobile untuk pelaporan kondisi infrastruktur berbasis digital map untuk pemerintah daerah.</p>
                            <div class="mb-4">
                                <div class="flex flex-wrap gap-2">
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">Flutter</span>
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">Google Maps</span>
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">Firebase</span>
                                </div>
                            </div>
                            <span class="inline-flex items-center text-green-600 text-sm">
                                <i class="fas fa-check-circle mr-2"></i>Completed
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-gradient-to-br from-blue-600 to-blue-800 text-white">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-3xl font-bold mb-4" data-aos="fade-up">Tertarik dengan Produk Kami?</h2>
        <p class="text-blue-100 mb-6 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">
            Hubungi kami untuk informasi lebih lanjut tentang produk dan layanan kolaborasi
        </p>
        <a href="../index.php#contact" class="inline-block px-8 py-4 bg-white text-blue-600 rounded-lg font-semibold hover-scale shadow-lg" data-aos="fade-up" data-aos-delay="200">
            <i class="fas fa-envelope mr-2"></i>Hubungi Kami
        </a>
    </div>
</section>

<?php include '../includes/footer.php'; ?>

