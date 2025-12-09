<?php
require_once 'includes/auth.php';
require_once '../config/database.php';

$pageTitle = 'Site Settings';
$db = new Database();

$success = '';
$error = '';

// Ensure contact_email column exists (will attempt later if needed)
// Fetch current settings row
$res = $db->query("SELECT * FROM setting_sosial_media LIMIT 1");
$settings = $res ? $db->fetch($res) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $alamat = $db->escape($_POST['alamat'] ?? '');
    $facebook = $db->escape($_POST['facebook'] ?? '');
    $twitter = $db->escape($_POST['twitter'] ?? '');
    $instagram = $db->escape($_POST['instagram'] ?? '');
    $no_hp = $db->escape($_POST['no_hp'] ?? '');
    $contact_email = $db->escape($_POST['contact_email'] ?? '');

    // Check if contact_email column exists
    $colCheck = $db->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'setting_sosial_media' AND column_name = 'contact_email'");
    if (!$colCheck || !$db->numRows($colCheck)) {
        // Try to add column
        $db->query("ALTER TABLE setting_sosial_media ADD COLUMN contact_email VARCHAR(255)");
    }

    if ($settings) {
        $id = (int)$settings['id'];
        $sql = "UPDATE setting_sosial_media SET facebook = '$facebook', twitter = '$twitter', instagram = '$instagram', no_hp = '$no_hp', contact_email = '$contact_email', alamat = '$alamat' WHERE id = $id";
    } else {
        $sql = "INSERT INTO setting_sosial_media (alamat, facebook, twitter, instagram, no_hp, contact_email) VALUES ( '$alamat', '$facebook', '$twitter', '$instagram', '$no_hp', '$contact_email')";
    }

    if ($db->query($sql)) {
        $success = 'Pengaturan berhasil disimpan.';
        $res = $db->query("SELECT * FROM setting_sosial_media LIMIT 1");
        $settings = $res ? $db->fetch($res) : null;
    } else {
        $error = 'Gagal menyimpan pengaturan.';
    }
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

<div class="bg-white rounded-xl shadow-md p-6 mb-6">
    <h3 class="text-lg font-bold mb-4">Site Contact & Social Settings</h3>

    <form method="POST">
        <div class="grid gap-4 mb-4">
            <div>
                <label class="block font-semibold mb-2">Alamat Lengkap</label>
                <input type="text" name="alamat" value="<?php echo htmlspecialchars($settings['alamat'] ?? ''); ?>" class="w-full px-4 py-2 border rounded-lg">
            </div>
        </div>
        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block font-semibold mb-2">Telepon (no_hp)</label>
                <input type="text" name="no_hp" value="<?php echo htmlspecialchars($settings['no_hp'] ?? ''); ?>" class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block font-semibold mb-2">Email Kontak</label>
                <input type="email" name="contact_email" value="<?php echo htmlspecialchars($settings['contact_email'] ?? ''); ?>" class="w-full px-4 py-2 border rounded-lg">
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block font-semibold mb-2">Facebook (URL)</label>
                <input type="text" name="facebook" value="<?php echo htmlspecialchars($settings['facebook'] ?? ''); ?>" class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block font-semibold mb-2">Twitter (URL)</label>
                <input type="text" name="twitter" value="<?php echo htmlspecialchars($settings['twitter'] ?? ''); ?>" class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block font-semibold mb-2">Instagram (URL)</label>
                <input type="text" name="instagram" value="<?php echo htmlspecialchars($settings['instagram'] ?? ''); ?>" class="w-full px-4 py-2 border rounded-lg">
            </div>
        </div>

        <div class="flex justify-end">
            <button class="px-6 py-2 bg-blue-600 text-white rounded-lg">Simpan</button>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
