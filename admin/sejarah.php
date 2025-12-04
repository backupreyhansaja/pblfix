<?php
require_once 'includes/auth.php';
require_once '../config/database.php';

$pageTitle = 'Sejarah';
$db = new Database();

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $db->escape($_POST['content']);
    
    // Create JSON data
    $jsonData = json_encode(['content' => $content]);
    $jsonData = $db->escape($jsonData);
    
    // Check if record exists
    $result = $db->query("SELECT id FROM content_dashboard WHERE type = 'sejarah' LIMIT 1");
    $existing = $db->fetch($result);
    
    if ($existing) {
        $sql = "UPDATE content_dashboard SET data = '$jsonData', updated_at = CURRENT_TIMESTAMP WHERE id = {$existing['id']}";
    } else {
        $sql = "INSERT INTO content_dashboard (title, type, data) VALUES ('Sejarah Laboratorium', 'sejarah', '$jsonData')";
    }
    
    if ($db->query($sql)) {
        $success = 'Data berhasil disimpan!';
    } else {
        $error = 'Gagal menyimpan data!';
    }
}

// Fetch current data
$result = $db->query("SELECT * FROM content_dashboard WHERE type = 'sejarah' LIMIT 1");
$row = $db->fetch($result);
$data = [];
if ($row && !empty($row['data'])) {
    $jsonData = json_decode($row['data'], true);
    if ($jsonData) {
        $data = ['content' => $jsonData['content']];
    }
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

<div class="bg-white rounded-xl shadow-md p-6">
    <form method="POST" action="">
        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2">
                <i class="fas fa-book mr-2"></i>Sejarah Laboratorium
            </label>
            <textarea name="content" rows="15" required 
                      class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 outline-none transition"
                      placeholder="Masukkan sejarah laboratorium..."><?php echo htmlspecialchars($data['content'] ?? ''); ?></textarea>
            <p class="text-sm text-gray-600 mt-2">
                <i class="fas fa-info-circle mr-1"></i>Tips: Pisahkan setiap paragraf dengan enter ganda
            </p>
        </div>
        
        <div class="flex justify-end">
            <button type="submit" class="px-6 py-3 text-white rounded-lg hover:shadow-lg transition" style="background-color : blue">
                <i class="fas fa-save mr-2"></i>Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
