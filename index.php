<?php
/*
session_start();
?>
<!DOCTYPE html>
<html>
<head><title>الصفحة الرئيسية</title></head>
<body>
<?php require 'includes/navbar.php';?>

    <h1>مرحبا بك في موقع مشاركة الملفات</h1>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="files.php">ملفاتي</a> | <a href="logout.php">تسجيل الخروج</a>
    <?php else: ?>
        <a href="login.php">تسجيل الدخول</a> | <a href="register.php">تسجيل حساب</a>
    <?php endif; ?>
</body>
</html>
*/ ?>
<?php
session_start();
if (isset($_SESSION['user_id'])) {
    //header("Location: files.php");
    //exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>مشاركة الملفات</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg">

<?php require 'includes/navbar.php'; ?>

<main class="landing">
    <section class="hero">
        <h1>👋 مرحبًا بك في منصّة مشاركة الملفات</h1>
        <p>قم برفع ملفاتك، تنظيمها في مجلدات، ومشاركتها عبر روابط مباشرة بأمان وسهولة.</p>

        <div class="actions">
            <a href="register.php" class="btn btn-primary">إنشاء حساب جديد</a>
            <a href="login.php" class="btn btn-secondary">تسجيل الدخول</a>
        </div>
    </section>

    <section class="features">
        <h2>🚀 ماذا يمكنك أن تفعل؟</h2>
        <ul>
            <li>📁 إنشاء مجلدات لتنظيم ملفاتك.</li>
            <li>🔗 مشاركة روابط تحميل خاصة وآمنة.</li>
            <li>📊 متابعة عدد مرات تحميل كل ملف.</li>
            <li>🖼️ عرض الصور ومعاينة ملفات PDF.</li>
        </ul>
    </section>
</main>

<footer>
    <p>&copy; 2025 منصة مشاركة الملفات</p>
</footer>

</body>
</html>
