<?php
/*
require 'includes/db.php';

if (isset($_GET['file'])) {
    $filepath = $_GET['file'];
    if (file_exists($filepath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
        readfile($filepath);
        exit();
    } else {
        echo "الملف غير موجود.";
    }
}*/
?>
<?php

/*
require 'includes/db.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // البحث عن التوكن في قاعدة البيانات
    $stmt = $pdo->prepare("SELECT fs.*, f.filename, f.filepath FROM file_shares fs JOIN files f ON fs.file_id = f.id WHERE fs.token = ?");
    $stmt->execute([$token]);
    $share = $stmt->fetch();

    if ($share) {
        // التحقق من صلاحية الرابط (إذا كان منتهيًا أو تم استخدامه بالفعل)
        if ($share['usage_limit'] > 0) {
            // تنزيل الملف
            $file_path = $share['filepath'];
            $file_name = $share['filename'];

            // تحديث عدد مرات الاستخدام
            $stmt_update = $pdo->prepare("UPDATE file_shares SET usage_limit = usage_limit - 1 WHERE token = ?");
            $stmt_update->execute([$token]);

            // بدء تنزيل الملف
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
            readfile($file_path);
            exit;
        } else {
            echo 'عذراً، تم استخدام الرابط بالفعل.';
        }
    } else {
        echo 'الرابط غير صالح.';
    }
} else {
    echo 'عذراً، الرابط غير موجود.';
}*/
?>
<?php
require 'includes/db.php';

if (!isset($_GET['token'])) {
    die("الرابط غير صالح.");
}

$token = $_GET['token'];

// جلب الرابط والملف
$stmt = $pdo->prepare("SELECT fs.*, f.filename, f.filepath FROM file_shares fs JOIN files f ON fs.file_id = f.id WHERE fs.token = ?");
$stmt->execute([$token]);
$share = $stmt->fetch();

if (!$share) {
    die("الرابط غير موجود.");
}

// التحقق من صلاحية التاريخ
if ($share['expires_at'] && strtotime($share['expires_at']) < time()) {
    die("هذا الرابط منتهي الصلاحية.");
}

// التحقق من الحد الأقصى للتحميلات
if ($share['usage_limit'] <= 0) {
    die("تم تجاوز عدد مرات التحميل.");
}

// تنزيل الملف
$file_path = $share['filepath'];
$file_name = $share['filename'];

// تقليل عدد الاستخدامات
$stmt = $pdo->prepare("UPDATE file_shares SET usage_limit = usage_limit - 1 WHERE token = ?");
$stmt->execute([$token]);

// إرسال الملف
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
readfile($file_path);
exit;
