<?php
// تفعيل عرض جميع أنواع الأخطاء
error_reporting(E_ALL);

// جعل الأخطاء تظهر على الشاشة
ini_set('display_errors', 1);

// اختياري: تفعيل سجل الأخطاء (يتم حفظ الأخطاء في ملف لوج بدلاً من الشاشة)
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php-error.log'); // تغيير المسار حسب الحاجة
?>
<?php
session_start();
require 'includes/auth.php';
require 'includes/db.php';

// جلب المجلدات الخاصة بالمستخدم
$stmt_folders = $pdo->prepare("SELECT * FROM folders WHERE user_id = ?");
$stmt_folders->execute([$_SESSION['user_id']]);
$folders = $stmt_folders->fetchAll();

// التحقق من رفع الملف
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'], $_POST['folder_id'])) {
    $folder_id = $_POST['folder_id'];
    $file = $_FILES['file'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'حدث خطأ أثناء رفع الملف.';
    } else {
        $folder_path = "uploads/" . $_SESSION['user_id'] . "/" . $folder_id;
        if (!file_exists($folder_path)) {
            mkdir($folder_path, 0777, true);  // إنشاء المجلد إذا لم يكن موجودًا
        }

        $file_path = $folder_path . "/" . basename($file['name']);
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            // إضافة الملف إلى قاعدة البيانات
            $stmt = $pdo->prepare("INSERT INTO files (user_id, filename, filepath, folder_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $file['name'], $file_path, $folder_id]);

            // الحصول على id الملف
            $file_id = $pdo->lastInsertId();

            // إذا كان المستخدم يرغب في إنشاء رابط
            if (isset($_POST['create_link']) && $_POST['create_link'] === '1') {
                $token = bin2hex(random_bytes(16));  // توليد توكن عشوائي
                $expires_at = NULL;  // يمكن تخصيص صلاحية إذا رغبت
                $usage_limit = 1;  // الرابط قابل للاستخدام مرة واحدة فقط، يمكن تغييره لاحقًا

                // إدخال التوكن في قاعدة البيانات
                $stmt_link = $pdo->prepare("INSERT INTO file_shares (file_id, token, expires_at, usage_limit) VALUES (?, ?, ?, ?)");
                $stmt_link->execute([$file_id, $token, $expires_at, $usage_limit]);

                $success = 'تم رفع الملف مع رابط المشاركة بنجاح!';
            } else {
                $success = 'تم رفع الملف بنجاح!';
            }
        } else {
            $error = 'فشل رفع الملف.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رفع الملفات</title>
    <style>
        :root {
            --main-color: #62d1d2;
            --dark-color: #142223;
            --bg-color: #f5f5f5;
            --text-color: #142223;
            --light-gray: #e0e0e0;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: Arial, sans-serif;
            direction: rtl;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }

        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: var(--main-color);
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }

        .card-body {
            padding: 20px;
        }

        .form-control {
            border: 1px solid var(--light-gray);
            border-radius: 4px;
            padding: 10px;
            width: 100%;
            margin-bottom: 15px;
        }

        .btn {
            background-color: var(--main-color);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn:hover {
            background-color: #4ab8b9;
        }

        .form-check-label {
            margin-right: 10px;
        }

        .form-check {
            margin-bottom: 20px;
        }

        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
<?php require 'includes/navbar.php';?>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h4>رفع ملف</h4>
            </div>
            <div class="card-body">
                <h5>اختر الملف لرفعه</h5>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div>
                        <label for="folder_id">اختر المجلد</label>
                        <select id="folder_id" name="folder_id" class="form-control" required>
                            <?php foreach ($folders as $folder): ?>
                                <option value="<?= $folder['id'] ?>"><?= htmlspecialchars($folder['folder_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="file">اختر الملف</label>
                        <input type="file" id="file" name="file" class="form-control" required>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" id="create_link" name="create_link" value="1" class="form-check-input">
                        <label for="create_link" class="form-check-label">إنشاء رابط مشاركة</label>
                    </div>

                    <button type="submit" class="btn">رفع الملف</button><br><br>
                    
                    <a href="upload2.php" class="btn">رفع ملفات كثيرة أو كبيرة الحجم</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
