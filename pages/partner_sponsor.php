<?php
require_once '../config/database.php';
$pageTitle = 'Partner & Sponsor';
$pageInPages = true;

// Fetch data
$db = new Database();

// Get Partners
$partnerResult = $db->query("SELECT * FROM partners ORDER BY created_at DESC");
$partners = $db->fetchAll($partnerResult);

// Get Sponsors
$sponsorResult = $db->query("SELECT * FROM sponsors ORDER BY created_at DESC");
$sponsors = $db->fetchAll($sponsorResult);

include '../includes/header.php';
?>

<!-- Page Header -->
<section class="relative py-20 bg-gradient-to-br from-blue-600 to-blue-800 text-white">
    <div class="container mx-auto px-6 text-center">
        <div data-aos="fade-up">
            <i class="fas fa-handshake text-6xl mb-4 opacity-90"></i>
            <h1 class="text-5xl md:text-6xl font-bold mb-4">Partner & Sponsor</h1>
            <p class="text-xl text-blue-100 max-w-2xl mx-auto">Mitra dan Sponsor yang Mendukung Kegiatan Laboratorium</p>
        </div>
    </div>
</section>

<!-- Partners Section -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl font-bold mb-4 text-gray-800">Our Partners</h2>
            <div class="w-24 h-1 bg-blue-600 mx-auto mb-4"></div>
            <p class="text-gray-600 max-w-2xl mx-auto">Mitra strategis yang berkolaborasi dalam penelitian dan pengembangan</p>
        </div>
        
        <?php if (!empty($partners)): ?>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8 max-w-6xl mx-auto">
                <?php foreach ($partners as $partner): ?>
                    <div class="card-hover bg-white rounded-xl shadow-lg p-6 flex items-center justify-center border-2 border-gray-100" data-aos="fade-up">
                        <?php if (!empty($partner['logo'])): ?>
                            <img src="../uploads/partners/<?php echo htmlspecialchars($partner['logo']); ?>" 
                                 alt="<?php echo htmlspecialchars($partner['nama']); ?>"
                                 class="max-w-full max-h-24 object-contain">
                        <?php else: ?>
                            <div class="text-center">
                                <i class="fas fa-building text-4xl text-blue-600 mb-2"></i>
                                <p class="text-sm font-semibold text-gray-800"><?php echo htmlspecialchars($partner['nama']); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Default Partners -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8 max-w-6xl mx-auto">
                <div class="card-hover bg-white rounded-xl shadow-lg p-6 flex items-center justify-center border-2 border-gray-100" data-aos="fade-up">
                    <div class="text-center">
                        <i class="fas fa-university text-4xl text-blue-600 mb-2"></i>
                        <p class="text-sm font-semibold text-gray-800">Universitas Indonesia</p>
                    </div>
                </div>
                <div class="card-hover bg-white rounded-xl shadow-lg p-6 flex items-center justify-center border-2 border-gray-100" data-aos="fade-up" data-aos-delay="100">
                    <div class="text-center">
                        <i class="fas fa-university text-4xl text-blue-600 mb-2"></i>
                        <p class="text-sm font-semibold text-gray-800">Institut Teknologi Bandung</p>
                    </div>
                </div>
                <div class="card-hover bg-white rounded-xl shadow-lg p-6 flex items-center justify-center border-2 border-gray-100" data-aos="fade-up" data-aos-delay="200">
                    <div class="text-center">
                        <i class="fas fa-industry text-4xl text-green-600 mb-2"></i>
                        <p class="text-sm font-semibold text-gray-800">PT. Teknologi Digital</p>
                    </div>
                </div>
                <div class="card-hover bg-white rounded-xl shadow-lg p-6 flex items-center justify-center border-2 border-gray-100" data-aos="fade-up" data-aos-delay="300">
                    <div class="text-center">
                        <i class="fas fa-building text-4xl text-purple-600 mb-2"></i>
                        <p class="text-sm font-semibold text-gray-800">CV. Inovasi Sistem</p>
                    </div>
                </div>
                <div class="card-hover bg-white rounded-xl shadow-lg p-6 flex items-center justify-center border-2 border-gray-100" data-aos="fade-up" data-aos-delay="400">
                    <div class="text-center">
                        <i class="fas fa-laptop-code text-4xl text-red-600 mb-2"></i>
                        <p class="text-sm font-semibold text-gray-800">Tech Startup Hub</p>
                    </div>
                </div>
                <div class="card-hover bg-white rounded-xl shadow-lg p-6 flex items-center justify-center border-2 border-gray-100" data-aos="fade-up" data-aos-delay="500">
                    <div class="text-center">
                        <i class="fas fa-network-wired text-4xl text-indigo-600 mb-2"></i>
                        <p class="text-sm font-semibold text-gray-800">IoT Solutions Inc</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Sponsors Section -->
<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl font-bold mb-4 text-gray-800">Our Sponsors</h2>
            <div class="w-24 h-1 bg-blue-600 mx-auto mb-4"></div>
            <p class="text-gray-600 max-w-2xl mx-auto">Sponsor yang mendukung kegiatan dan pengembangan laboratorium</p>
        </div>
        
        <?php if (!empty($sponsors)): ?>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 max-w-6xl mx-auto">
                <?php foreach ($sponsors as $sponsor): ?>
                    <div class="card-hover bg-white rounded-xl shadow-lg p-6 flex items-center justify-center" data-aos="fade-up">
                        <?php if (!empty($sponsor['logo'])): ?>
                            <img src="../uploads/sponsors/<?php echo htmlspecialchars($sponsor['logo']); ?>" 
                                 alt="<?php echo htmlspecialchars($sponsor['nama']); ?>"
                                 class="max-w-full max-h-20 object-contain grayscale hover:grayscale-0 transition">
                        <?php else: ?>
                            <div class="text-center">
                                <i class="fas fa-gift text-3xl text-yellow-600 mb-2"></i>
                                <p class="text-xs font-semibold text-gray-800"><?php echo htmlspecialchars($sponsor['nama']); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Default Sponsors -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 max-w-6xl mx-auto">
                <div class="card-hover bg-white rounded-xl shadow-lg p-6 flex items-center justify-center" data-aos="fade-up">
                    <div class="text-center">
                        <i class="fas fa-microchip text-3xl text-blue-600 mb-2"></i>
                        <p class="text-xs font-semibold text-gray-800">Intel Corp</p>
                    </div>
                </div>
                <div class="card-hover bg-white rounded-xl shadow-lg p-6 flex items-center justify-center" data-aos="fade-up" data-aos-delay="100">
                    <div class="text-center">
                        <i class="fab fa-microsoft text-3xl text-blue-500 mb-2"></i>
                        <p class="text-xs font-semibold text-gray-800">Microsoft</p>
                    </div>
                </div>
                <div class="card-hover bg-white rounded-xl shadow-lg p-6 flex items-center justify-center" data-aos="fade-up" data-aos-delay="200">
                    <div class="text-center">
                        <i class="fab fa-google text-3xl text-red-500 mb-2"></i>
                        <p class="text-xs font-semibold text-gray-800">Google</p>
                    </div>
                </div>
                <div class="card-hover bg-white rounded-xl shadow-lg p-6 flex items-center justify-center" data-aos="fade-up" data-aos-delay="300">
                    <div class="text-center">
                        <i class="fab fa-aws text-3xl text-orange-500 mb-2"></i>
                        <p class="text-xs font-semibold text-gray-800">AWS</p>
                    </div>
                </div>
                <div class="card-hover bg-white rounded-xl shadow-lg p-6 flex items-center justify-center" data-aos="fade-up" data-aos-delay="400">
                    <div class="text-center">
                        <i class="fas fa-server text-3xl text-green-600 mb-2"></i>
                        <p class="text-xs font-semibold text-gray-800">IBM</p>
                    </div>
                </div>
                <div class="card-hover bg-white rounded-xl shadow-lg p-6 flex items-center justify-center" data-aos="fade-up" data-aos-delay="500">
                    <div class="text-center">
                        <i class="fab fa-docker text-3xl text-blue-400 mb-2"></i>
                        <p class="text-xs font-semibold text-gray-800">Docker</p>
                    </div>
                </div>
                <div class="card-hover bg-white rounded-xl shadow-lg p-6 flex items-center justify-center" data-aos="fade-up" data-aos-delay="600">
                    <div class="text-center">
                        <i class="fas fa-database text-3xl text-purple-600 mb-2"></i>
                        <p class="text-xs font-semibold text-gray-800">Oracle</p>
                    </div>
                </div>
                <div class="card-hover bg-white rounded-xl shadow-lg p-6 flex items-center justify-center" data-aos="fade-up" data-aos-delay="700">
                    <div class="text-center">
                        <i class="fab fa-github text-3xl text-gray-800 mb-2"></i>
                        <p class="text-xs font-semibold text-gray-800">GitHub</p>
                    </div>
                </div>
                <div class="card-hover bg-white rounded-xl shadow-lg p-6 flex items-center justify-center" data-aos="fade-up" data-aos-delay="800">
                    <div class="text-center">
                        <i class="fas fa-cloud text-3xl text-blue-300 mb-2"></i>
                        <p class="text-xs font-semibold text-gray-800">Azure</p>
                    </div>
                </div>
                <div class="card-hover bg-white rounded-xl shadow-lg p-6 flex items-center justify-center" data-aos="fade-up" data-aos-delay="900">
                    <div class="text-center">
                        <i class="fab fa-node-js text-3xl text-green-500 mb-2"></i>
                        <p class="text-xs font-semibold text-gray-800">Node Foundation</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Collaboration Benefits Section -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl font-bold mb-4 text-gray-800">Keuntungan Berkolaborasi</h2>
            <div class="w-24 h-1 bg-blue-600 mx-auto mb-4"></div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 max-w-6xl mx-auto">
            <div class="text-center" data-aos="fade-up">
                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-lightbulb text-blue-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-gray-800">Inovasi Bersama</h3>
                <p class="text-gray-600">Mengembangkan solusi inovatif melalui kolaborasi research & development</p>
            </div>
            
            <div class="text-center" data-aos="fade-up" data-aos-delay="100">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users-cog text-green-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-gray-800">Knowledge Transfer</h3>
                <p class="text-gray-600">Berbagi pengetahuan dan expertise antar institusi</p>
            </div>
            
            <div class="text-center" data-aos="fade-up" data-aos-delay="200">
                <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-chart-line text-purple-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-gray-800">Peningkatan Kapasitas</h3>
                <p class="text-gray-600">Meningkatkan kapasitas SDM dan infrastruktur</p>
            </div>
            
            <div class="text-center" data-aos="fade-up" data-aos-delay="300">
                <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-award text-yellow-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-gray-800">Publikasi & Sertifikasi</h3>
                <p class="text-gray-600">Kesempatan publikasi penelitian dan sertifikasi</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-gradient-to-br from-blue-600 to-blue-800 text-white">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-3xl font-bold mb-4" data-aos="fade-up">Tertarik Menjadi Partner atau Sponsor?</h2>
        <p class="text-blue-100 mb-6 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">
            Mari bergabung dan berkontribusi dalam pengembangan teknologi dan inovasi bersama kami
        </p>
        <div class="flex justify-center space-x-4" data-aos="fade-up" data-aos-delay="200">
            <a href="../index.php#contact" class="inline-block px-8 py-4 bg-white text-blue-600 rounded-lg font-semibold hover-scale shadow-lg">
                <i class="fas fa-envelope mr-2"></i>Hubungi Kami
            </a>
            <a href="mailto:partnership@lab.ac.id" class="inline-block px-8 py-4 bg-transparent border-2 border-white text-white rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition">
                <i class="fas fa-handshake mr-2"></i>Partnership Inquiry
            </a>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>

