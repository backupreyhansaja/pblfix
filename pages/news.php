<?php
require_once '../config/database.php';
$pageInPages = true;
$db = new Database();

/* -----------------------------
   PAGINATION SETUP
-------------------------------*/
$perPage = 5; 
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $perPage;

/* TOTAL DATA */
$countResult = $db->query("SELECT COUNT(*) AS total FROM berita");
$countRow = $db->fetch($countResult);
$totalData = $countRow['total'];
$totalPages = max(1, ceil($totalData / $perPage));

/* AMBIL BERITA PER PAGE */
$sql = "
    SELECT 
        b.*, 
        a.full_name AS uploader,
        (f.path || '/' || f.filename) AS gambar_path
    FROM berita b
    LEFT JOIN admin_users a ON b.uploaded_by = a.id
    LEFT JOIN files f ON b.image_id = f.id
    ORDER BY b.tanggal DESC, b.created_at DESC
    LIMIT $perPage OFFSET $offset
";

$result = $db->query($sql);
$berita = $db->fetchAll($result);

/* Format tanggal */
function tglIndo($date) {
    return date('d M Y', strtotime($date));
}

include '../includes/header.php';
?>

<section class="py-16 bg-gray-50">
    <div class="max-w-6xl mx-auto px-4">
        <h1 class="text-4xl font-extrabold text-center text-blue-700 mb-3">
            Semua Berita & Pengumuman
        </h1>
        <p class="text-center text-gray-600 mb-10">
            Informasi terbaru dan pengumuman penting dari laboratorium
        </p>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($berita as $b): ?>
                <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition">
                    <div class="h-40 overflow-hidden">
                        <?php if (!empty($b['gambar_path'])): ?>
                            <img src="../<?= htmlspecialchars($b['gambar_path']); ?>" class="w-full h-40 object-cover rounded-lg" alt="Gambar Berita">
                        <?php else: ?>
                            <div class="w-full h-80 bg-gradient-to-br from-blue-500 to-blue-700 rounded-lg flex items-center justify-center">
                                <i class="fas fa-newspaper text-white text-7xl"></i>
                            </div>
                        <?php endif; ?>
                    </div>


                    <div class="p-6">
                        <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full mb-3">
                            <?= htmlspecialchars($b['kategori']); ?>
                        </span>

                        <h3 class="text-lg font-semibold mb-2 text-gray-800">
                            <?= htmlspecialchars($b['judul']); ?>
                        </h3>

                        <div class="flex items-center justify-between text-sm text-gray-500 mb-3">
                            <span><i class="far fa-calendar"></i> <?= tglIndo($b['tanggal']); ?></span>
                        </div>

                        <a href="news_detail.php?id=<?= $b['id']; ?>" 
                            class="text-blue-600 font-semibold hover:underline text-sm">
                            Baca Selengkapnya →
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- PAGINATION -->
        <div class="flex justify-center mt-12 space-x-2">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>" 
                    class="px-4 py-2 bg-gray-200 rounded-lg text-gray-700 hover:bg-gray-300">
                    ‹ Prev
                </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>" 
                    class="px-4 py-2 rounded-lg 
                    <?= $i == $page ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>" 
                    class="px-4 py-2 bg-gray-200 rounded-lg text-gray-700 hover:bg-gray-300">
                    Next ›
                </a>
            <?php endif; ?>
        </div>

    </div>
</section>

<?php include '../includes/footer.php'; ?>

