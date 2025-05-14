<?php
/*
session_start();
require 'includes/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = 'الرجاء تعبئة جميع الحقول.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->fetch()) {
            $error = 'اسم المستخدم موجود بالفعل.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->execute([$username, $hashed_password]);
            header('Location: login.php');
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>تسجيل حساب</title></head>
<body>
<?php require 'includes/navbar.php';?>

    <h2>تسجيل حساب جديد</h2>
    <?php if ($error): ?><p style="color:red;"><?= $error ?></p><?php endif; ?>
    <form method="POST">
        <label>اسم المستخدم: <input type="text" name="username"></label><br>
        <label>كلمة المرور: <input type="password" name="password"></label><br>
        <button type="submit">تسجيل</button>
    </form>
</body>
</html>
*/?>
<?php
session_start();
require 'includes/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = '⚠️ الرجاء تعبئة جميع الحقول.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->fetch()) {
            $error = '❌ اسم المستخدم موجود بالفعل.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->execute([$username, $hashed_password]);
            header('Location: login.php');
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تسجيل حساب جديد</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg">

<?php require 'includes/navbar.php'; ?>

<main class="form-container">
    <h2>📝 تسجيل حساب جديد</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="form">
        <label>اسم المستخدم</label>
        <input type="text" name="username" required>

        <label>كلمة المرور</label>
        <input type="password" name="password" required>

        <button type="submit" class="btn btn-primary">تسجيل</button>
    </form>

    <p class="form-note">لديك حساب بالفعل؟ <a href="login.php" class="link">سجّل الدخول</a></p>
</main>

</body>
</html>
