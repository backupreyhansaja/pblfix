<?php
require_once 'includes/auth.php';
require_once '../config/database.php';
require_once '../config/mailer.php';

$pageTitle = 'Pesan Masuk';
$db = new Database();

$success = '';
$error = '';

/* -------------------------------
   HANDLE SEND REPLY (SMTP)
---------------------------------*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_email'])) {
    
    $reply_email = filter_var($_POST['reply_email'] ?? '', FILTER_VALIDATE_EMAIL);
    $reply_subject = trim($_POST['reply_subject'] ?? '');
    $reply_body = trim($_POST['reply_body'] ?? '');
    $reply_message_id = (int)($_POST['reply_message_id'] ?? 0);

    if (!$reply_email) {
        $error = 'Alamat email tujuan tidak valid.';
    } elseif ($reply_subject === '' || $reply_body === '') {
        $error = 'Subjek dan isi balasan wajib diisi.';
    } else {

        $from_email = $_SESSION['admin_email'] ?? 'noreply@yourdomain.com';
        $from_name  = $_SESSION['admin_name']  ?? 'Admin';

        // HTML Body
        $htmlBody = '
        <div style="font-family: Arial, sans-serif; color:#222;">
            <p>Halo,</p>
            <div style="margin:16px 0; padding:12px; background:#f7f7f7; border-radius:6px;">
                ' . nl2br(htmlspecialchars($reply_body)) . '
            </div>
            <p>Salam,<br>' . htmlspecialchars($from_name) . '</p>
        </div>';

        // Send SMTP
        $sent = sendMailSMTP($reply_email, $reply_subject, $htmlBody, $from_name, $from_email);

        if ($sent) {
            $success = 'Balasan berhasil dikirim.';

            if ($reply_message_id > 0) {
                $db->query("UPDATE contact_messages SET is_read = TRUE WHERE id = $reply_message_id");
            }
        } else {
            $error = 'Gagal mengirim email! Periksa konfigurasi SMTP.';
        }
    }
}

/* DELETE */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($db->query("DELETE FROM contact_messages WHERE id = $id")) {
        $success = 'Pesan berhasil dihapus!';
    } else {
        $error = 'Gagal menghapus pesan!';
    }
}

/* MARK AS READ */
if (isset($_GET['read'])) {
    $id = (int)$_GET['read'];
    $db->query("UPDATE contact_messages SET is_read = TRUE WHERE id = $id");
    $success = 'Pesan ditandai sudah dibaca.';
}

/* FETCH DATA */
$result = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
$data = $db->fetchAll($result);

include 'includes/header.php';
?>

<!-- ALERT -->
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


<div class="bg-white rounded-xl shadow-md overflow-hidden">
    <?php if ($data): ?>
    <div class="divide-y divide-gray-200">

        <?php foreach ($data as $msg): ?>
        <div class="p-6 <?php echo !$msg['is_read'] ? 'bg-blue-50' : ''; ?>">
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-blue-500 rounded-full flex items-center justify-center text-white">
                        <i class="fas fa-user text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 text-lg">
                            <?php echo htmlspecialchars($msg['name']); ?>
                            <?php if (!$msg['is_read']): ?>
                                <span class="ml-2 px-2 py-1 bg-red-500 text-white text-xs rounded-full">Baru</span>
                            <?php endif; ?>
                        </h4>
                        <p class="text-sm text-gray-600"><?php echo htmlspecialchars($msg['email']); ?></p>
                    </div>
                </div>

                <p class="text-sm text-gray-500">
                    <?php echo date('d M Y, H:i', strtotime($msg['created_at'])); ?>
                </p>
            </div>

            <?php if ($msg['subject']): ?>
            <p class="font-semibold text-gray-700 mb-3">
                <i class="fas fa-tag mr-2"></i>
                Subjek: <?php echo htmlspecialchars($msg['subject']); ?>
            </p>
            <?php endif; ?>

            <div class="bg-gray-100 rounded-lg p-4 mb-4">
                <p><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
            </div>

            <div class="flex space-x-2">

                <?php if (!$msg['is_read']): ?>
                <a href="?read=<?php echo $msg['id']; ?>" class="px-4 py-2 bg-green-500 text-white rounded-lg">Tandai Dibaca</a>
                <?php endif; ?>

                <button
                    class="px-4 py-2 bg-blue-500 text-white rounded-lg"
                    onclick="openReplyModal('<?php echo htmlspecialchars($msg['email'], ENT_QUOTES); ?>', '<?php echo $msg['id']; ?>', '<?php echo htmlspecialchars($msg['name'], ENT_QUOTES); ?>')"
                >
                    Balas
                </button>

                <a href="?delete=<?php echo $msg['id']; ?>" class="px-4 py-2 bg-red-500 text-white rounded-lg"
                   onclick="return confirm('Hapus pesan ini?')">
                    Hapus
                </a>
            </div>
        </div>
        <?php endforeach; ?>

    </div>
    <?php else: ?>
        <div class="p-12 text-center text-gray-500">
            <i class="fas fa-inbox text-6xl mb-4"></i>
            <p class="text-lg">Belum ada pesan masuk</p>
        </div>
    <?php endif; ?>
</div>

<!-- REPLY MODAL -->
<div id="replyModal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-2xl rounded-xl shadow-xl p-6 mx-4">
        <h2 class="text-xl font-bold mb-4">Balas Pesan</h2>

        <form method="POST">
            <input type="hidden" name="reply_email" id="reply_email">
            <input type="hidden" name="reply_message_id" id="reply_message_id">

            <div class="mb-4">
                <label class="block">Kepada</label>
                <input type="text" id="reply_to_display" disabled class="w-full border px-3 py-2 bg-gray-100">
            </div>

            <div class="mb-4">
                <label class="block">Subjek</label>
                <input type="text" name="reply_subject" id="reply_subject" class="w-full border px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label class="block">Isi Balasan</label>
                <textarea name="reply_body" id="reply_body" rows="6" class="w-full border px-3 py-2" required></textarea>
            </div>

            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeReplyModal()" class="px-4 py-2 bg-gray-300">Batal</button>
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white">Kirim</button>
            </div>
        </form>
    </div>
</div>

<script>
function openReplyModal(email, id, name) {
    document.getElementById('reply_email').value = email;
    document.getElementById('reply_message_id').value = id;
    document.getElementById('reply_to_display').value = name + " <" + email + ">";
    document.getElementById('reply_subject').value = "Re: ";
    document.getElementById('reply_body').value = "";
    document.getElementById('replyModal').classList.remove('hidden');
    document.getElementById('replyModal').classList.add('flex');
}

function closeReplyModal() {
    document.getElementById('replyModal').classList.add('hidden');
    document.getElementById('replyModal').classList.remove('flex');
}
</script>

<?php include 'includes/footer.php'; ?>
