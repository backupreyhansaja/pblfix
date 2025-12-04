<?php
require_once '../config/database.php';
$pageTitle = 'Gallery';
$pageInPages = true;

// Fetch data
$db = new Database();

// Get Gallery with JOIN to files
$galleryResult = $db->query("
    SELECT 
        g.*,
        (f.path || '/' || f.filename) AS image_path
    FROM gallery g
    LEFT JOIN files f ON g.image_id = f.id
    ORDER BY g.created_at DESC
");
$gallery = $db->fetchAll($galleryResult);

include '../includes/header.php';
?>

<!-- Page Header -->
<section class="relative py-20 bg-gradient-to-br from-blue-600 to-blue-800 text-white">
    <div class="container mx-auto px-6 text-center">
        <div data-aos="fade-up">
            <i class="fas fa-images text-6xl mb-4 opacity-90"></i>
            <h1 class="text-5xl md:text-6xl font-bold mb-4">Gallery</h1>
            <p class="text-xl text-blue-100 max-w-2xl mx-auto">Dokumentasi Kegiatan dan Aktivitas Laboratorium</p>
        </div>
    </div>
</section>

<!-- Gallery Grid Section -->
<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-6">
        <?php if (!empty($gallery)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($gallery as $item): ?>
                    <div class="card-hover bg-white rounded-xl shadow-lg overflow-hidden" data-aos="fade-up">
                        <div class="relative overflow-hidden group">
                            <?php 
                            $imageFile = !empty($item['image_path']) ? $item['image_path'] : '';
                            $title = $item['title'] ?? '';
                            $description = $item['description'] ?? '';
                            ?>
                            <?php if ($imageFile): ?>
                                <img src="../<?php echo htmlspecialchars($imageFile); ?>" 
                                     alt="<?php echo htmlspecialchars($title); ?>"
                                     class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-110">
                            <?php else: ?>
                                <div class="w-full h-64 bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                                    <i class="fas fa-image text-white text-6xl opacity-50"></i>
                                </div>
                            <?php endif; ?>
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center">
                                <button onclick="openModal('<?php echo htmlspecialchars($imageFile); ?>', '<?php echo htmlspecialchars(addslashes($title)); ?>', '<?php echo htmlspecialchars(addslashes($description)); ?>')" 
                                        class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-600 hover:text-white">
                                    <i class="fas fa-search-plus mr-2"></i>Lihat Detail
                                </button>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-2 text-gray-800"><?php echo htmlspecialchars($title); ?></h3>
                            <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($description); ?></p>
                            <?php if (!empty($item['tanggal'])): ?>
                                <p class="text-blue-600 text-sm mt-3">
                                    <i class="far fa-calendar mr-2"></i><?php echo date('d F Y', strtotime($item['tanggal'])); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-20">
                <div class="w-32 h-32 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-images text-gray-400 text-6xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-700 mb-2">Belum Ada Gallery</h3>
                <p class="text-gray-500">Dokumentasi kegiatan akan ditampilkan di sini</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Modal for Image Preview -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden flex items-center justify-center p-4">
    <div class="relative max-w-5xl w-full">
        <button onclick="closeModal()" class="absolute top-4 right-4 text-white text-4xl hover:text-gray-300 z-10">
            <i class="fas fa-times"></i>
        </button>
        <div class="bg-white rounded-lg overflow-hidden">
            <img id="modalImage" src="" alt="" class="w-full max-h-[70vh] object-contain">
            <div class="p-6">
                <h3 id="modalTitle" class="text-2xl font-bold mb-2 text-gray-800"></h3>
                <p id="modalDescription" class="text-gray-600"></p>
            </div>
        </div>
    </div>
</div>

<script>
function openModal(image, title, description) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const modalTitle = document.getElementById('modalTitle');
    const modalDescription = document.getElementById('modalDescription');
    
    // Check if image already has path prefix
    modalImage.src = image.startsWith('uploads/') ? '../' + image : image;
    modalTitle.textContent = title;
    modalDescription.textContent = description;
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});

// Close modal on background click
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>

<?php include '../includes/footer.php'; ?>

