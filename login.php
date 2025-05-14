<?php
/*
session_start();
require 'includes/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: files.php');
        exit();
    } else {
        $error = 'اسم المستخدم أو كلمة المرور غير صحيحة.';
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>تسجيل الدخول</title></head>
<body>
<?php require 'includes/navbar.php';?>

    <h2>تسجيل الدخول</h2>
    <?php if ($error): ?><p style="color:red;"><?= $error ?></p><?php endif; ?>
    <form method="POST">
        <label>اسم المستخدم: <input type="text" name="username"></label><br>
        <label>كلمة المرور: <input type="password" name="password"></label><br>
        <button type="submit">دخول</button>
    </form>
</body>
</html>*/
?>
<?php
session_start();
require 'includes/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: files.php');
        exit();
    } else {
        $error = '❌ اسم المستخدم أو كلمة المرور غير صحيحة.';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg">

<?php require 'includes/navbar.php'; ?>

<main class="form-container">
    <h2>🔐 تسجيل الدخول</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="form">
        <label>اسم المستخدم</label>
        <input type="text" name="username" required>

        <label>كلمة المرور</label>
        <input type="password" name="password" required>

        <button type="submit" class="btn btn-primary">دخول</button>
    </form>

    <p class="form-note">ليس لديك حساب؟ <a href="register.php" class="link">أنشئ حسابًا الآن</a></p>
</main>

</body>
</html>
