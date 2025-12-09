<?php
require_once 'includes/auth.php';
require_once '../config/database.php';

$pageTitle = 'Publikasi Dosen';
$db = new Database();

$success = '';
$error = '';

// If no dosen_id provided, show list of dosen to manage
$dosenId = isset($_GET['dosen_id']) ? (int) $_GET['dosen_id'] : 0;

/* -------------------------------
   DELETE MULTI
---------------------------------*/
if (isset($_POST['multi_delete']) && !empty($_POST['ids'])) {
    $ids = implode(',', array_map('intval', $_POST['ids']));
    $db->query("DELETE FROM publikasi WHERE id IN ($ids)");
    $success = "Publikasi terpilih berhasil dihapus!";
}

/* -------------------------------
   DELETE SINGLE
---------------------------------*/
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $check = $db->query("SELECT id FROM publikasi WHERE id = $id LIMIT 1");
    if ($db->numRows($check) > 0) {
        if ($db->query("DELETE FROM publikasi WHERE id = $id")) {
            $success = 'Publikasi berhasil dihapus!';
        } else {
            $error = 'Gagal menghapus publikasi!';
        }
    } else {
        $error = 'Publikasi tidak ditemukan!';
    }
}

/* -------------------------------
   ADD / EDIT
---------------------------------*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['multi_delete'])) {
    $id = (int) ($_POST['id'] ?? 0);
    $id_dosen = (int) ($_POST['id_dosen'] ?? 0);
    $judul = $db->escape($_POST['judul'] ?? '');
    $tahun = (int) ($_POST['tahun'] ?? 0);
    $jenis = $db->escape($_POST['jenis'] ?? '');
    $penerbit = $db->escape($_POST['penerbit'] ?? '');

    if (!$id_dosen || !$judul || !$tahun || !$jenis) {
        $error = 'Dosen, judul, tahun dan jenis wajib diisi!';
    }

    if (!$error) {
        if ($id > 0) {
            $check = $db->query("SELECT id FROM publikasi WHERE id = $id LIMIT 1");
            if ($db->numRows($check) == 0) {
                $error = 'Data publikasi tidak ditemukan!';
            } else {
                $sql = "UPDATE publikasi SET id_dosen = $id_dosen, judul = '$judul', tahun = $tahun, jenis = '$jenis', penerbit = '$penerbit', created_at = created_at, updated_at = CURRENT_TIMESTAMP WHERE id = $id";
                if ($db->query($sql)) {
                    $success = 'Publikasi berhasil diupdate!';
                } else {
                    $error = 'Gagal mengupdate publikasi!';
                }
            }
        } else {
            $sql = "INSERT INTO publikasi (id_dosen, judul, tahun, jenis, penerbit) VALUES ($id_dosen, '$judul', $tahun, '$jenis', '$penerbit')";
            if ($db->query($sql)) {
                $success = 'Publikasi berhasil ditambahkan!';
            } else {
                $error = 'Gagal menambahkan publikasi!';
            }
        }
    }
}

/* -------------------------------
   DATA & VIEWS
---------------------------------*/
include 'includes/header.php';

// If no dosen selected, show daftar dosen
if (!$dosenId) {
    $dosenRes = $db->query("SELECT id, deskripsi, nama FROM dosen ORDER BY nama ASC");
    $dosens = $db->fetchAll($dosenRes);
    ?>
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-bold mb-4">Pilih Dosen untuk Kelola Publikasi</h3>
        <?php if ($dosens): ?>
            <ul class="space-y-2">
                <?php foreach ($dosens as $d): ?>
                    <li>
                        <a href="publikasi.php?dosen_id=<?php echo $d['id']; ?>" class="px-4 py-2 bg-blue-600 text-white rounded-lg inline-block">
                            <?php echo htmlspecialchars($d['nama']); ?> (<?php echo htmlspecialchars($d['deskripsi'] ?: '-'); ?>)
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Tidak ada data dosen. Tambah terlebih dahulu pada Struktur Organisasi.</p>
        <?php endif; ?>
    </div>
    <?php
    include 'includes/footer.php';
    exit;
}

// Show publikasi untuk dosen terpilih
$dosenRes = $db->query("SELECT id, nama, deskripsi FROM dosen WHERE id = $dosenId LIMIT 1");
$dosen = $db->fetch($dosenRes);

// Pagination
$perPage = 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

$countRes = $db->query("SELECT COUNT(*) AS total FROM publikasi WHERE id_dosen = $dosenId");
$countRow = $db->fetch($countRes);
$totalRows = $countRow['total'];
$totalPages = ceil($totalRows / $perPage);

$pubsRes = $db->query("SELECT * FROM publikasi WHERE id_dosen = $dosenId ORDER BY tahun DESC, id DESC LIMIT $perPage OFFSET $offset");
$pubs = $db->fetchAll($pubsRes);

// Edit data
$editData = null;
if (isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    $editRes = $db->query("SELECT * FROM publikasi WHERE id = $editId LIMIT 1");
    if ($db->numRows($editRes) > 0) {
        $editData = $db->fetch($editRes);
    }
}

?>

<?php if ($success): ?>
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded">
        <p class="text-green-700"><?= $success ?></p>
    </div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
        <p class="text-red-700"><?= $error ?></p>
    </div>
<?php endif; ?>

<div class="bg-white rounded-xl shadow-md p-6 mb-6">
    <h3 class="text-lg font-bold mb-4">Publikasi: <?php echo htmlspecialchars($dosen['nama'] ?? ''); ?> (<?php echo htmlspecialchars($dosen['deskripsi'] ?? '-'); ?>)</h3>

    <form method="POST">
        <?php if ($editData): ?><input type="hidden" name="id" value="<?= $editData['id'] ?>"><?php endif; ?>
        <input type="hidden" name="id_dosen" value="<?= $dosenId ?>">

        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block font-semibold mb-2">Judul *</label>
                <input type="text" name="judul" required value="<?= htmlspecialchars($editData['judul'] ?? '') ?>" class="w-full px-4 py-2 border rounded-lg">
            </div>

            <div>
                <label class="block font-semibold mb-2">Tahun *</label>
                <input type="number" name="tahun" required min="1900" max="2100" value="<?= htmlspecialchars($editData['tahun'] ?? date('Y')) ?>" class="w-full px-4 py-2 border rounded-lg">
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block font-semibold mb-2">Jenis *</label>
                <select name="jenis" required class="w-full px-4 py-2 border rounded-lg">
                    <?php $jenisList = ['jurnal' => 'Jurnal', 'conference' => 'Conference', 'thesis' => 'Thesis'];
                    foreach ($jenisList as $k => $v) {
                        $sel = ((($editData['jenis'] ?? '') == $k) ? 'selected' : '');
                        echo "<option value='$k' $sel>$v</option>";
                    }
                    ?>
                </select>
            </div>

            <div>
                <label class="block font-semibold mb-2">Penerbit</label>
                <input type="text" name="penerbit" value="<?= htmlspecialchars($editData['penerbit'] ?? '') ?>" class="w-full px-4 py-2 border rounded-lg">
            </div>
        </div>

        <div class="flex justify-end">
            <?php if ($editData): ?>
                <a href="publikasi.php?dosen_id=<?= $dosenId ?>" class="px-4 py-2 bg-gray-300 rounded-lg mr-2">Batal</a>
            <?php endif; ?>
            <button class="px-6 py-2 bg-blue-600 text-white rounded-lg">Simpan</button>
        </div>
    </form>
</div>

<!-- LIST PUBLIKASI -->
<div class="bg-white rounded-xl shadow-md overflow-hidden mt-6">
    <form method="POST">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="px-6 py-3 text-sm font-semibold">Judul</th>
                        <th class="px-6 py-3 text-sm font-semibold">Tahun</th>
                        <th class="px-6 py-3 text-sm font-semibold">Jenis</th>
                        <th class="px-6 py-3 text-sm font-semibold">Penerbit</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold">Aksi</th>
                        <th class="px-6 py-3 text-center"><input type="checkbox" id="checkAll"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if ($pubs): ?>
                        <?php foreach ($pubs as $row): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4"><?= htmlspecialchars($row['judul']) ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($row['tahun']) ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($row['jenis']) ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($row['penerbit'] ?? '-') ?></td>
                                <td class="px-6 py-4 text-center">
                                    <a href="?dosen_id=<?= $dosenId ?>&edit=<?= $row['id'] ?>" class="px-3 py-1 bg-blue-500 text-white rounded-lg text-sm mr-2">Edit</a>
                                    <a href="?dosen_id=<?= $dosenId ?>&delete=<?= $row['id'] ?>" onclick="return confirm('Hapus publikasi ini?')" class="px-3 py-1 bg-red-500 text-white rounded-lg text-sm">Hapus</a>
                                </td>
                                <td class="px-6 py-4 text-center"><input type="checkbox" name="ids[]" value="<?= $row['id'] ?>"></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">Belum ada publikasi</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t flex justify-between">
            <button type="submit" name="multi_delete" class="px-4 py-2 bg-red-500 text-white rounded-lg" onclick="return confirm('Hapus semua yang dipilih?')">Hapus Terpilih</button>

            <div class="flex space-x-2">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?dosen_id=<?= $dosenId ?>&page=<?= $i ?>" class="px-3 py-1 rounded-lg <?= $i == $page ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
        </div>
    </form>
</div>

<div class="mt-4">
    <a href="struktur.php" class="px-4 py-2 bg-gray-300 rounded-lg">Kembali ke Struktur</a>
</div>

<?php include 'includes/footer.php'; ?>
