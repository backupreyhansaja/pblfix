<?php
session_start();
require_once 'config/database.php';

// Initialize variables
$success = '';
$error = '';
$nama = '';
$email = '';
$pesan = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    
    // Get and validate input
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pesan = trim($_POST['pesan'] ?? '');
    $subject = trim($_POST['subject'] ?? 'Pesan dari Website');
    
    // Validation
    if (empty($nama)) {
        $error = 'Nama harus diisi!';
    } elseif (empty($email)) {
        $error = 'Email harus diisi!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid!';
    } elseif (empty($pesan)) {
        $error = 'Pesan harus diisi!';
    } else {
        // Escape input for SQL
        $namaEsc = $db->escape($nama);
        $emailEsc = $db->escape($email);
        $subjectEsc = $db->escape($subject);
        $pesanEsc = $db->escape($pesan);
        
        // Insert to database
        $sql = "INSERT INTO contact_messages (name, email, subject, message, is_read, created_at) 
                VALUES ('$namaEsc', '$emailEsc', '$subjectEsc', '$pesanEsc', FALSE, CURRENT_TIMESTAMP)";
        
        if ($db->query($sql)) {
            $_SESSION['message_success'] = 'Terima kasih! Pesan Anda berhasil dikirim. Kami akan segera menghubungi Anda.';
            $_SESSION['message_type'] = 'success';
            
            // Redirect to prevent form resubmission
            header('Location: index.php#contact');
            exit;
        } else {
            $error = 'Gagal mengirim pesan. Silakan coba lagi.';
        }
    }
    
    // Store error in session for redirect
    if ($error) {
        $_SESSION['message_error'] = $error;
        $_SESSION['message_type'] = 'error';
        $_SESSION['form_data'] = [
            'nama' => $nama,
            'email' => $email,
            'pesan' => $pesan
        ];
    }
}

// Get from parameter (for page redirect)
$from = $_GET['from'] ?? 'index';

// Determine redirect location
if (isset($_SERVER['HTTP_REFERER'])) {
    $redirectUrl = $_SERVER['HTTP_REFERER'];
} else {
    $redirectUrl = 'index.php#contact';
}

// Redirect back to form
header('Location: ' . $redirectUrl);
exit;

