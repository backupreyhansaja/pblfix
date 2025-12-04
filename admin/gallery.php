<?php
require_once 'includes/auth.php';
require_once '../config/database.php';

$pageTitle = 'Kelola Galeri';
$db = new Database();

$success = '';
$error = '';

/* ================================
   HELPER: UPLOAD FILE KE TABEL FILES
================================ */
function uploadImageToFilesTable($db, $fileInputName = 'image') {
    if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] !== 0) {
        return null;
    }

    $uploadDir = '../uploads/gallery/';
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
        $pathEsc = $db->escape('uploads/gallery');
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

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($db->query("DELETE FROM gallery WHERE id = $id")) {
        $success = 'Data berhasil dihapus!';
    }
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $title = $db->escape($_POST['title']);
    $description = $db->escape($_POST['description']);
    $tanggal = $db->escape($_POST['tanggal']);
    $imageId = null;
    
    // Handle file upload to files table
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadResult = uploadImageToFilesTable($db, 'image');
        
        if (isset($uploadResult['error'])) {
            $error = $uploadResult['error'];
        } elseif (isset($uploadResult['id'])) {
            $imageId = (int) $uploadResult['id'];
        }
    }
    
    if (!$error) {
        if ($id > 0) {
            // UPDATE
            $sql = "UPDATE gallery SET title = '$title', description = '$description', tanggal = '$tanggal'";
            
            // Only update image_id if new image was uploaded
            if ($imageId) {
                $sql .= ", image_id = $imageId";
            }
            
            $sql .= ", updated_at = CURRENT_TIMESTAMP WHERE id = $id";
        } else {
            // INSERT - require image for new gallery
            if ($imageId) {
                $sql = "INSERT INTO gallery (title, description, image_id, tanggal) 
                        VALUES ('$title', '$description', $imageId, '$tanggal')";
            } else {
                $error = 'Gambar harus diupload!';
            }
        }
        
        if (!$error && $db->query($sql)) {
            $success = 'Data berhasil disimpan!';
        } elseif (!$error) {
            $error = 'Gagal menyimpan data!';
        }
    }
}

// Fetch all data with JOIN to files
$result = $db->query("
    SELECT 
        g.*,
        (f.path || '/' || f.filename) AS image_path
    FROM gallery g
    LEFT JOIN files f ON g.image_id = f.id
    ORDER BY g.created_at DESC
");
$data = $db->fetchAll($result);

// Get data for edit
$editData = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $editResult = $db->query("
        SELECT 
            g.*,
            (f.path || '/' || f.filename) AS image_path
        FROM gallery g
        LEFT JOIN files f ON g.image_id = f.id
        WHERE g.id = $editId 
        LIMIT 1
    ");
    $editData = $db->fetch($editResult);
}

include 'includes/header.php';
?>

<?php if ($success): ?>
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 mr-3"></i>
            <p class="text-green-700"><?php echo $success; ?></p>
        </div>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
            <p class="text-red-700"><?php echo $error; ?></p>
        </div>
    </div>
<?php endif; ?>

<!-- Form -->
<div class="bg-white rounded-xl shadow-md p-6 mb-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4">
        <?php echo $editData ? 'Edit' : 'Tambah'; ?> Foto Galeri
    </h3>
    
    <form method="POST" action="" enctype="multipart/form-data">
        <?php if ($editData): ?>
            <input type="hidden" name="id" value="<?php echo $editData['id']; ?>">
        <?php endif; ?>
        
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Judul Kegiatan *</label>
            <input type="text" name="title" required 
                   value="<?php echo htmlspecialchars($editData['title'] ?? ''); ?>"
                   class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 outline-none"
                   placeholder="Contoh: Workshop Teknologi AI">
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Deskripsi</label>
            <textarea name="description" rows="3" 
                      class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 outline-none"
                      placeholder="Deskripsi singkat kegiatan..."><?php echo htmlspecialchars($editData['description'] ?? ''); ?></textarea>
        </div>
        
        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Tanggal Kegiatan</label>
                <input type="date" name="tanggal" 
                       value="<?php echo htmlspecialchars($editData['tanggal'] ?? ''); ?>"
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 outline-none">
            </div>
            
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Foto <?php echo $editData ? '(Opsional - kosongkan jika tidak ingin mengubah)' : '*'; ?></label>
                <input type="file" name="image" accept="image/*" <?php echo $editData ? '' : 'required'; ?>
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-purple-500 outline-none">
            </div>
        </div>
        
        <?php if ($editData && !empty($editData['image_path'])): ?>
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Foto Saat Ini</label>
                <img src="../<?php echo htmlspecialchars($editData['image_path']); ?>" alt="Preview" class="w-48 h-48 object-cover rounded-lg">
            </div>
        <?php endif; ?>
        
        <div class="flex justify-end space-x-2">
            <?php if ($editData): ?>
                <a href="gallery.php" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                    Batal
                </a>
            <?php endif; ?>
            <button type="submit" class="px-6 py-2 text-white rounded-lg hover:shadow-lg transition" style="background-color : blue">
                <i class="fas fa-save mr-2"></i><?php echo $editData ? 'Update' : 'Simpan'; ?>
            </button>
        </div>
    </form>
</div>

<!-- Gallery Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if ($data && count($data) > 0): ?>
        <?php foreach ($data as $row): ?>
            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition">
                <div class="h-48 overflow-hidden">
                    <?php if (!empty($row['image_path'])): ?>
                        <img src="../<?php echo htmlspecialchars($row['image_path']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <div class="w-full h-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center">
                            <i class="fas fa-images text-white text-5xl"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="p-4">
                    <h4 class="font-bold text-lg text-gray-800 mb-2"><?php echo htmlspecialchars($row['title']); ?></h4>
                    <p class="text-sm text-gray-600 mb-3"><?php echo htmlspecialchars($row['description'] ?? ''); ?></p>
                    <?php if ($row['tanggal']): ?>
                        <p class="text-xs text-gray-500 mb-3">
                            <i class="fas fa-calendar mr-1"></i><?php echo date('d M Y', strtotime($row['tanggal'])); ?>
                        </p>
                    <?php endif; ?>
                    <div class="flex space-x-2">
                        <a href="?edit=<?php echo $row['id']; ?>" class="flex-1 px-4 py-2 bg-blue-500 text-white text-center rounded-lg hover:bg-blue-600 transition text-sm">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                        <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Hapus foto ini dari galeri?')" class="flex-1 px-4 py-2 bg-red-500 text-white text-center rounded-lg hover:bg-red-600 transition text-sm">
                            <i class="fas fa-trash mr-1"></i>Hapus
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-span-full text-center py-12 bg-white rounded-xl">
            <i class="fas fa-images text-gray-300 text-6xl mb-4"></i>
            <p class="text-gray-500 text-lg">Belum ada foto di galeri</p>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
