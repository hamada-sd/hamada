<?php
/*
session_start();
require 'includes/auth.php';
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'], $_POST['folder_id'])) {
    $user_id = $_SESSION['user_id'];
    $folder_id = (int)$_POST['folder_id'];
    $file = $_FILES['file'];

    $folder_path = "uploads/{$user_id}/{$folder_id}";
    if (!file_exists($folder_path)) {
        mkdir($folder_path, 0777, true);
    }

    $target_path = $folder_path . "/" . basename($file['name']);
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        // حفظ الملف في قاعدة البيانات
        $stmt = $pdo->prepare("INSERT INTO files (user_id, filename, filepath, folder_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $file['name'], $target_path, $folder_id]);
        echo "تم الحفظ";
    } else {
        http_response_code(500);
        echo "خطأ في رفع الملف";
    }
}*/
?>
<?php
session_start();
require 'includes/auth.php';
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'], $_POST['folder_id'])) {
    $user_id = $_SESSION['user_id'];
    $folder_id = (int)$_POST['folder_id'];
    $file = $_FILES['file'];

    $allowed_extensions = [
        'jpg','jpeg','png','gif','bmp','webp','tiff','svg',
        'mp3','wav','ogg','m4a','flac','aac','wma',
        'mp4','mov','avi','mkv','webm','wmv',
        'doc','docx','xls','xlsx','ppt','pptx','pdf','txt',
        'zip','rar','7z'
    ];

    $filename = basename($file['name']);
    $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (!in_array($file_ext, $allowed_extensions)) {
        http_response_code(400);
        echo "نوع الملف غير مسموح.";
        exit;
    }

    // الحصول على نوع MIME
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowed_mime_types = [
        'image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/webp', 'image/tiff', 'image/svg+xml',
        'audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/x-m4a', 'audio/flac', 'audio/aac', 'audio/x-ms-wma',
        'video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/x-matroska', 'video/webm', 'video/x-ms-wmv',
        'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'text/plain', 'application/zip', 'application/x-rar-compressed', 'application/x-7z-compressed'
    ];

    if (!in_array($mime_type, $allowed_mime_types)) {
        http_response_code(400);
        echo "نوع الملف الحقيقي غير مدعوم.";
        exit;
    }

    $folder_path = "uploads/{$user_id}/{$folder_id}";
    if (!file_exists($folder_path)) {
        mkdir($folder_path, 0777, true);
    }

    $target_path = $folder_path . "/" . $filename;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        // ✅ حفظ نوع الملف في قاعدة البيانات (عمود type)
        $stmt = $pdo->prepare("INSERT INTO files (user_id, filename, filepath, folder_id, type) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $filename, $target_path, $folder_id, $mime_type]);

        echo "تم الحفظ";
    } else {
        http_response_code(500);
        echo "خطأ في رفع الملف";
    }
}
