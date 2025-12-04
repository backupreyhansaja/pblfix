<?php
require_once '../config/database.php';
$pageTitle = 'Visi & Misi';
$pageInPages = true;

// Fetch data
$db = new Database();
$result = $db->query("SELECT * FROM content_dashboard WHERE type = 'visi_misi' LIMIT 1");
$row = $db->fetch($result);

$visi = '';
$misi = '';

if ($row && !empty($row['data'])) {
    $data = json_decode($row['data'], true);
    if ($data) {
        $visi = $data['visi'] ?? '';
        $misi = $data['misi'] ?? '';
    }
}

include '../includes/header.php';
?>

<!-- Page Header -->
<section class="relative py-20 bg-gradient-to-br from-blue-600 to-blue-800 text-white">
    <div class="container mx-auto px-6 text-center">
        <div data-aos="fade-up">
            <i class="fas fa-eye text-6xl mb-4 opacity-90"></i>
            <h1 class="text-5xl md:text-6xl font-bold mb-4">Visi & Misi</h1>
            <p class="text-xl text-blue-100 max-w-2xl mx-auto">Pandangan dan Tujuan Laboratorium Kami</p>
        </div>
    </div>
</section>

<!-- Visi Misi Content -->
<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="max-w-5xl mx-auto">
            <?php if (!empty($visi) || !empty($misi)): ?>
                <!-- Visi Section -->
                <?php if (!empty($visi)): ?>
                    <div class="mb-16" data-aos="fade-up">
                        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border-t-4 border-blue-600">
                            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
                                <div class="flex items-center">
                                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-4">
                                        <i class="fas fa-eye text-white text-2xl"></i>
                                    </div>
                                    <h2 class="text-3xl font-bold text-white">VISI</h2>
                                </div>
                            </div>
                            <div class="px-8 py-10">
                                <p class="text-gray-700 text-lg leading-relaxed text-center italic">
                                    "<?php echo nl2br(htmlspecialchars($visi)); ?>"
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Misi Section -->
                <?php if (!empty($misi)): ?>
                    <div data-aos="fade-up" data-aos-delay="200">
                        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border-t-4 border-blue-600">
                            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
                                <div class="flex items-center">
                                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-4">
                                        <i class="fas fa-bullseye text-white text-2xl"></i>
                                    </div>
                                    <h2 class="text-3xl font-bold text-white">MISI</h2>
                                </div>
                            </div>
                            <div class="px-8 py-10">
                                <div class="text-gray-700 text-lg leading-relaxed space-y-4">
                                    <?php 
                                    // Split misi by new lines and format as list
                                    $misiLines = explode("\n", $misi);
                                    $counter = 1;
                                    foreach ($misiLines as $line):
                                        $line = trim($line);
                                        if (!empty($line)):
                                            // Check if line already starts with number
                                            if (preg_match('/^\d+\.?\s/', $line)):
                                                echo '<div class="flex items-start mb-3">';
                                                echo '<div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4 mt-1">';
                                                echo '<span class="text-blue-600 font-bold">' . $counter . '</span>';
                                                echo '</div>';
                                                echo '<p class="flex-1 pt-2">' . htmlspecialchars($line) . '</p>';
                                                echo '</div>';
                                                $counter++;
                                            else:
                                                echo '<div class="flex items-start mb-3">';
                                                echo '<div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4 mt-1">';
                                                echo '<i class="fas fa-check text-blue-600"></i>';
                                                echo '</div>';
                                                echo '<p class="flex-1 pt-2">' . htmlspecialchars($line) . '</p>';
                                                echo '</div>';
                                            endif;
                                        endif;
                                    endforeach;
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <!-- Empty State -->
                <div class="text-center py-20">
                    <div class="w-32 h-32 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-eye text-gray-400 text-6xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-700 mb-2">Belum Ada Visi & Misi</h3>
                    <p class="text-gray-500">Visi dan Misi laboratorium akan ditampilkan di sini</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-gradient-to-br from-blue-600 to-blue-800 text-white">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-3xl font-bold mb-4" data-aos="fade-up">Mari Bergabung Bersama Kami</h2>
        <p class="text-blue-100 mb-6 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">
            Bersama kita wujudkan visi dan misi laboratorium untuk kemajuan penelitian dan inovasi
        </p>
        <a href="../index.php#contact" class="inline-block px-8 py-4 bg-white text-blue-600 rounded-lg font-semibold hover-scale shadow-lg" data-aos="fade-up" data-aos-delay="200">
            <i class="fas fa-envelope mr-2"></i>Hubungi Kami
        </a>
    </div>
</section>

<?php include '../includes/footer.php'; ?>


