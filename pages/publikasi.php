<?php
require_once '../config/database.php';
$pageTitle = 'Publikasi Dosen';
$pageInPages = true;

$db = new Database();

$dosen_id = isset($_GET['dosen_id']) ? (int) $_GET['dosen_id'] : 0;

if (!$dosen_id) {
    // redirect back to struktur page if no id
    header('Location: struktur_organisasi.php');
    exit;
}

// Get dosen info
// Get dosen info and latest foto (if available)
$dosenRes = $db->query("SELECT id, nama, deskripsi FROM dosen WHERE id = $dosen_id LIMIT 1");
$dosen = $db->fetch($dosenRes);

// Try to get foto from struktur_organisasi -> files (latest entry)
$foto_path = '';
$fotoRes = $db->query("SELECT (f.path || '/' || f.filename) AS foto_path
    FROM struktur_organisasi so
    LEFT JOIN files f ON so.foto_id = f.id
    WHERE so.id_dosen = $dosen_id AND so.foto_id IS NOT NULL
    ORDER BY so.created_at DESC LIMIT 1");
$fotoRow = $db->fetch($fotoRes);
if ($fotoRow && !empty($fotoRow['foto_path'])) {
    $foto_path = $fotoRow['foto_path'];
}

// Debug info (enable by adding ?debug=1 to the URL)
if (isset($_GET['debug']) && $_GET['debug'] == '1') {
    $fotoServerPathDbg = __DIR__ . '/../' . ltrim($foto_path, '/');
    $fotoWebPathDbg = '../' . ltrim($foto_path, '/');
    $existsDbg = file_exists($fotoServerPathDbg) ? 'yes' : 'no';
    echo "<div style='background:#fff3cd;border:1px solid #ffeeba;padding:12px;margin:12px 0;border-radius:6px;'>";
    echo "<strong>DEBUG publikasi.php</strong><br>";
    echo "foto_path (DB): <code>" . htmlspecialchars($foto_path) . "</code><br>";
    echo "fotoServerPath: <code>" . htmlspecialchars($fotoServerPathDbg) . "</code> (<strong>exists: $existsDbg</strong>)<br>";
    echo "fotoWebPath: <code>" . htmlspecialchars($fotoWebPathDbg) . "</code><br>";
    echo "</div>";
}

// Get publikasi
$pubRes = $db->query("SELECT id, judul, tahun, jenis, penerbit FROM publikasi WHERE id_dosen = $dosen_id ORDER BY tahun DESC, id DESC");
$publikasi = $db->fetchAll($pubRes);

include '../includes/header.php';
?>

<section class="relative py-16 bg-gradient-to-br from-blue-600 to-blue-800 text-white">
    <div class="container mx-auto px-6 text-center">
        <h1 class="text-4xl font-bold mb-2">Publikasi</h1>
        <p class="text-lg text-blue-100">Daftar publikasi untuk: <strong><?= htmlspecialchars($dosen['nama'] ?? '-') ?></strong></p>
    </div>
</section>

<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-6 max-w-4xl">
        <div class="bg-white shadow p-6 mb-6">
            <div class="flex items-center gap-4 mb-4">
                <?php
                $fotoServerPath = __DIR__ . '/../' . ltrim($foto_path, '/');
                $fotoWebPath = '../' . ltrim($foto_path, '/');
                if (!empty($foto_path) && file_exists($fotoServerPath)): ?>
                    <div class="w-24 h-24 overflow-hidden">
                        <img src="<?= htmlspecialchars($fotoWebPath) ?>" alt="<?= htmlspecialchars($dosen['nama'] ?? '-') ?>" class="w-full h-full object-cover">
                    </div>
                <?php else: ?>
                    <div class="w-24 h-24 bg-gray-200 flex items-center justify-center text-gray-500">
                        <i class="fas fa-user text-2xl"></i>
                    </div>
                <?php endif; ?>

                <div>
                    <h2 class="text-2xl font-bold mb-1"><?= htmlspecialchars($dosen['nama'] ?? '-') ?></h2>
                    <p class="text-sm text-gray-600">Deskripsi: <?= htmlspecialchars($dosen['deskripsi'] ?: '-') ?></p>
                </div>
            </div>

            <?php if (!empty($publikasi)): ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2">Judul</th>
                                <th class="px-4 py-2">Tahun</th>
                                <th class="px-4 py-2">Jenis</th>
                                <th class="px-4 py-2">Penerbit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($publikasi as $p): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-3"><?= htmlspecialchars($p['judul']) ?></td>
                                    <td class="px-4 py-3"><?= htmlspecialchars($p['tahun']) ?></td>
                                    <td class="px-4 py-3"><?= htmlspecialchars(ucfirst($p['jenis'])) ?></td>
                                    <td class="px-4 py-3"><?= htmlspecialchars($p['penerbit'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-8 text-gray-600">
                    <i class="fas fa-book text-4xl mb-2"></i>
                    <p class="mt-4">Belum ada publikasi untuk dosen ini.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="flex justify-between">
            <a href="struktur_organisasi.php" class="px-4 py-2 bg-gray-300 rounded-lg">Kembali</a>
            <a href="../admin/publikasi.php?dosen_id=<?= $dosen_id ?>" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Kelola Publikasi (Admin)</a>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
