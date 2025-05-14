<?php
/*
session_start();
require 'includes/auth.php';
require 'includes/db.php';

// جلب المجلدات الخاصة بالمستخدم
$stmt_folders = $pdo->prepare("SELECT * FROM folders WHERE user_id = ?");
$stmt_folders->execute([$_SESSION['user_id']]);
$folders = $stmt_folders->fetchAll();

// إضافة مجلد جديد
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['folder_name'])) {
    $folder_name = trim($_POST['folder_name']);
    if (empty($folder_name)) {
        $error = 'يرجى إدخال اسم المجلد.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO folders (user_id, folder_name) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $folder_name]);
        $success = 'تم إضافة المجلد بنجاح!';
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المجلدات الخاصة بي</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php require 'includes/navbar.php';?>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>مجلداتك</h4>
                    </div>
                    <div class="card-body">
                        <h5>إنشاء مجلد جديد</h5>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="folder_name" class="form-label">اسم المجلد</label>
                                <input type="text" id="folder_name" name="folder_name" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary">إنشاء المجلد</button>
                        </form>

                        <hr>

                        <h5>مجلداتك</h5>
                        <div class="row">
                            <?php foreach ($folders as $folder): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h6 class="card-title"><?= htmlspecialchars($folder['folder_name']) ?></h6>
                                            <a href="files.php?folder_id=<?= $folder['id'] ?>" class="btn btn-info btn-sm">عرض الملفات</a>
                                            <a href="delete_folder.php?folder_id=<?= $folder['id'] ?>" class="btn btn-danger btn-sm mt-2">حذف المجلد</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
*/ ?>
<?php
session_start();
require 'includes/auth.php';
require 'includes/db.php';

// جلب المجلدات الخاصة بالمستخدم
$stmt_folders = $pdo->prepare("SELECT * FROM folders WHERE user_id = ?");
$stmt_folders->execute([$_SESSION['user_id']]);
$folders = $stmt_folders->fetchAll();

// إضافة مجلد جديد
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['folder_name'])) {
    $folder_name = trim($_POST['folder_name']);
    if (empty($folder_name)) {
        $error = 'يرجى إدخال اسم المجلد.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO folders (user_id, folder_name) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $folder_name]);
        $success = 'تم إضافة المجلد بنجاح!';
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>📁 مجلداتي</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg">

<?php require 'includes/navbar.php'; ?>

<main class="container">
    <section class="card">
        <h2>📂 إنشاء مجلد جديد</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" class="form">
            <label for="folder_name">اسم المجلد</label>
            <input type="text" id="folder_name" name="folder_name" required>
            <button type="submit" class="btn btn-primary">إنشاء المجلد</button>
        </form>
    </section>

    <section class="card mt">
        <h2>📁 مجلداتك</h2>

        <?php if (count($folders) === 0): ?>
            <p class="info">لا توجد مجلدات حالياً.</p>
        <?php else: ?>
            <div class="grid">
                <?php foreach ($folders as $folder): ?>
                    <div class="folder-box">
                        <h3><?= htmlspecialchars($folder['folder_name']) ?></h3>
                        <a href="files.php?folder_id=<?= $folder['id'] ?>" class="btn btn-sm btn-secondary">عرض الملفات</a>
                        <a href="delete_folder.php?folder_id=<?= $folder['id'] ?>" class="btn btn-sm btn-danger">حذف</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<footer>
    <p>&copy; 2025 جميع الحقوق محفوظة</p>
</footer>

</body>
</html>
