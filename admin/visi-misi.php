<?php
require_once 'includes/auth.php';
require_once '../config/database.php';

$pageTitle = 'Visi & Misi';
$db = new Database();

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $visi = $db->escape($_POST['visi']);
    $misi = $db->escape($_POST['misi']);
    
    // Create JSON data
    $jsonData = json_encode(['visi' => $visi, 'misi' => $misi]);
    $jsonData = $db->escape($jsonData);
    
    // Check if record exists
    $result = $db->query("SELECT id FROM content_dashboard WHERE type = 'visi_misi' LIMIT 1");
    $existing = $db->fetch($result);
    
    if ($existing) {
        $sql = "UPDATE content_dashboard SET data = '$jsonData', updated_at = CURRENT_TIMESTAMP WHERE id = {$existing['id']}";
    } else {
        $sql = "INSERT INTO content_dashboard (title, type, data) VALUES ('Visi Misi Laboratorium', 'visi_misi', '$jsonData')";
    }
    
    if ($db->query($sql)) {
        $success = 'Data berhasil disimpan!';
    } else {
        $error = 'Gagal menyimpan data!';
    }
}

// Fetch current data
$result = $db->query("SELECT * FROM content_dashboard WHERE type = 'visi_misi' LIMIT 1");
$row = $db->fetch($result);
$data = [];
if ($row && !empty($row['data'])) {
    $jsonData = json_decode($row['data'], true);
    if ($jsonData) {
        $data = $jsonData;
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
                <i class="fas fa-eye mr-2"></i>Visi
            </label>
            <textarea name="visi" rows="4" required 
                      class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 outline-none transition"
                      placeholder="Masukkan visi laboratorium..."><?php echo htmlspecialchars($data['visi'] ?? ''); ?></textarea>
        </div>
        
        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2">
                <i class="fas fa-bullseye mr-2"></i>Misi
            </label>
            <textarea name="misi" rows="8" required 
                      class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 outline-none transition"
                      placeholder="Masukkan misi laboratorium (pisahkan dengan enter untuk setiap poin)..."><?php echo htmlspecialchars($data['misi'] ?? ''); ?></textarea>
            <p class="text-sm text-gray-600 mt-2">
                <i class="fas fa-info-circle mr-1"></i>Tips: Gunakan format nomor (1., 2., dst) untuk setiap poin misi
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
