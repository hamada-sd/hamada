<?php
/*
require 'includes/db.php';

if (isset($_GET['folder_id'])) {
    $folder_id = (int)$_GET['folder_id'];

    // استعلام لجلب الملفات الخاصة بالمجلد
    $stmt = $pdo->prepare("SELECT f.*, fs.token FROM files f JOIN file_shares fs ON f.id = fs.file_id WHERE f.folder_id = ?");
    $stmt->execute([$folder_id]);
    $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($files);
}*/
?>
<?php
/*
require 'includes/db.php';

if (isset($_GET['folder_id'])) {
    $folder_id = (int)$_GET['folder_id'];

    // استعلام لجلب جميع الملفات، حتى التي بدون مشاركة
    $stmt = $pdo->prepare("
        SELECT 
            f.*, 
            COALESCE(fs.token, 'غير قابل للتنزيل') AS token
        FROM 
            files f
        LEFT JOIN 
            file_shares fs 
        ON 
            f.id = fs.file_id
        WHERE 
            f.folder_id = ?
    ");
    
    $stmt->execute([$folder_id]);
    $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($files);
}*/
?>
<?php
require 'includes/db.php';

if (isset($_GET['folder_id'])) {
    $folder_id = (int)$_GET['folder_id'];

    $stmt = $pdo->prepare("
        SELECT 
            f.*, 
            COALESCE(fs.token, 'غير قابل للتنزيل') AS token,
            fs.usage_limit
        FROM 
            files f
        LEFT JOIN 
            file_shares fs 
        ON 
            f.id = fs.file_id
        WHERE 
            f.folder_id = ?
    ");
    
    $stmt->execute([$folder_id]);
    $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($files);
}
?>
