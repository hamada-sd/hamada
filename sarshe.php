<?php
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

// 1. إنشاء الرابط فعليًا بعد ضبط الإعدادات
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['file_id'])) {
    $file_id = (int)$_POST['file_id'];
    $usage_limit = (int)$_POST['usage_limit'];
    $expiry_days = (int)$_POST['expiry_days'];

    // تأكد من ملكية الملف
    $stmt = $pdo->prepare("SELECT * FROM files WHERE id = ? AND user_id = ?");
    $stmt->execute([$file_id, $user_id]);
    $file = $stmt->fetch();

    if ($file) {
        // تحقق من وجود رابط سابق
        $stmt = $pdo->prepare("SELECT * FROM file_shares WHERE file_id = ?");
        $stmt->execute([$file_id]);
        $existing = $stmt->fetch();

        if (!$existing) {
            $token = generateToken();
            $expires_at = date('Y-m-d H:i:s', strtotime("+$expiry_days days"));

            $stmt = $pdo->prepare("INSERT INTO file_shares (file_id, token, usage_limit, expires_at) VALUES (?, ?, ?, ?)");
            $stmt->execute([$file_id, $token, $usage_limit, $expires_at]);

            $message = "✅ تم إنشاء رابط المشاركة بنجاح: <a class='text-blue-600 underline' href='download.php?token=$token'>تحميل الملف</a><br>ينتهي في: $expires_at";
            $link_created = true;
        } else {
            $message = "⚠️ تم إنشاء رابط مسبقًا لهذا الملف.";
        }
    } else {
        $message = "❌ الملف غير موجود أو لا يخصك.";
    }
}

// 2. عرض نموذج الإعدادات عند وجود file_id في GET
elseif (isset($_GET['file_id'])) {
    $file_id = (int)$_GET['file_id'];

    $stmt = $pdo->prepare("SELECT * FROM files WHERE id = ? AND user_id = ?");
    $stmt->execute([$file_id, $user_id]);
    $file_to_configure = $stmt->fetch();

    if (!$file_to_configure) {
        $message = "❌ الملف غير موجود أو لا يخصك.";
    }
}

// 3. عرض قائمة الملفات بدون روابط
else {
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
        <h2 class="text-xl font-bold mb-6">🔗 إعداد رابط مشاركة</h2>

        <?php if ($message): ?>
            <div class="bg-white border rounded p-4 shadow mb-6">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <?php if (!$link_created && isset($file_to_configure)): ?>
            <!-- نموذج إعدادات الرابط -->
            <div class="bg-white border rounded p-4 shadow mb-6">
                <h3 class="font-semibold mb-4">إنشاء رابط لملف: <?= htmlspecialchars($file_to_configure['filename']) ?></h3>
                <form method="POST">
                    <input type="hidden" name="file_id" value="<?= $file_to_configure['id'] ?>">
                    
                    <div class="mb-4">
                        <label class="block mb-1">عدد مرات التحميل المسموح بها:</label>
                        <input type="number" name="usage_limit" value="5" min="1" class="border rounded w-full px-3 py-2">
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">مدة صلاحية الرابط (بالأيام):</label>
                        <input type="number" name="expiry_days" value="7" min="1" class="border rounded w-full px-3 py-2">
                    </div>

                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">إنشاء الرابط</button>
                </form>
            </div>

        <?php elseif (!$link_created): ?>
            <!-- عرض قائمة الملفات لإنشاء روابط -->
            <div class="bg-white border rounded p-4 shadow">
                <h3 class="font-semibold mb-4">اختر ملفًا لإنشاء رابط له:</h3>
                <?php if (!empty($available_files)): ?>
                    <ul class="space-y-2">
                        <?php foreach ($available_files as $file): ?>
                            <li class="flex justify-between items-center border px-4 py-2 rounded">
                                <span><?= htmlspecialchars($file['filename']) ?></span>
                                <a class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700" href="link.php?file_id=<?= $file['id'] ?>">إعدادات الرابط</a>
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


<?php
/*
session_start();
require 'includes/auth.php';
require 'includes/db.php';

$user_id = $_SESSION['user_id'];
$folder_id = isset($_GET['folder_id']) ? (int)$_GET['folder_id'] : null;

// استعلام القاعدة الأساسي
$sql = "SELECT * FROM files WHERE user_id = ?";
$params = [$user_id];

// فلترة حسب المجلد
if ($folder_id) {
    $sql .= " AND folder_id = ?";
    $params[] = $folder_id;
}

// فلترة البحث
$search_name = $_GET['name'] ?? '';
$search_ext = $_GET['ext'] ?? '';
$search_date = $_GET['date'] ?? '';

if (!empty($search_name)) {
    $sql .= " AND filename LIKE ?";
    $params[] = "%$search_name%";
}
if (!empty($search_ext)) {
    $sql .= " AND filename LIKE ?";
    $params[] = "%.$search_ext";
}
if (!empty($search_date)) {
    $sql .= " AND DATE(uploaded_at) = ?";
    $params[] = $search_date;
}

$sql .= " ORDER BY uploaded_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$files = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>ملفاتي</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php require 'includes/navbar.php';?>

<div class="container mt-5">
    <h3 class="mb-4 text-center">📁 ملفاتي</h3>

    <form method="GET" class="row g-3 mb-4">
        <?php if ($folder_id): ?>
            <input type="hidden" name="folder_id" value="<?= $folder_id ?>">
        <?php endif; ?>
        <div class="col-md-4">
            <input type="text" name="name" value="<?= htmlspecialchars($search_name) ?>" class="form-control" placeholder="🔍 البحث بالاسم">
        </div>
        <div class="col-md-3">
            <input type="text" name="ext" value="<?= htmlspecialchars($search_ext) ?>" class="form-control" placeholder=".pdf, .jpg, zip">
        </div>
        <div class="col-md-3">
            <input type="date" name="date" value="<?= htmlspecialchars($search_date) ?>" class="form-control">
        </div>
        <div class="col-md-2 d-grid">
            <button class="btn btn-primary" type="submit">بحث</button>
        </div>
    </form>

    <?php if (count($files) > 0): ?>
        <table class="table table-bordered table-hover text-center">
            <thead class="table-dark">
                <tr>
                    <th>اسم الملف</th>
                    <th>المسار</th>
                    <th>تاريخ الرفع</th>
                    <th>تحميل</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($files as $file): ?>
                    <tr>
                        <td><?= htmlspecialchars($file['filename']) ?></td>
                        <td><?= htmlspecialchars($file['filepath']) ?></td>
                        <td><?= $file['uploaded_at'] ?></td>
                        <td>
                            <a href="download.php?token=<?= generate_token_for_file($file['id']) ?>" class="btn btn-sm btn-success">⬇ تحميل</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning text-center">لا توجد ملفات مطابقة لبحثك.</div>
    <?php endif; ?>
</div>
</body>
</html>

<?php
// دالة مؤقتة لتوليد التوكن - يمكنك تحسينها لاحقًا
function generate_token_for_file($file_id) {
    return 'manual_token_handler.php?file_id=' . $file_id;
}*/
?>
