<?php
/*
session_start();
require 'includes/auth.php';
require 'includes/db.php';

if (!isset($_GET['id'])) {
    die("رقم الملف غير موجود.");
}

$file_id = (int)$_GET['id'];

// التأكد من أن المستخدم يملك الملف
$stmt = $pdo->prepare("SELECT f.*, 
                      (SELECT COUNT(*) FROM file_shares WHERE file_id = f.id) as downloads
                      FROM files f 
                      WHERE f.id = ? AND f.user_id = ?");
$stmt->execute([$file_id, $_SESSION['user_id']]);
$file = $stmt->fetch();

if (!$file) {
    die("الملف غير موجود أو لا تملكه.");
}

// حساب الحجم بصيغة قابلة للقراءة
function readableSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $i = 0;
    while ($bytes > 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 2) . ' ' . $units[$i];
}


<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تفاصيل الملف</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php require 'includes/navbar.php'; ?>

<div class="container mt-5">
    <h3>🧾 تفاصيل الملف</h3>
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($file['filename']) ?></h5>
            <p class="card-text">
                <strong>الحجم:</strong> <?= readableSize(filesize($file['filepath'])) ?><br>
                <strong>النوع:</strong> <?= pathinfo($file['filename'], PATHINFO_EXTENSION) ?><br>
                <strong>تم تحميله:</strong> <?= $file['downloads'] ?> مرة<br>
                <strong>رابط مباشر:</strong> <a href="download.php?token=<?= generate_token_for_file($file['id']) ?>" target="_blank">تحميل</a>
            </p>

            <?php
            $ext = strtolower(pathinfo($file['filename'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                echo "<hr><h6>📷 معاينة الصورة:</h6><img src='{$file['filepath']}' class='img-fluid' style='max-height:400px'>";
            } elseif ($ext === 'pdf') {
                echo "<hr><h6>📄 معاينة PDF:</h6>
                      <iframe src='{$file['filepath']}' width='100%' height='500px'></iframe>";
            } else {
                echo "<hr><em>🛈 لا يمكن عرض هذا النوع من الملفات داخل الموقع.</em>";
            }

            function generate_token_for_file($id) {
                // عشان التجريب - في الإنتاج استخدم رمز حقيقي مخزن بقاعدة البيانات
                return 'token_placeholder_for_' . $id;
            }
            ?>
        </div>
    </div>
</div>

</body>
</html>
*/?>
<?php
session_start();
require 'includes/auth.php';
require 'includes/db.php';

if (!isset($_GET['id'])) {
    die("رقم الملف غير موجود.");
}

$file_id = (int)$_GET['id'];

// التأكد من أن المستخدم يملك الملف
$stmt = $pdo->prepare("SELECT f.*, 
                      (SELECT COUNT(*) FROM file_shares WHERE file_id = f.id) as downloads
                      FROM files f 
                      WHERE f.id = ? AND f.user_id = ?");
$stmt->execute([$file_id, $_SESSION['user_id']]);
$file = $stmt->fetch();

if (!$file) {
    die("الملف غير موجود أو لا تملكه.");
}

// حجم الملف بصيغة قابلة للقراءة
function readableSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $i = 0;
    while ($bytes > 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 2) . ' ' . $units[$i];
}

function generate_token_for_file($id) {
    return 'token_placeholder_for_' . $id;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>تفاصيل الملف</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php require 'includes/navbar.php'; ?>

<main class="container">
  <section class="card">
    <h2>🧾 تفاصيل الملف</h2>
    <p><strong>الاسم:</strong> <?= htmlspecialchars($file['filename']) ?></p>
    <p><strong>الحجم:</strong> <?= readableSize(filesize($file['filepath'])) ?></p>
    <p><strong>النوع:</strong> <?= pathinfo($file['filename'], PATHINFO_EXTENSION) ?></p>
    <p><strong>عدد مرات التحميل:</strong> <?= $file['downloads'] ?> مرة</p>
    <p><strong>رابط مباشر:</strong> <a href="download.php?token=<?= generate_token_for_file($file['id']) ?>" target="_blank" class="btn btn-primary">تحميل</a></p>

    <?php
      $ext = strtolower(pathinfo($file['filename'], PATHINFO_EXTENSION));
      if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
          echo "<hr><h3>📷 معاينة الصورة:</h3>
                <img src='{$file['filepath']}' class='responsive-img' style='max-height: 400px; border-radius: 8px;'>";
      } elseif ($ext === 'pdf') {
          echo "<hr><h3>📄 معاينة PDF:</h3>
                <iframe src='{$file['filepath']}' width='100%' height='500px' style='border: 1px solid #ccc; border-radius: 8px;'></iframe>";
      } else {
          echo "<hr><em>🛈 لا يمكن عرض هذا النوع من الملفات داخل الموقع.</em>";
      }
    ?>
  </section>
</main>

<footer>
  <p>&copy; 2025 جميع الحقوق محفوظة</p>
</footer>

</body>
</html>
