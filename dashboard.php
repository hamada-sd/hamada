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

$stmt = $pdo->prepare("SELECT username, created_at FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$stmt_files = $pdo->prepare("SELECT COUNT(*) FROM files WHERE user_id = ?");
$stmt_files->execute([$_SESSION['user_id']]);
$file_count = $stmt_files->fetchColumn();

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['current_password'], $_POST['new_password'], $_POST['confirm_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = 'يرجى تعبئة جميع الحقول.';
    } elseif ($new_password !== $confirm_password) {
        $error = 'كلمة المرور الجديدة لا تطابق التأكيد.';
    } else {
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user_data = $stmt->fetch();

        if (!password_verify($current_password, $user_data['password'])) {
            $error = 'كلمة المرور الحالية غير صحيحة.';
        } else {
            $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed_new_password, $_SESSION['user_id']]);
            $success = 'تم تغيير كلمة المرور بنجاح!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>لوحة التحكم</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <?php require 'includes/navbar.php'; ?>

  <main class="container">
    <section class="card">
      <h2>لوحة التحكم</h2>
      <p><strong>مرحبًا، <?= htmlspecialchars($user['username']) ?>!</strong></p>
      <p>تاريخ التسجيل: <?= $user['created_at'] ?></p>
      <p>عدد الملفات التي قمت برفعها: <?= $file_count ?></p>

      <hr />

      <h3>تغيير كلمة المرور</h3>

      <?php if ($error): ?>
        <div class="alert error"><?= $error ?></div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="alert success"><?= $success ?></div>
      <?php endif; ?>

      <form method="POST" class="form">
        <label for="current_password">كلمة المرور الحالية</label>
        <input type="password" name="current_password" id="current_password" required />

        <label for="new_password">كلمة المرور الجديدة</label>
        <input type="password" name="new_password" id="new_password" required />

        <label for="confirm_password">تأكيد كلمة المرور الجديدة</label>
        <input type="password" name="confirm_password" id="confirm_password" required />

        <button type="submit" class="btn btn-primary">تغيير كلمة المرور</button>
      </form>

      <hr />

      <a href="logout.php" class="btn btn-danger">تسجيل الخروج</a>
    </section>
  </main>

  <footer>
    <p>&copy; 2025 جميع الحقوق محفوظة</p>
  </footer>
</body>
</html>
