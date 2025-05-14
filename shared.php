<?php
/*
session_start();
require 'includes/auth.php';
require 'includes/db.php';

if (!isset($_GET['file_id'])) {
    die("معرف الملف مفقود.");
}

$file_id = (int)$_GET['file_id'];

// تأكيد ملكية المستخدم للملف
$stmt = $pdo->prepare("SELECT * FROM files WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$file = $stmt->fetch();

if (!$file) {
    die("الملف غير موجود أو لا تملكه.");
}

$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usage_limit = (int)$_POST['usage_limit'];
    $expires_at = !empty($_POST['expires_at']) ? $_POST['expires_at'] : null;

    $token = bin2hex(random_bytes(16));

    $stmt = $pdo->prepare("INSERT INTO file_shares (file_id, token, expires_at, usage_limit) VALUES (?, ?, ?, ?)");
    $stmt->execute([$file_id, $token, $expires_at, $usage_limit]);

    $share_url = "http://" . $_SERVER['HTTP_HOST'] . "/download.php?token=" . $token;
    $success = "تم إنشاء الرابط: <a href='$share_url' target='_blank'>$share_url</a>";
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>مشاركة ملف</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php require 'includes/navbar.php'; ?>
<div class="container mt-4">
    <h3>🔗 مشاركة الملف: <?= htmlspecialchars($file['filename']) ?></h3>

    <?php if ($success): ?>
        <div class="alert alert-success mt-3"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" class="mt-4">
        <div class="mb-3">
            <label for="usage_limit" class="form-label">عدد مرات التحميل المسموح بها</label>
            <input type="number" name="usage_limit" id="usage_limit" class="form-control" value="1" min="1" required>
        </div>
        <div class="mb-3">
            <label for="expires_at" class="form-label">تاريخ انتهاء الرابط (اختياري)</label>
            <input type="datetime-local" name="expires_at" id="expires_at" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">إنشاء الرابط</button>
    </form>
</div>
</body>
</html>
    */ ?>
<?php
session_start();
require 'includes/auth.php';
require 'includes/db.php';

if (!isset($_GET['file_id'])) {
    die("معرف الملف مفقود.");
}

$file_id = (int)$_GET['file_id'];

// تأكيد ملكية المستخدم للملف
$stmt = $pdo->prepare("SELECT * FROM files WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$file = $stmt->fetch();

if (!$file) {
    die("الملف غير موجود أو لا تملكه.");
}

$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usage_limit = (int)$_POST['usage_limit'];
    $expires_at = !empty($_POST['expires_at']) ? $_POST['expires_at'] : null;

    $token = bin2hex(random_bytes(16));

    $stmt = $pdo->prepare("INSERT INTO file_shares (file_id, token, expires_at, usage_limit) VALUES (?, ?, ?, ?)");
    $stmt->execute([$file_id, $token, $expires_at, $usage_limit]);

    $share_url = "http://" . $_SERVER['HTTP_HOST'] . "/download.php?token=" . $token;
    $success = "تم إنشاء الرابط: <a href='$share_url' target='_blank'>$share_url</a>";
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مشاركة ملف</title>
    <style>
        :root {
            --main-color: #62d1d2;
            --dark-color: #142223;
            --bg-color: #f5f5f5;
            --text-color: #142223;
            --light-gray: #e0e0e0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: "Tahoma", sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
        }

        .container {
            width: 90%;
            max-width: 900px;
            margin: auto;
            padding: 20px 0;
        }

        header {
            background-color: var(--dark-color);
            color: white;
            padding: 20px 0;
            text-align: center;
        }

        footer {
            background-color: var(--dark-color);
            color: white;
            text-align: center;
            padding: 15px 0;
            margin-top: 30px;
        }

        .alert {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
        }

        h3 {
            color: var(--main-color);
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: bold;
        }

        .form-control {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid var(--light-gray);
            width: 100%;
        }

        .btn {
            background-color: var(--main-color);
            color: white;
            border-radius: 5px;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #4fa3a3;
        }

        .form-control:focus {
            border-color: var(--main-color);
            outline: none;
        }

    </style>
</head>
<body>

<?php require 'includes/navbar.php'; ?>

<div class="container mt-4">
    <h3>🔗 مشاركة الملف: <?= htmlspecialchars($file['filename']) ?></h3>

    <?php if ($success): ?>
        <div class="alert mt-3"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" class="mt-4">
        <div class="mb-3">
            <label for="usage_limit" class="form-label">عدد مرات التحميل المسموح بها</label>
            <input type="number" name="usage_limit" id="usage_limit" class="form-control" value="1" min="1" required>
        </div>
        <div class="mb-3">
            <label for="expires_at" class="form-label">تاريخ انتهاء الرابط (اختياري)</label>
            <input type="datetime-local" name="expires_at" id="expires_at" class="form-control">
        </div>
        <button type="submit" class="btn">إنشاء الرابط</button>
    </form>
</div>

<footer>
    <p>&copy; 2025 جميع الحقوق محفوظة</p>
</footer>

</body>
</html>
