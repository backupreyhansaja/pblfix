<?php
require_once 'includes/auth.php';
require_once '../config/database.php';

$pageTitle = 'Kelola Scope';
$db = new Database();

$success = '';
$error = '';

/* ======================================
   DELETE DATA
====================================== */
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    if ($db->query("DELETE FROM scope WHERE id = $id")) {
        $success = "Scope berhasil dihapus!";
    } else {
        $error = "Gagal menghapus scope!";
    }
}

/* ======================================
   INSERT / UPDATE DATA
====================================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id      = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $title   = $db->escape($_POST['title']);
    $desc    = $db->escape($_POST['description']);
    $icon    = $db->escape($_POST['icon']);
    $color   = $db->escape($_POST['color']);
    $urutan  = (int) ($_POST['urutan'] ?? 0);
    $adminId = $_SESSION['admin_id'];

    if (!$title || !$desc || !$icon) {
        $error = "Judul, deskripsi, dan icon wajib diisi!";
    }

    if (!$error) {
        // UPDATE
        if ($id > 0) {
            $sql = "
                UPDATE scope SET
                    title = '$title',
                    description = '$desc',
                    icon = '$icon',
                    color = '$color',
                    urutan = $urutan,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = $id
            ";
        } 
        // INSERT
        else {
            $sql = "
                INSERT INTO scope (title, description, icon, color, urutan, uploaded_by)
                VALUES ('$title', '$desc', '$icon', '$color', $urutan, $adminId)
            ";
        }

        if ($db->query($sql)) {
            $success = $id > 0 ? "Scope berhasil diupdate!" : "Scope berhasil ditambahkan!";
        } else {
            $error = "Gagal menyimpan data!";
        }
    }
}

/* ======================================
   FETCH DATA
====================================== */
$result = $db->query("SELECT s.*, a.full_name AS uploader 
                      FROM scope s 
                      LEFT JOIN admin_users a ON s.uploaded_by = a.id
                      ORDER BY urutan ASC, created_at DESC");

$data = $db->fetchAll($result);

/* Get data untuk edit */
$editData = null;
if (isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    $res = $db->query("SELECT * FROM scope WHERE id = $editId LIMIT 1");
    $editData = $db->fetch($res);
}

include 'includes/header.php';
?>

<!-- ALERTS -->
<?php if ($success): ?>
<div class="bg-green-50 border-l-4 border-green-500 p-4 rounded mb-6">
    <p class="text-green-700"><?= $success ?></p>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="bg-red-50 border-l-4 border-red-500 p-4 rounded mb-6">
    <p class="text-red-700"><?= $error ?></p>
</div>
<?php endif; ?>

<!-- FORM -->
<div class="bg-white rounded-xl shadow-md p-6 mb-8">
    <h3 class="text-lg font-bold mb-4">
        <?= $editData ? "Edit Scope" : "Tambah Scope" ?>
    </h3>

    <form method="POST">
        <?php if ($editData): ?>
            <input type="hidden" name="id" value="<?= $editData['id'] ?>">
        <?php endif; ?>

        <div class="mb-4">
            <label class="font-semibold">Judul *</label>
            <input type="text" name="title" required
                   value="<?= htmlspecialchars($editData['title'] ?? '') ?>"
                   class="w-full p-2 border rounded-lg">
        </div>

        <div class="mb-4">
            <label class="font-semibold">Deskripsi *</label>
            <textarea name="description" rows="3" required
                      class="w-full p-2 border rounded-lg"><?= htmlspecialchars($editData['description'] ?? '') ?></textarea>
        </div>

        <div class="mb-4">
            <label class="font-semibold">Icon *</label>
            <select name="icon" required class="w-full p-2 border rounded-lg">
                <option value="">-- Pilih Icon --</option>

                <?php
                // daftar icon (BISA DITAMBAH)
                $icons = [
                    "fa-solid fa-brain"      => "Brain",
                    "fa-solid fa-book"       => "Book",
                    "fa-solid fa-users"      => "Users",
                    "fa-solid fa-graduation-cap" => "Graduation Cap",
                    "fa-solid fa-layer-group" => "Layer Group",
                    "fa-solid fa-lightbulb"  => "Lightbulb",
                    "fa-solid fa-pen-nib"    => "Pen Nib",
                    "fa-solid fa-code"       => "Code",
                ];

                $selectedIcon = $editData['icon'] ?? "";
                foreach ($icons as $class => $label):
                ?>
                    <option value="<?= $class ?>" <?= $selectedIcon === $class ? 'selected' : '' ?>>
                        <?= $label ?> (<?= $class ?>)
                    </option>
                <?php endforeach; ?>    
            </select>
        </div>


        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="font-semibold">Warna (Hex)</label> <br>
                <input type="color" name="color"
                   value="<?= htmlspecialchars($editData['color'] ?? '#6C5CE7') ?>"
                   class="w-20 h-10 border rounded">
            </div>

            <!-- <div>
                <label class="font-semibold">Urutan</label>
                <input type="number" name="urutan"
                       value="<?= htmlspecialchars($editData['urutan'] ?? 0) ?>"
                       class="w-full p-2 border rounded-lg">
            </div> -->
        </div>

        <div class="flex justify-end">
            <?php if ($editData): ?>
                <a href="scope.php" class="px-4 py-2 bg-gray-400 text-white rounded-lg">Batal</a>
            <?php endif; ?>
            <button class="px-5 py-2 bg-blue-600 text-white rounded-lg">
                <?= $editData ? 'Update' : 'Simpan' ?>
            </button>
        </div>
    </form>
</div>

<!-- GRID -->
<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
<?php foreach ($data as $row): ?>
    <div class="bg-white rounded-xl shadow-lg p-5 hover:shadow-xl transition">
        
        <div class="text-5xl mb-4 <?= $row['color'] ? '' : 'text-purple-600' ?>"
             style="color: <?= $row['color'] ?>;">
            <i class="<?= htmlspecialchars($row['icon']) ?>"></i>
        </div>

        <h4 class="font-bold text-lg mb-2"><?= $row['title'] ?></h4>
        <p class="text-gray-600 mb-3"><?= $row['description'] ?></p>

        <p class="text-xs text-gray-500 mb-2">
            Diunggah oleh: <b><?= $row['uploader'] ?: 'Unknown' ?></b>
        </p>

        <div class="flex space-x-2">
            <a href="?edit=<?= $row['id'] ?>" 
               class="flex-1 px-3 py-2 bg-blue-500 text-white rounded-lg text-center">Edit</a>

            <a href="?delete=<?= $row['id'] ?>"
               onclick="return confirm('Hapus data ini?')"
               class="flex-1 px-3 py-2 bg-red-500 text-white rounded-lg text-center">Hapus</a>
        </div>
    </div>
<?php endforeach; ?>
</div>

<?php include 'includes/footer.php'; ?>
