<?php
/*
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>تسجيل الخروج</title>
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
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }

    .logout-box {
      background-color: white;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      text-align: center;
    }

    h1 {
      color: var(--main-color);
      margin-bottom: 20px;
    }

    a.btn {
      display: inline-block;
      background-color: var(--main-color);
      color: white;
      padding: 10px 25px;
      border-radius: 5px;
      text-decoration: none;
      font-weight: bold;
      transition: background 0.3s;
    }

    a.btn:hover {
      background-color: #4cb9ba;
    }
  </style>
</head>
<body>
  <div class="logout-box">
    <h1>تم تسجيل الخروج بنجاح</h1>
    <a href="login.php" class="btn">العودة إلى صفحة الدخول</a>
  </div>
</body>
</html>*/?>
<?php
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>تسجيل الخروج</title>
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
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .logout-box {
      background-color: white;
      padding: 30px 20px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      text-align: center;
      width: 100%;
      max-width: 400px;
    }

    h1 {
      color: var(--main-color);
      margin-bottom: 20px;
      font-size: 1.5rem;
    }

    a.btn {
      display: inline-block;
      background-color: var(--main-color);
      color: white;
      padding: 12px 20px;
      border-radius: 5px;
      text-decoration: none;
      font-weight: bold;
      font-size: 1rem;
      transition: background 0.3s;
    }

    a.btn:hover {
      background-color: #4cb9ba;
    }

    @media (max-width: 480px) {
      h1 {
        font-size: 1.2rem;
      }

      a.btn {
        width: 100%;
        padding: 12px;
        font-size: 1rem;
      }
    }
  </style>
</head>
<body>
  <div class="logout-box">
    <h1>تم تسجيل الخروج بنجاح</h1>
    <a href="login.php" class="btn">العودة إلى صفحة الدخول</a>
  </div>
</body>
</html>

