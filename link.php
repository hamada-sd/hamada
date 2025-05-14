<?php
session_start();
require 'includes/auth.php';
require 'includes/db.php';

$user_id = $_SESSION['user_id'];
$message = '';

// حذف الرابط
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $share_id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("
        DELETE fs FROM file_shares fs 
        JOIN files f ON fs.file_id = f.id 
        WHERE fs.id = ? AND f.user_id = ?
    ");
    $stmt->execute([$share_id, $user_id]);
    $message = "✅ تم حذف الرابط بنجاح.";
}

// تعديل الرابط
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['share_id'])) {
    $share_id = (int)$_POST['share_id'];
    $usage_limit = (int)$_POST['usage_limit'];
    $expiry_date = $_POST['expires_at'];
    $stmt = $pdo->prepare("
        UPDATE file_shares fs
        JOIN files f ON fs.file_id = f.id
        SET fs.usage_limit = ?, fs.expires_at = ?
        WHERE fs.id = ? AND f.user_id = ?
    ");
    $stmt->execute([$usage_limit, $expiry_date, $share_id, $user_id]);
    $message = "✅ تم تعديل الرابط بنجاح.";
}

// جلب الروابط
$stmt = $pdo->prepare("
    SELECT fs.*, f.filename 
    FROM file_shares fs
    JOIN files f ON fs.file_id = f.id
    WHERE f.user_id = ?
");
$stmt->execute([$user_id]);
$links = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة روابط المشاركة</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg">

<?php require 'includes/navbar.php'; ?>

<main class="container">
    <h2>🔗 إدارة روابط المشاركة</h2>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>

    <?php if (count($links) >= 0): ?>
        <?php foreach ($links as $link): ?>
            <div class="card mb-3">
                <div class="flex-space-between">
                    <strong><?= htmlspecialchars($link['filename']) ?></strong>
                    <a href="?delete=<?= $link['id'] ?>" onclick="return confirm('هل أنت متأكد من حذف الرابط؟')" class="btn btn-danger btn-sm">🗑️ حذف</a>
                </div>

                <p class="info-text">
                    🔗 <a href="download.php?token=<?= $link['token'] ?>" target="_blank" class="link">رابط التحميل</a><br>
                    مرات التحميل المتبقية: <?= $link['usage_limit'] ?><br>
                    تاريخ الانتهاء: <?= $link['expires_at'] ?: 'غير محدد' ?>
                </p>

                <form method="POST" class="form grid-form">
                    <input type="hidden" name="share_id" value="<?= $link['id'] ?>">

                    <div>
                        <label>عدد مرات التحميل</label>
                        <input type="number" name="usage_limit" value="<?= $link['usage_limit'] ?>" min="1">
                    </div>

                    <div>
                        <label>تاريخ الانتهاء</label>
                        <input type="datetime-local" name="expires_at" value="<?= $link['expires_at'] ? date('Y-m-d\TH:i', strtotime($link['expires_at'])) : '' ?>">
                    </div>

                    <div class="flex-end">
                        <button type="submit" class="btn btn-primary">💾 حفظ</button>
                    </div>
                </form>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="card">
            <p class="info-text">لا توجد روابط مشاركة حالياً.</p>
        </div>
    <?php endif; ?>
</main>

</body>
</html>

<?php
/*
session_start();
require 'includes/auth.php';
require 'includes/db.php';

$user_id = $_SESSION['user_id'];
$message = '';

// حذف الرابط
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $share_id = (int)$_GET['delete'];

    // تأكد أن الرابط يخص المستخدم
    $stmt = $pdo->prepare("
        DELETE fs FROM file_shares fs 
        JOIN files f ON fs.file_id = f.id 
        WHERE fs.id = ? AND f.user_id = ?
    ");
    $stmt->execute([$share_id, $user_id]);

    $message = "✅ تم حذف الرابط بنجاح.";
}

// تعديل الرابط
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['share_id'])) {
    $share_id = (int)$_POST['share_id'];
    $usage_limit = (int)$_POST['usage_limit'];
    $expiry_date = $_POST['expires_at'];

    // تأكد من ملكية الرابط
    $stmt = $pdo->prepare("
        UPDATE file_shares fs
        JOIN files f ON fs.file_id = f.id
        SET fs.usage_limit = ?, fs.expires_at = ?
        WHERE fs.id = ? AND f.user_id = ?
    ");
    $stmt->execute([$usage_limit, $expiry_date, $share_id, $user_id]);

    $message = "✅ تم تعديل الرابط بنجاح.";
}

// جلب روابط المشاركة للمستخدم
$stmt = $pdo->prepare("
    SELECT fs.*, f.filename 
    FROM file_shares fs
    JOIN files f ON fs.file_id = f.id
    WHERE f.user_id = ?
");
$stmt->execute([$user_id]);
$links = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة روابط المشاركة</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">

<div class="container mx-auto mt-10 px-4">
    <h2 class="text-xl font-bold mb-6">🔗 إدارة روابط المشاركة</h2>

    <?php if ($message): ?>
        <div class="bg-white border rounded p-4 shadow mb-6"><?= $message ?></div>
    <?php endif; ?>

    <?php if (count($links) > 0): ?>
        <div class="bg-white border rounded p-4 shadow space-y-4">
            <?php foreach ($links as $link): ?>
                <div class="border rounded px-4 py-3 space-y-2">
                    <div class="flex justify-between items-center">
                        <strong><?= htmlspecialchars($link['filename']) ?></strong>
                        <a href="?delete=<?= $link['id'] ?>" onclick="return confirm('هل أنت متأكد من حذف الرابط؟')" class="text-red-600 hover:underline">حذف الرابط</a>
                    </div>
                    <div class="text-sm text-gray-700">
                        <p>🔗 <a class="text-blue-600 underline" href="download.php?token=<?= $link['token'] ?>" target="_blank">رابط التحميل</a></p>
                        <p>مرات التحميل المتبقية: <?= $link['usage_limit'] ?></p>
                        <p>تاريخ الانتهاء: <?= $link['expires_at'] ?: 'غير محدد' ?></p>
                    </div>
                    <form method="POST" class="mt-2 grid grid-cols-1 md:grid-cols-3 gap-3">
                        <input type="hidden" name="share_id" value="<?= $link['id'] ?>">

                        <div>
                            <label class="block text-sm mb-1">عدد مرات التحميل</label>
                            <input type="number" name="usage_limit" value="<?= $link['usage_limit'] ?>" min="1" class="border px-2 py-1 rounded w-full">
                        </div>

                        <div>
                            <label class="block text-sm mb-1">تاريخ الانتهاء</label>
                            <input type="datetime-local" name="expires_at" value="<?= $link['expires_at'] ? date('Y-m-d\TH:i', strtotime($link['expires_at'])) : '' ?>" class="border px-2 py-1 rounded w-full">
                        </div>

                        <div class="flex items-end">
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">💾 حفظ التغييرات</button>
                        </div>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="bg-white border rounded p-4 shadow">
            لا توجد روابط مشاركة حالياً.
        </div>
    <?php endif; ?>
</div>

</body>
</html>

<?php
/*
session_start();
require 'includes/auth.php';
require 'includes/db.php';

$user_id = $_SESSION['user_id'];
$message = '';
$link_created = false;

// دالة لإنشاء توكن عشوائي
function generateToken($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

// التعامل مع رابط فيه file_id
if (isset($_GET['file_id'])) {
    $file_id = (int)$_GET['file_id'];

    // تأكد أن الملف يخص المستخدم
    $stmt = $pdo->prepare("SELECT * FROM files WHERE id = ? AND user_id = ?");
    $stmt->execute([$file_id, $user_id]);
    $file = $stmt->fetch();

    if ($file) {
        // تحقق إذا كان هناك رابط مشاركة موجود مسبقًا
        $stmt = $pdo->prepare("SELECT * FROM file_shares WHERE file_id = ?");
        $stmt->execute([$file_id]);
        $share = $stmt->fetch();

        if (!$share) {
            // إنشاء رابط مشاركة
            $token = generateToken();
            $usage_limit = 5; // يمكن تغييره حسب الحاجة

            $stmt = $pdo->prepare("INSERT INTO file_shares (file_id, token, usage_limit) VALUES (?, ?, ?)");
            $stmt->execute([$file_id, $token, $usage_limit]);

            $message = "✅ تم إنشاء رابط المشاركة بنجاح: <a class='text-blue-600 underline' href='download.php?token=$token'>تحميل الملف</a>";
            $link_created = true;
        } else {
            $token = $share['token'];
            $message = "⚠️ تم إنشاء الرابط مسبقًا: <a class='text-blue-600 underline' href='download.php?token=$token'>تحميل الملف</a>";
        }
    } else {
        $message = "❌ الملف غير موجود أو لا يخصك.";
    }
}

// التعامل مع الصفحة بدون file_id (عرض الملفات بدون روابط)
else {
    // جلب الملفات التي لا تملك رابط مشاركة
    $stmt = $pdo->prepare("
        SELECT f.* FROM files f
        LEFT JOIN file_shares fs ON f.id = fs.file_id
        WHERE f.user_id = ? AND fs.id IS NULL
    ");
    $stmt->execute([$user_id]);
    $available_files = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إنشاء رابط مشاركة</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">

    <div class="container mx-auto mt-10 px-4">
        <h2 class="text-xl font-bold mb-6">🔗 إنشاء رابط مشاركة لملف</h2>

        <?php if ($message): ?>
            <div class="bg-white border rounded p-4 shadow mb-6">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <?php if (!$link_created && empty($_GET['file_id'])): ?>
            <div class="bg-white border rounded p-4 shadow">
                <h3 class="font-semibold mb-4">اختر ملفًا لإنشاء رابط مشاركة:</h3>
                <?php if (count($available_files) > 0): ?>
                    <ul class="space-y-2">
                        <?php foreach ($available_files as $file): ?>
                            <li class="flex justify-between items-center border px-4 py-2 rounded">
                                <span><?= htmlspecialchars($file['filename']) ?></span>
                                <a class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700" href="link.php?file_id=<?= $file['id'] ?>">إنشاء رابط</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-gray-600">لا توجد ملفات متاحة لإنشاء روابط لها.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
*/?>
