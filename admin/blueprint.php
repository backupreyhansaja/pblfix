<?php
require_once 'includes/auth.php';
require_once '../config/database.php';

$pageTitle = 'Kelola Blueprint';
$db = new Database();

$success = '';
$error = '';

/* ================================
   DELETE BLUEPRINT
================================ */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($db->query("DELETE FROM blueprint WHERE id = $id")) {
        $success = "Blueprint berhasil dihapus!";
    } else {
        $error = "Gagal menghapus data!";
    }
}

/* ================================
   INSERT / UPDATE BLUEPRINT
================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id          = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $title       = $db->escape($_POST['title']);
    $description = $db->escape($_POST['description']);
    $icon        = $db->escape($_POST['icon']);
    $color       = $db->escape($_POST['color']);
    $urutan      = (int)($_POST['urutan'] ?? 0);
    $uploaded_by = $_SESSION['admin_id'];

    if (!$title || !$description || !$icon) {
        $error = "Semua field wajib diisi!";
    }

    if (!$error) {
        if ($id > 0) {
            // UPDATE
            $sql = "
                UPDATE blueprint SET 
                    title='$title',
                    description='$description',
                    icon='$icon',
                    color='$color',
                    urutan=$urutan,
                    updated_at=CURRENT_TIMESTAMP
                WHERE id=$id
            ";
        } else {
            // INSERT
            $sql = "
                INSERT INTO blueprint (title, description, icon, color, urutan, uploaded_by)
                VALUES ('$title', '$description', '$icon', '$color', $urutan, $uploaded_by)
            ";
        }

        if ($db->query($sql)) {
            $success = "Blueprint berhasil disimpan!";
        } else {
            $error = "Gagal menyimpan blueprint!";
        }
    }
}

/* ================================
   FETCH ALL BLUEPRINT
================================ */
$result = $db->query("SELECT b.*, a.full_name AS uploader 
                      FROM blueprint b
                      LEFT JOIN admin_users a ON b.uploaded_by = a.id
                      ORDER BY urutan ASC, id DESC");
$data = $db->fetchAll($result);

/* ================================
   EDIT DATA
================================ */
$editData = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = $db->query("SELECT * FROM blueprint WHERE id = $id LIMIT 1");
    $editData = $db->fetch($res);
}

include 'includes/header.php';
$icons = include "../config/icons.php"; 
$selectedIcon = $editData['icon'] ?? "";
?>

<!-- SUCCESS -->
<?php if ($success): ?>
<div class="bg-green-50 border-l-4 border-green-600 p-4 mb-6 rounded">
    <p class="text-green-700"><?= $success ?></p>
</div>
<?php endif; ?>

<!-- ERROR -->
<?php if ($error): ?>
<div class="bg-red-50 border-l-4 border-red-600 p-4 mb-6 rounded">
    <p class="text-red-700"><?= $error ?></p>
</div>
<?php endif; ?>

<!-- FORM -->
<div class="bg-white rounded-xl shadow-md p-6 mb-6">
    <h3 class="text-lg font-bold mb-4 text-gray-800">
        <?= $editData ? 'Edit Blueprint' : 'Tambah Blueprint' ?>
    </h3>

    <form method="POST">
        <?php if ($editData): ?>
            <input type="hidden" name="id" value="<?= $editData['id'] ?>">
        <?php endif; ?>

        <div class="mb-4">
            <label class="block mb-2 font-semibold text-gray-700">Judul *</label>
            <input type="text" name="title" required
                   value="<?= htmlspecialchars($editData['title'] ?? '') ?>"
                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-300">
        </div>

        <div class="mb-4">
            <label class="block mb-2 font-semibold text-gray-700">Deskripsi *</label>
            <textarea name="description" rows="3" required
                      class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-300"><?= htmlspecialchars($editData['description'] ?? '') ?></textarea>
        </div>

        <div class="mb-4">
            <label class="font-semibold">Icon *</label>
            <input type="hidden" name="icon" id="iconInput" value="<?= $selectedIcon ?>">
            <!-- Dropdown Button -->
            <button id="dropdownBtn" type="button"
                    class="w-full flex items-center justify-between p-2 border rounded-lg bg-white">
                <span id="dropdownLabel" class="flex items-center gap-2">
                    <?php if ($selectedIcon): ?>
                        <i class="<?= $selectedIcon ?> text-xl"></i>
                    <?php endif; ?>
                    <?= $selectedIcon ?: "Pilih Icon" ?>
                </span>
                <i class="fa-solid fa-chevron-down"></i>
            </button>

            <!-- Dropdown List -->
            <div id="dropdownMenu" 
                class="hidden border rounded-lg mt-2 bg-white shadow-lg dropdown-icon-list">

                <?php foreach ($icons as $class => $label): ?>
                    <div class="flex items-center gap-3 p-2 cursor-pointer hover:bg-gray-100"
                        onclick="selectIcon('<?= $class ?>')">
                        <i class="<?= $class ?> text-lg"></i>
                        <span><?= $label ?> (<?= $class ?>)</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Preview -->
        <div id="iconPreview" class="mt-3 text-4xl">
            <?php if ($selectedIcon): ?>
                <i class="<?= $selectedIcon ?>"></i>
            <?php endif; ?>
        </div>


        <div class="mb-4">
            <label class="block mb-2 font-semibold text-gray-700">Warna Card</label>
            <input type="color" name="color" id="colorPicker"
                   value="<?= htmlspecialchars($editData['color'] ?? '#6C5CE7') ?>"
                   class="w-20 h-10 border rounded">
        </div>

        <!-- <div class="mb-4">
            <label class="block mb-2 font-semibold text-gray-700">Urutan</label>
            <input type="number" name="urutan"
                   value="<?= htmlspecialchars($editData['urutan'] ?? 0) ?>"
                   class="w-full px-4 py-2 border rounded-lg">
        </div> -->

        <div class="flex justify-end space-x-2">
            <?php if ($editData): ?>
                <a href="blueprint.php" class="px-4 py-2 bg-gray-400 text-white rounded-lg">Batal</a>
            <?php endif; ?>

            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Simpan
            </button>
        </div>
        <!-- Script preview and select icon -->
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            const dropdownBtn   = document.getElementById("dropdownBtn");
            const dropdownMenu  = document.getElementById("dropdownMenu");
            const dropdownLabel = document.getElementById("dropdownLabel");
            const iconInput     = document.getElementById("iconInput");
            const iconPreview   = document.getElementById("iconPreview");
            const colorPicker   = document.getElementById("colorPicker");

            function updatePreview() {
                const icon = iconInput.value || "";
                const color = colorPicker.value || "#000000";

                if (icon === "") {
                    iconPreview.innerHTML = "";
                    return;
                }

                iconPreview.innerHTML = 
                    `<i class="${icon}" style="font-size:48px; color:${color};"></i>`;
            }

            window.selectIcon = function(iconClass) {
                iconInput.value = iconClass;
                dropdownLabel.innerHTML = `<i class="${iconClass} text-xl"></i> ${iconClass}`;
                updatePreview();
                dropdownMenu.classList.add("hidden");
            }

            dropdownBtn.addEventListener("click", () => {
                dropdownMenu.classList.toggle("hidden");
            });

            colorPicker.addEventListener("input", updatePreview);

            document.addEventListener("click", function(e) {
                if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                    dropdownMenu.classList.add("hidden");
                }
            });

            updatePreview();
        });
        </script>
    </form>
</div>

<!-- LIST BLUEPRINT -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
<?php if ($data): ?>
    <?php foreach ($data as $row): ?>
        <div class="bg-white rounded-xl shadow-md p-5 hover:shadow-lg transition">

            <div class="w-16 h-16 rounded-full flex items-center justify-center text-white mb-4"
                 style="background: <?= $row['color'] ?: '#6C5CE7' ?>">
                <i class="<?= $row['icon'] ?> text-3xl"></i>
            </div>

            <h4 class="font-bold text-lg mb-1"><?= htmlspecialchars($row['title']) ?></h4>
            <p class="text-gray-600 text-sm mb-3"><?= htmlspecialchars($row['description']) ?></p>

            <p class="text-xs text-gray-400 mb-3">
                Uploaded by: <?= htmlspecialchars($row['uploader'] ?? 'Unknown') ?>
            </p>

            <div class="flex space-x-2">
                <a href="?edit=<?= $row['id'] ?>" 
                   class="flex-1 text-center py-2 bg-blue-500 text-white rounded-lg">
                    Edit
                </a>

                <a href="?delete=<?= $row['id'] ?>"
                   onclick="return confirm('Yakin ingin menghapus?')"
                   class="flex-1 text-center py-2 bg-red-500 text-white rounded-lg">
                    Hapus
                </a>
            </div>

        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="col-span-full text-center py-10 bg-white rounded-xl shadow">
        <p class="text-gray-500 text-lg">Belum ada data blueprint</p>
    </div>
<?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
