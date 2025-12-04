<?php
header('Content-Type: application/json');
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    
    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Semua field harus diisi']);
        exit;
    }
    
    $db = new Database();
    
    $name = $db->escape($name);
    $email = $db->escape($email);
    $subject = $db->escape($subject);
    $message = $db->escape($message);
    
    $sql = "INSERT INTO contact_messages (name, email, subject, message) VALUES ('$name', '$email', '$subject', '$message')";
    
    if ($db->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Pesan berhasil dikirim']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal mengirim pesan']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
