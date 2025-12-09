<?php
require_once 'includes/auth.php';
require_once '../config/database.php';

$pageTitle = 'Kelola Sponsor / Kolaborasi';
$db = new Database();

$success = '';
$error = '';

/* ============================================================
   UPLOAD FILE KE TABEL FILES
============================================================ */
function uploadImageToFiles($db, $fileInputName = 'gambar') {
    if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] !== 0) {
        return null;
    }

    $allowed = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
    if (!in_array($_FILES[$fileInputName]['type'], $allowed)) {
        return ['error' => 'File harus berupa gambar (jpg, png, webp)'];
    }

    $uploadDir = '../uploads/sponsor/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = time() . '_' . basename($_FILES[$fileInputName]['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $targetPath)) {

        $mime = mime_content_type($targetPath);

        $fileNameEsc = $db->escape($fileName);
        $pathEsc = $db->escape('uploads/sponsor');
        $mimeEsc = $db->escape($mime);

        $sql = "INSERT INTO files (filename, path, mime_type)
                VALUES ('$fileNameEsc', '$pathEsc', '$mimeEsc')
                RETURNING id";

        $query = $db->query($sql);
        if ($query) {
            $row = $db->fetch($query);
            return ['id' => $row['id']];
        }
    }

    return ['error' => 'Gagal upload gambar'];
}

/* ============================================================
   DELETE MULTI
============================================================ */
if (isset($_POST['multi_delete']) && !empty($_POST['ids'])) {
    $ids = implode(",", array_map('intval', $_POST['ids']));
    $db->query("DELETE FROM kolaborasi WHERE id IN ($ids)");
    $success = "Sponsor dipilih berhasil dihapus!";
}

/* ============================================================
   DELETE SINGLE
============================================================ */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    $check = $db->query("SELECT id FROM kolaborasi WHERE id = $id");
    if ($db->numRows($check) > 0) {
        $db->query("DELETE FROM kolaborasi WHERE id = $id");
        $success = "Sponsor berhasil dihapus!";
    } else {
        $error = "Sponsor tidak ditemukan!";
    }
}

/* ============================================================
   ADD / EDIT
============================================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['multi_delete'])) {

    $id = (int)($_POST['id'] ?? 0);
    $nama_sponsor = $db->escape($_POST['nama_sponsor']);
    $jenis = $db->escape($_POST['jenis']);
    $imageId = null;

    if (!$nama_sponsor || !$jenis) {
        $error = "Nama sponsor dan jenis wajib diisi!";
    }

    // Upload gambar
    if (!$error && isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        $upload = uploadImageToFiles($db, 'gambar');
        if (isset($upload['error'])) {
            $error = $upload['error'];
        } else {
            $imageId = (int)$upload['id'];
        }
    }

    if (!$error) {
        if ($id > 0) {
            // UPDATE
            $sql = "UPDATE kolaborasi SET 
                    nama_sponsor = '$nama_sponsor',
                    jenis = '$jenis',
                    updated_at = CURRENT_TIMESTAMP";

            if ($imageId) {
                $sql .= ", image_id = $imageId";
            }

            $sql .= " WHERE id = $id";

            if ($db->query($sql)) {
                $success = "Sponsor berhasil diupdate!";
            } else {
                $error = "Gagal update sponsor!";
            }

        } else {
            // INSERT
            if ($imageId) {
                $sql = "INSERT INTO kolaborasi (nama_sponsor, jenis, image_id)
                        VALUES ('$nama_sponsor', '$jenis', $imageId)";
            } else {
                $sql = "INSERT INTO kolaborasi (nama_sponsor, jenis)
                        VALUES ('$nama_sponsor', '$jenis')";
            }

            if ($db->query($sql)) {
                $success = "Sponsor berhasil ditambahkan!";
            } else {
                $error = "Gagal menambahkan sponsor!";
            }
        }
    }
}

/* ============================================================
   PAGINATION
============================================================ */
$perPage = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

$totalRow = $db->fetch($db->query("SELECT COUNT(*) AS total FROM kolaborasi"))['total'];
$totalPages = ceil($totalRow / $perPage);

$query = $db->query("
    SELECT k.*, (f.path || '/' || f.filename) AS gambar_path
    FROM kolaborasi k
    LEFT JOIN files f ON k.image_id = f.id
    ORDER BY k.created_at DESC
    LIMIT $perPage OFFSET $offset
");
$data = $db->fetchAll($query);

/* ============================================================
   EDIT GET
============================================================ */
$editData = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $q = $db->query("
        SELECT k.*, (f.path || '/' || f.filename) AS gambar_path
        FROM kolaborasi k
        LEFT JOIN files f ON k.image_id = f.id
        WHERE k.id = $eid
    ");
    if ($db->numRows($q) > 0) {
        $editData = $db->fetch($q);
    }
}

include 'includes/header.php';
?>

<!-- ALERT -->
<?php if ($success): ?>
<div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4 rounded">
    <p class="text-green-700"><?= $success ?></p>
</div>
<?php endif; ?>
<?php if ($error): ?>
<div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 rounded">
    <p class="text-red-700"><?= $error ?></p>
</div>
<?php endif; ?>

<!-- FORM TAMBAH/EDIT -->
<div class="bg-white rounded-xl shadow-md p-6 mb-6">
    <h3 class="text-lg font-bold mb-4"><?= $editData ? "Edit" : "Tambah" ?> Sponsor</h3>

    <form method="POST" enctype="multipart/form-data">
        <?php if ($editData): ?>
            <input type="hidden" name="id" value="<?= $editData['id'] ?>">
        <?php endif; ?>

        <div class="mb-4">
            <label class="font-semibold">Nama Sponsor *</label>
            <input type="text" name="nama_sponsor" required
                value="<?= htmlspecialchars($editData['nama_sponsor'] ?? '') ?>"
                class="w-full px-4 py-2 border rounded-lg">
        </div>

        <div class="mb-4">
            <label class="font-semibold">Jenis Sponsor *</label>
            <select name="jenis" class="w-full px-4 py-2 border rounded-lg" required>
                <option value="">Pilih Jenis</option>
                <?php
                    $opsi = ["Partner","Internal", "Eksternal", "Kolaborasi"];
                    foreach ($opsi as $o) {
                        $sel = ($editData['jenis'] ?? '') == $o ? 'selected' : '';
                        echo "<option value='$o' $sel>$o</option>";
                    }
                ?>
            </select>
        </div>

        <div class="mb-4">
            <label class="font-semibold">Gambar Sponsor (opsional)</label>
            <input type="file" name="gambar" accept="image/*"
                class="w-full px-4 py-2 border rounded-lg">
        </div>

        <?php if ($editData && $editData['gambar_path']): ?>
            <div class="mb-4">
                <p class="font-semibold mb-1">Gambar Saat Ini:</p>
                <img src="../<?= $editData['gambar_path'] ?>" class="w-40 rounded">
            </div>
        <?php endif; ?>

        <button class="px-6 py-2 bg-blue-600 text-white rounded-lg">
            <?= $editData ? "Update" : "Simpan" ?>
        </button>
    </form>
</div>

<!-- LIST DATA -->
<div class="bg-white rounded-xl shadow-md overflow-hidden">
    <form method="POST">
        <table class="w-full">
            <thead class="bg-gradient-to-r from-purple-600 to-blue-600 text-white">
                <tr>
                    <th class="px-6 py-3 text-left">Logo</th>
                    <th class="px-6 py-3 text-left">Nama Sponsor</th>
                    <th class="px-6 py-3 text-left">Jenis</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                    <th class="px-6 py-3 text-center">
                        <input type="checkbox" id="checkAll">
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php if ($data): foreach ($data as $row): ?>
                <tr>
                    <td class="px-6 py-3">
                        <?php if ($row['gambar_path']): ?>
                            <img src="../<?= $row['gambar_path'] ?>" class="w-20 h-16 object-cover rounded">
                        <?php else: ?>
                            <div class="w-20 h-16 bg-gray-200 rounded flex items-center justify-center">
                                <i class="fas fa-image text-gray-400"></i>
                            </div>
                        <?php endif; ?>
                    </td>

                    <td class="px-6 py-3 font-semibold"><?= htmlspecialchars($row['nama_sponsor']) ?></td>

                    <td class="px-6 py-3 text-sm"><?= htmlspecialchars($row['jenis']) ?></td>

                    <td class="px-6 py-3 text-center space-x-2">
                        <a href="?edit=<?= $row['id'] ?>" class="px-3 py-1 bg-blue-500 text-white rounded">Edit</a>
                        <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Hapus sponsor ini?')"
                           class="px-3 py-1 bg-red-500 text-white rounded">Hapus</a>
                    </td>

                    <td class="px-6 py-3 text-center">
                        <input type="checkbox" name="ids[]" value="<?= $row['id'] ?>">
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="5" class="py-6 text-center text-gray-500">Belum ada data sponsor</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="p-4 border-t flex justify-between">
            <button type="submit" name="multi_delete"
                class="px-4 py-2 bg-red-600 text-white rounded"
                onclick="return confirm('Hapus sponsor yang dipilih?')">Hapus Terpilih</button>

            <div class="flex space-x-2">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>"
                        class="px-3 py-1 rounded-lg <?= $i == $page ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    </form>
</div>

<script>
document.getElementById("checkAll").addEventListener("change", function () {
    document.querySelectorAll("input[name='ids[]']").forEach(cb => cb.checked = this.checked);
});
</script>

<?php include 'includes/footer.php'; ?>
