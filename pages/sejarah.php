<?php
require_once '../config/database.php';
$pageTitle = 'Sejarah';
$pageInPages = true;

// Fetch data
$db = new Database();
$result = $db->query("SELECT * FROM content_dashboard WHERE type = 'sejarah' LIMIT 1");
$row = $db->fetch($result);

$content = '';

if ($row && !empty($row['data'])) {
    $data = json_decode($row['data'], true);
    if ($data) {
        $content = $data['content'] ?? '';
    }
}

include '../includes/header.php';
?>

<!-- Page Header -->
<section class="relative py-20 bg-gradient-to-br from-blue-600 to-blue-800 text-white">
    <div class="container mx-auto px-6 text-center">
        <div data-aos="fade-up">
            <i class="fas fa-book text-6xl mb-4 opacity-90"></i>
            <h1 class="text-5xl md:text-6xl font-bold mb-4">Sejarah</h1>
            <p class="text-xl text-blue-100 max-w-2xl mx-auto">Perjalanan dan Perkembangan Laboratorium Kami</p>
        </div>
    </div>
</section>

<!-- Sejarah Content -->
<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="max-w-4xl mx-auto">
            <?php if (!empty($content)): ?>
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden" data-aos="fade-up">
                    <!-- Decorative Header -->
                    <div class="h-2 bg-gradient-to-r from-blue-600 to-blue-800"></div>
                    
                    <!-- Content -->
                    <div class="px-8 md:px-12 py-12">
                        <!-- Timeline Icon -->
                        <div class="flex justify-center mb-8">
                            <div class="w-20 h-20 bg-gradient-to-br from-blue-600 to-blue-800 rounded-full flex items-center justify-center shadow-lg">
                                <i class="fas fa-clock text-white text-3xl"></i>
                            </div>
                        </div>

                        <!-- Main Content -->
                        <div class="prose prose-lg max-w-none">
                            <?php 
                            // Split content by double line breaks (paragraphs)
                            $paragraphs = preg_split('/\n\s*\n/', $content);
                            
                            foreach ($paragraphs as $index => $paragraph):
                                $paragraph = trim($paragraph);
                                if (!empty($paragraph)):
                            ?>
                                <div class="mb-6 relative pl-8 border-l-4 border-blue-200" data-aos="fade-up" data-aos-delay="<?php echo ($index * 100); ?>">
                                    <div class="absolute -left-3 top-2 w-5 h-5 bg-blue-600 rounded-full border-4 border-white"></div>
                                    <p class="text-gray-700 text-lg leading-relaxed text-justify">
                                        <?php echo nl2br(htmlspecialchars($paragraph)); ?>
                                    </p>
                                </div>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>

                        <!-- Quote Section -->
                        <div class="mt-12 bg-blue-50 border-l-4 border-blue-600 rounded-r-lg px-8 py-6" data-aos="fade-up">
                            <div class="flex items-start">
                                <i class="fas fa-quote-left text-blue-600 text-3xl mr-4 mt-2"></i>
                                <p class="text-gray-700 text-lg italic">
                                    Sejarah adalah guru terbaik yang mengajarkan kita untuk terus berinovasi dan berkembang
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Empty State -->
                <div class="text-center py-20">
                    <div class="w-32 h-32 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-book text-gray-400 text-6xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-700 mb-2">Belum Ada Sejarah</h3>
                    <p class="text-gray-500">Sejarah laboratorium akan ditampilkan di sini</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-6">
        <div class="max-w-5xl mx-auto">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-12" data-aos="fade-up">Pencapaian Kami</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="text-center" data-aos="fade-up" data-aos-delay="0">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-600 to-blue-800 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-trophy text-white text-2xl"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-blue-600 mb-2">50+</h3>
                    <p class="text-gray-600 text-sm">Penghargaan</p>
                </div>
                <div class="text-center" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-600 to-blue-800 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-flask text-white text-2xl"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-blue-600 mb-2">200+</h3>
                    <p class="text-gray-600 text-sm">Penelitian</p>
                </div>
                <div class="text-center" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-600 to-blue-800 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-blue-600 mb-2">500+</h3>
                    <p class="text-gray-600 text-sm">Mahasiswa</p>
                </div>
                <div class="text-center" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-600 to-blue-800 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-handshake text-white text-2xl"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-blue-600 mb-2">30+</h3>
                    <p class="text-gray-600 text-sm">Kerjasama</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-gradient-to-br from-blue-600 to-blue-800 text-white">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-3xl font-bold mb-4" data-aos="fade-up">Mari Jadi Bagian dari Sejarah</h2>
        <p class="text-blue-100 mb-6 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">
            Bergabunglah dengan kami dan ciptakan sejarah baru dalam dunia penelitian dan inovasi
        </p>
        <a href="../index.php#contact" class="inline-block px-8 py-4 bg-white text-blue-600 rounded-lg font-semibold hover-scale shadow-lg" data-aos="fade-up" data-aos-delay="200">
            <i class="fas fa-envelope mr-2"></i>Hubungi Kami
        </a>
    </div>
</section>

<?php include '../includes/footer.php'; ?>


