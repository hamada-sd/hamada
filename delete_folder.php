<?php
session_start();
require 'includes/auth.php';
require 'includes/db.php';

// التحقق من وجود المجلد
if (isset($_GET['folder_id'])) {
    $folder_id = $_GET['folder_id'];

    // حذف جميع الملفات المرتبطة بالمجلد
    $stmt_files = $pdo->prepare("SELECT filepath FROM files WHERE folder_id = ?");
    $stmt_files->execute([$folder_id]);
    $files = $stmt_files->fetchAll();
    foreach ($files as $file) {
        unlink($file['filepath']);  // حذف الملف من السيرفر
    }

    // حذف المجلد من قاعدة البيانات
    $stmt = $pdo->prepare("DELETE FROM folders WHERE id = ? AND user_id = ?");
    $stmt->execute([$folder_id, $_SESSION['user_id']]);

    // إعادة التوجيه إلى صفحة المجلدات
    header("Location: folders.php");
    exit;
}
?>
