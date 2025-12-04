<?php
require_once 'includes/auth.php';
require_once '../config/database.php';

$pageTitle = 'Struktur Organisasi';
$db = new Database();

$success = '';
$error = '';

/* ================================
   HELPER: UPLOAD FILE KE TABEL FILES
================================ */
function uploadFileToFilesTable($db, $fileInputName) {
    if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] !== 0) {
        return null;
    }

    $uploadDir = '../uploads/struktur/';
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
        $pathEsc = $db->escape('uploads/struktur');
        $mimeTypeEsc = $db->escape($mimeType);

        // Insert to files table and return ID
        $sql = "INSERT INTO files (filename, path, mime_type) 
                VALUES ('$fileNameEsc', '$pathEsc', '$mimeTypeEsc') 
                RETURNING id";
        
        $result = $db->query($sql);
        if ($result) {
            $row = $db->fetch($result);
            return $row['id'] ?? null;
        }
    }

    return null;
}

/* ================================
   DELETE DATA
================================ */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($db->query("DELETE FROM struktur_organisasi WHERE id = $id")) {
        $success = 'Data berhasil dihapus!';
    } else {
        $error = 'Gagal menghapus data!';
    }
}

/* ================================
   ADD / EDIT DATA
================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    $jabatan = $db->escape($_POST['jabatan']);
    $nama = $db->escape($_POST['nama']);
    $nip = $db->escape($_POST['nip'] ?? '');
    $fotoId = null;

    // Get or create dosen record
    $dosenId = null;
    if ($nip) {
        $dosenCheck = $db->query("SELECT id FROM dosen WHERE nip = '$nip' LIMIT 1");
        $dosenRow = $db->fetch($dosenCheck);
        if ($dosenRow) {
            $dosenId = $dosenRow['id'];
            // Update nama if changed
            $db->query("UPDATE dosen SET nama = '$nama' WHERE id = $dosenId");
        } else {
            // Create new dosen
            $db->query("INSERT INTO dosen (nip, nama) VALUES ('$nip', '$nama')");
            $dosenCheck = $db->query("SELECT id FROM dosen WHERE nip = '$nip' LIMIT 1");
            $dosenRow = $db->fetch($dosenCheck);
            if ($dosenRow) {
                $dosenId = $dosenRow['id'];
            }
        }
    } else {
        // If no NIP, check if dosen exists by name
        $dosenCheck = $db->query("SELECT id FROM dosen WHERE nama = '$nama' LIMIT 1");
        $dosenRow = $db->fetch($dosenCheck);
        if ($dosenRow) {
            $dosenId = $dosenRow['id'];
        } else {
            // Create new dosen without NIP
            $db->query("INSERT INTO dosen (nip, nama) VALUES ('', '$nama')");
            $dosenCheck = $db->query("SELECT id FROM dosen WHERE nama = '$nama' ORDER BY id DESC LIMIT 1");
            $dosenRow = $db->fetch($dosenCheck);
            if ($dosenRow) {
                $dosenId = $dosenRow['id'];
            }
        }
    }

    // Upload foto to files table
    $fotoId = uploadFileToFilesTable($db, 'foto', 'image');

    if ($dosenId) {
        if ($id > 0) {
            // UPDATE
            $sql = "UPDATE struktur_organisasi 
                    SET id_dosen=$dosenId, jabatan='$jabatan'";
            
            // Only update foto_id if new file was uploaded
            if ($fotoId) {
                $sql .= ", foto_id=$fotoId";
            }
            
            $sql .= ", updated_at=CURRENT_TIMESTAMP WHERE id=$id";
        } else {
            // INSERT
            if ($fotoId) {
                $sql = "INSERT INTO struktur_organisasi (id_dosen, jabatan, foto_id)
                        VALUES ($dosenId, '$jabatan', $fotoId)";
            } else {
                $sql = "INSERT INTO struktur_organisasi (id_dosen, jabatan)
                        VALUES ($dosenId, '$jabatan')";
            }
        }

        if ($db->query($sql)) {
            $success = 'Data berhasil disimpan!';
        } else {
            $error = 'Gagal menyimpan data!';
        }
    } else {
        $error = 'Gagal membuat/mengambil data dosen!';
    }
}

/* ================================
   GET DATA
================================ */
/*
Ranking jabatan (besar ke kecil):
1. Ketua Laboratorium
2. Wakil Ketua
3. Sekretaris
4. Bendahara
5. Koordinator
6. Anggota
*/
$result = $db->query("
    SELECT 
        so.*, 
        d.nama, 
        d.nip,
        (f.path || '/' || f.filename) AS foto_path
    FROM struktur_organisasi so
    LEFT JOIN dosen d ON so.id_dosen = d.id
    LEFT JOIN files f ON so.foto_id = f.id
    ORDER BY 
        CASE 
            WHEN so.jabatan = 'Ketua Laboratorium' THEN 1
            WHEN so.jabatan = 'Wakil Ketua' THEN 2
            WHEN so.jabatan = 'Sekretaris' THEN 3
            WHEN so.jabatan = 'Bendahara' THEN 4
            WHEN so.jabatan = 'Koordinator' THEN 5
            WHEN so.jabatan = 'Anggota' THEN 6
            ELSE 99
        END,
        d.nama ASC
");
$data = $db->fetchAll($result);

// Edit mode
$editData = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $editRes = $db->query("
        SELECT 
            so.*, 
            d.nama, 
            d.nip,
            (f.path || '/' || f.filename) AS foto_path
        FROM struktur_organisasi so
        LEFT JOIN dosen d ON so.id_dosen = d.id
        LEFT JOIN files f ON so.foto_id = f.id
        WHERE so.id = $editId LIMIT 1
    ");
    $editData = $db->fetch($editRes);
}

include 'includes/header.php';
?>

<?php if ($success): ?>
<div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded">
    <p class="text-green-700"><?php echo $success; ?></p>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
    <p class="text-red-700"><?php echo $error; ?></p>
</div>
<?php endif; ?>

<!-- FORM TAMBAH / EDIT -->
<div class="bg-white rounded-xl shadow-md p-6 mb-6">
    <h3 class="text-lg font-bold mb-4">
        <?php echo $editData ? 'Edit Struktur' : 'Tambah Struktur Organisasi'; ?>
    </h3>

    <form method="POST" enctype="multipart/form-data">
        <?php if ($editData): ?>
            <input type="hidden" name="id" value="<?php echo $editData['id']; ?>">
        <?php endif; ?>

        <div class="grid md:grid-cols-2 gap-4 mb-4">

            <div>
                <label class="block font-semibold mb-2">Jabatan *</label>
                <select name="jabatan" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-300">
                    <option value="">-- Pilih Jabatan --</option>

                    <?php 
                    $listJabatan = [
                        "Ketua Laboratorium",
                        "Wakil Ketua",
                        "Sekretaris",
                        "Bendahara",
                        "Koordinator",
                        "Anggota"
                    ];

                    foreach ($listJabatan as $j): ?>
                        <option value="<?php echo $j; ?>"
                            <?php echo (isset($editData['jabatan']) && $editData['jabatan'] === $j) ? 'selected' : ''; ?>>
                            <?php echo $j; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block font-semibold mb-2">Nama *</label>
                <input type="text" name="nama" required
                       value="<?php echo htmlspecialchars($editData['nama'] ?? ''); ?>"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-300">
            </div>

        </div>

        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block font-semibold mb-2">NIP (Opsional)</label>
                <input type="text" name="nip"
                       value="<?php echo htmlspecialchars($editData['nip'] ?? ''); ?>"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-300">
            </div>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-2">Foto</label>
            <input type="file" name="foto" accept="image/*"
                   class="w-full px-4 py-2 border rounded-lg">

            <?php if ($editData && !empty($editData['foto_path'])): ?>
                <p class="mt-2 text-sm text-gray-600">Foto saat ini:</p>
                <img src="../<?php echo htmlspecialchars($editData['foto_path']); ?>" class="w-24 h-24 rounded-lg mt-1 object-cover">
            <?php endif; ?>
        </div>

        <div class="flex justify-end">
            <?php if ($editData): ?>
                <a href="struktur.php" class="px-4 py-2 bg-gray-300 rounded-lg mr-2">Batal</a>
            <?php endif; ?>

            <button class="px-6 py-2 bg-blue-600 text-white rounded-lg">
                Simpan
            </button>
        </div>
    </form>
</div>

<!-- TABEL -->
<div class="bg-white rounded-xl shadow-md overflow-hidden">
    <table class="w-full">
        <thead class="text-white" style="background-color: blue;">
            <tr>
                <th class="px-6 py-3 text-left">Foto</th>
                <th class="px-6 py-3 text-left">Jabatan</th>
                <th class="px-6 py-3 text-left">Nama</th>
                <th class="px-6 py-3 text-left">NIP</th>
                <th class="px-6 py-3 text-center">Aksi</th>
            </tr>
        </thead>

        <tbody>
        <?php if ($data): ?>
            <?php foreach ($data as $row): ?>
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <?php if (!empty($row['foto_path'])): ?>
                            <img src="../<?php echo htmlspecialchars($row['foto_path']); ?>" class="w-12 h-12 rounded-full object-cover">
                        <?php else: ?>
                            <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-gray-500"></i>
                            </div>
                        <?php endif; ?>
                    </td>

                    <td class="px-6 py-4 font-semibold"><?php echo htmlspecialchars($row['jabatan']); ?></td>
                    <td class="px-6 py-4"><?php echo htmlspecialchars($row['nama']); ?></td>
                    <td class="px-6 py-4"><?php echo htmlspecialchars($row['nip'] ?: '-'); ?></td>

                    <td class="px-6 py-4 text-center">
                        <a href="?edit=<?php echo $row['id']; ?>" class="text-blue-600 mr-3">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="?delete=<?php echo $row['id']; ?>" 
                           class="text-red-600" 
                           onclick="return confirm('Hapus data ini?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="text-center py-8 text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-2"></i><br>
                    Belum ada data
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
