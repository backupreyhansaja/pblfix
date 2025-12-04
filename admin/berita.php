<?php
// CRUD BERITA – DENGAN QUILL EDITOR – FULL FIX
require_once 'includes/auth.php';
require_once '../config/database.php';

$pageTitle = 'Kelola Berita';
$db = new Database();

$success = '';
$error = '';

/* ================================
   HELPER: UPLOAD FILE KE TABEL FILES
================================ */
function uploadImageToFiles($db, $fileInputName = 'gambar') {
    if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] !== 0) {
        return null;
    }

    $allowed = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
    if (!in_array($_FILES[$fileInputName]['type'], $allowed)) {
        return ['error' => 'File harus berupa gambar (jpg, png, webp)'];
    }

    $uploadDir = '../uploads/berita/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = time() . '_' . basename($_FILES[$fileInputName]['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $targetPath)) {
        // Get mime type
        $mimeType = mime_content_type($targetPath);
        
        // Escape values for SQL
        $fileNameEsc = $db->escape($fileName);
        $pathEsc = $db->escape('uploads/berita');
        $mimeTypeEsc = $db->escape($mimeType);

        // Insert to files table and return ID
        $sql = "INSERT INTO files (filename, path, mime_type) 
                VALUES ('$fileNameEsc', '$pathEsc', '$mimeTypeEsc') 
                RETURNING id";
        
        $result = $db->query($sql);
        if ($result) {
            $row = $db->fetch($result);
            return ['id' => $row['id'] ?? null];
        }
    }

    return ['error' => 'Gagal upload gambar'];
}

/* -------------------------------
   DELETE MULTI
---------------------------------*/
if (isset($_POST['multi_delete']) && !empty($_POST['ids'])) {
    $ids = implode(',', array_map('intval', $_POST['ids']));
    $db->query("DELETE FROM berita WHERE id IN ($ids)");
    $success = "Berita terpilih berhasil dihapus!";
}

/* -------------------------------
   DELETE SINGLE
---------------------------------*/
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    $check = $db->query("SELECT id FROM berita WHERE id = $id LIMIT 1");
    if ($db->numRows($check) > 0) {
        if ($db->query("DELETE FROM berita WHERE id = $id")) {
            $success = 'Berita berhasil dihapus!';
        } else {
            $error = 'Gagal menghapus berita!';
        }
    } else {
        $error = 'Berita tidak ditemukan!';
    }
}

/* -------------------------------
   ADD / EDIT
---------------------------------*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['multi_delete'])) {

    $id = (int) ($_POST['id'] ?? 0);
    $judul = $db->escape($_POST['judul'] ?? '');
    $isi = $db->escape($_POST['isi'] ?? ''); // QUILL HTML
    $deskripsi = $db->escape($_POST['deskripsi'] ?? '');
    $kategori = $db->escape($_POST['kategori'] ?? '');
    $tanggal = $db->escape($_POST['tanggal'] ?? '');
    $imageId = null;

    if (!$judul || !$kategori || !$tanggal) {
        $error = "Judul, kategori dan tanggal wajib diisi!";
    }

    /* UPLOAD FILE TO FILES TABLE */
    if (!$error && isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        $uploadResult = uploadImageToFiles($db, 'gambar');
        
        if (isset($uploadResult['error'])) {
            $error = $uploadResult['error'];
        } elseif (isset($uploadResult['id'])) {
            $imageId = (int) $uploadResult['id'];
        }
    }

    /* INSERT / UPDATE */
    if (!$error) {
        if ($id > 0) {
            // UPDATE
            $check = $db->query("SELECT id FROM berita WHERE id = $id LIMIT 1");
            if ($db->numRows($check) == 0) {
                $error = 'Data berita tidak ditemukan!';
            } else {
                $sql = "UPDATE berita SET 
                        judul = '$judul',
                        isi = '$isi',
                        deskripsi = '$deskripsi',
                        kategori = '$kategori',
                        tanggal = '$tanggal'";

                // Only update image_id if new image was uploaded
                if ($imageId) {
                    $sql .= ", image_id = $imageId";
                }

                $sql .= ", updated_at = CURRENT_TIMESTAMP WHERE id = $id";

                if ($db->query($sql)) {
                    $success = 'Berita berhasil diupdate!';
                } else {
                    $error = 'Gagal mengupdate berita!';
                }
            }
        } else {
            // INSERT
            $admin_id = $_SESSION['admin_id'];

            if ($imageId) {
                $sql = "INSERT INTO berita (judul, isi, deskripsi, image_id, kategori, tanggal, uploaded_by)
                        VALUES ('$judul', '$isi', '$deskripsi', $imageId, '$kategori', '$tanggal', $admin_id)";
            } else {
                $sql = "INSERT INTO berita (judul, isi, deskripsi, kategori, tanggal, uploaded_by)
                        VALUES ('$judul', '$isi', '$deskripsi', '$kategori', '$tanggal', $admin_id)";
            }

            if ($db->query($sql)) {
                $success = 'Berita berhasil ditambahkan!';
            } else {
                $error = 'Gagal menambahkan berita!';
            }
        }
    }
}

/* -------------------------------
   PAGINATION
---------------------------------*/
$perPage = 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

$countResult = $db->query("SELECT COUNT(*) AS total FROM berita");
$countRow = $db->fetch($countResult);
$totalRows = $countRow['total'];
$totalPages = ceil($totalRows / $perPage);

$result = $db->query("
    SELECT 
        b.*,
        (f.path || '/' || f.filename) AS gambar_path
    FROM berita b
    LEFT JOIN files f ON b.image_id = f.id
    ORDER BY b.tanggal DESC, b.created_at DESC 
    LIMIT $perPage OFFSET $offset
");
$data = $db->fetchAll($result);

/* -------------------------------
   GET DATA EDIT
---------------------------------*/
$editData = null;
if (isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    $editResult = $db->query("
        SELECT 
            b.*,
            (f.path || '/' || f.filename) AS gambar_path
        FROM berita b
        LEFT JOIN files f ON b.image_id = f.id
        WHERE b.id = $editId 
        LIMIT 1
    ");
    if ($db->numRows($editResult) > 0) {
        $editData = $db->fetch($editResult);
    }
}

include 'includes/header.php';
?>

<!-- ALERTS -->
<?php if ($success): ?>
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded">
        <p class="text-green-700"><?= $success ?></p>
    </div><?php endif; ?>
<?php if ($error): ?>
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
        <p class="text-red-700"><?= $error ?></p>
    </div><?php endif; ?>

<!-- FORM CRUD BERITA DENGAN QUILL -->
<div class="bg-white rounded-xl shadow-md p-6 mb-6">

    <h3 class="text-lg font-bold text-gray-800 mb-4"><?= $editData ? 'Edit' : 'Tambah' ?> Berita</h3>

    <form method="POST" enctype="multipart/form-data">

        <?php if ($editData): ?><input type="hidden" name="id" value="<?= $editData['id'] ?>"><?php endif; ?>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Judul *</label>
            <input type="text" name="judul" required value="<?= htmlspecialchars($editData['judul'] ?? '') ?>"
                class="w-full px-4 py-2 rounded-lg border" />
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Deskripsi Singkat</label>
            <textarea name="deskripsi" rows="2"
                class="w-full px-4 py-2 rounded-lg border"><?= htmlspecialchars($editData['deskripsi'] ?? '') ?></textarea>
        </div>

        <!-- QUILL EDITOR -->
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Isi Berita *</label>

            <div id="quillEditor" style="height:250px; background:white;" class="rounded border"></div>

            <textarea name="isi" id="isi" style="display:none;"></textarea>
        </div>

        <div class="grid md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Kategori *</label>
                <select name="kategori" class="w-full px-4 py-2 rounded-lg border" required>
                    <option value="">Pilih Kategori</option>
                    <?php
                    $kategoriList = ['Pengumuman', 'Event', 'Pelatihan', 'Kegiatan', 'Penelitian', 'Prestasi'];
                    foreach ($kategoriList as $k) {
                        $sel = (($editData['kategori'] ?? '') == $k) ? 'selected' : '';
                        echo "<option value='$k' $sel>$k</option>";
                    }
                    ?>
                </select>
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Tanggal *</label>
                <input type="date" name="tanggal" required
                    value="<?= htmlspecialchars($editData['tanggal'] ?? date('Y-m-d')) ?>"
                    class="w-full px-4 py-2 rounded-lg border" />
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Gambar
                    <?= $editData ? '(Opsional)' : '' ?></label>
                <input type="file" name="gambar" accept="image/*" class="w-full px-4 py-2 rounded-lg border" />
            </div>
        </div>

        <?php if ($editData && !empty($editData['gambar_path'])): ?>
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Gambar Saat Ini</label>
                <img src="../<?= htmlspecialchars($editData['gambar_path']) ?>" class="w-64 h-48 object-cover rounded-lg" />
            </div>
        <?php endif; ?>

        <div class="flex justify-end space-x-2">
            <?php if ($editData): ?>
                <a href="berita.php" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg">Batal</a>
            <?php endif; ?>
            
            <button type="submit" class="px-6 py-2 text-white rounded-lg hover:shadow-lg transition" style="background-color : blue">
                <i class="fas fa-save mr-2"></i><?php echo $editData ? 'Update' : 'Simpan'; ?>
            </button>
        </div>

    </form>
</div>

<!-- LIST DATA BERITA -->
<div class="bg-white rounded-xl shadow-md overflow-hidden mt-6">
    <form method="POST">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-purple-600 to-blue-600 text-white">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Gambar</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Judul</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Kategori</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Tanggal</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold">Aksi</th>
                        <th class="px-6 py-3 text-center">
                            <input type="checkbox" id="checkAll">
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">

                    <?php if ($data): ?>
                        <?php foreach ($data as $row): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <?php if (!empty($row['gambar_path'])): ?>
                                        <img src="../<?= htmlspecialchars($row['gambar_path']) ?>"
                                            class="w-20 h-16 object-cover rounded">
                                    <?php else: ?>
                                        <div class="w-20 h-16 bg-gray-200 rounded flex items-center justify-center">
                                            <i class="fas fa-newspaper text-gray-400"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <td class="px-6 py-4">
                                    <p class="font-semibold text-gray-800"><?= htmlspecialchars($row['judul']) ?></p>
                                    <p class="text-sm text-gray-500">
                                        <?= htmlspecialchars(substr($row['deskripsi'] ?? '', 0, 60)) ?>
                                        <?= strlen($row['deskripsi'] ?? '') > 60 ? '...' : '' ?>
                                    </p>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-semibold">
                                        <?= htmlspecialchars($row['kategori']) ?>
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <?= date('d M Y', strtotime($row['tanggal'])) ?>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex justify-center space-x-2">
                                        <a href="?edit=<?= $row['id'] ?>"
                                            class="px-3 py-1 bg-blue-500 text-white rounded-lg text-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Hapus berita ini?')"
                                            class="px-3 py-1 bg-red-500 text-white rounded-lg text-sm">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <input type="checkbox" name="ids[]" value="<?= $row['id'] ?>">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-newspaper text-gray-300 text-6xl mb-4"></i><br>
                                Belum ada berita
                            </td>
                        </tr>
                    <?php endif; ?>

                </tbody>
            </table>
        </div>

        <div class="p-4 border-t flex justify-between">
            <button type="submit" name="multi_delete" class="px-4 py-2 bg-red-500 text-white rounded-lg"
                onclick="return confirm('Hapus semua yang dipilih?')">
                Hapus Terpilih
            </button>

            <!-- Pagination -->
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

<!-- SCRIPT QUILL -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
    // Init Quill
    var quill = new Quill('#quillEditor', {
        theme: 'snow'
    });

    // Preload isi untuk EDIT
    <?php if ($editData): ?>
        quill.root.innerHTML = <?= json_encode($editData['isi']) ?>;
    <?php endif; ?>

    // On Submit → Kirim HTML ke textarea
    document.querySelector("form").onsubmit = function () {
        document.querySelector("#isi").value = quill.root.innerHTML;
    };

    document.getElementById("checkAll").addEventListener("change", function () {
        let checkboxes = document.querySelectorAll("input[name='ids[]']");
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
</script>

<?php include 'includes/footer.php'; ?>