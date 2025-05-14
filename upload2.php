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

// جلب المجلدات الخاصة بالمستخدم
$stmt = $pdo->prepare("SELECT * FROM folders WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$folders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>رفع ملفات</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css">
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
            padding: 40px 0;
        }

        h3 {
            color: var(--main-color);
            margin-bottom: 20px;
            text-align: center;
            font-size: 1.8rem;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        select {
            padding: 10px;
            width: 100%;
            border: 1px solid var(--light-gray);
            border-radius: 5px;
            margin-bottom: 30px;
        }

        .dropzone {
            border: 2px dashed var(--main-color);
            background: #f9f9f9;
            padding: 30px;
            text-align: center;
            border-radius: 5px;
            color: var(--main-color);
            transition: background-color 0.3s, border-color 0.3s;
        }

        .dropzone:hover {
            background-color: #e0f7f7;
            border-color: #4fa3a3;
        }

        footer {
            background-color: var(--dark-color);
            color: white;
            text-align: center;
            padding: 15px 0;
            margin-top: 40px;
        }

        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 20px 0;
            }

            h3 {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            h3 {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>

<?php require 'includes/navbar.php'; ?>

<div class="container">
    <h3>رفع ملفاتك باستخدام السحب والإفلات</h3>

    <form method="POST" id="folderForm">
        <label for="folder_id">اختر المجلد:</label>
        <select id="folder_id" name="folder_id" required>
            <?php foreach ($folders as $folder): ?>
                <option value="<?= $folder['id'] ?>"><?= htmlspecialchars($folder['folder_name']) ?></option>
            <?php endforeach; ?>
        </select>
    </form>

    <form action="upload_handler.php" class="dropzone" id="fileDropzone">
        <div>اسحب الملفات هنا أو انقر لتحديدها</div>
    </form>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
<script>
    Dropzone.autoDiscover = false;

    const folderId = document.getElementById('folder_id');
    const myDropzone = new Dropzone("#fileDropzone", {
        url: "upload_handler.php",
        paramName: "file",
        maxFilesize: 100, // 100MB
        acceptedFiles: ".jpg,.jpeg,.png,.gif,.bmp,.webp,.tiff,.svg,.mp3,.wav,.ogg,.m4a,.flac,.aac,.wma,.mp4,.mov,.avi,.mkv,.webm,.wmv,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.pdf,.txt,.zip,.rar,.7z",
        addRemoveLinks: true,
        init: function () {
            this.on("sending", function (file, xhr, formData) {
                formData.append("folder_id", folderId.value);
            });
            this.on("success", function (file, response) {
                console.log("تم الرفع:", response);
            });
            this.on("error", function (file, errorMessage, xhr) {
                console.error("حدث خطأ:", errorMessage);
            });
        }
    });

    folderId.addEventListener('change', function () {
        myDropzone.removeAllFiles();
    });
</script>

<footer>
    <p>&copy; 2025 جميع الحقوق محفوظة</p>
</footer>

</body>
</html>

<?php
/*
session_start();
require 'includes/auth.php';
require 'includes/db.php';

// جلب المجلدات الخاصة بالمستخدم
$stmt = $pdo->prepare("SELECT * FROM folders WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$folders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>رفع ملفات</title>
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

        h3 {
            color: var(--main-color);
            margin-bottom: 20px;
            text-align: center;
        }

        .form-label {
            font-weight: bold;
        }

        .form-select {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid var(--light-gray);
            width: 100%;
            margin-bottom: 20px;
        }

        .dropzone {
            border: 2px dashed var(--main-color);
            border-radius: 5px;
            background: #f9f9f9;
            padding: 30px;
            text-align: center;
        }

        .dropzone span {
            font-size: 1.2em;
            color: var(--main-color);
        }

        .dropzone.dz-clickable {
            cursor: pointer;
        }

        .dropzone.dz-hovered {
            border-color: #4fa3a3;
            background-color: #e0f7f7;
        }

        .alert {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .btn {
            background-color: var(--main-color);
            color: white;
            border-radius: 5px;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn:hover {
            background-color: #4fa3a3;
        }

        /* ===== Responsive Styles ===== * /
        @media (max-width: 768px) {
            .container {
                padding: 15px;
                width: 95%;
            }

            .dropzone {
                padding: 20px;
            }

            .form-label {
                font-size: 1rem;
            }

            .form-select {
                font-size: 1rem;
            }

            h3 {
                font-size: 1.5rem;
                margin-bottom: 15px;
            }

            .btn {
                width: 100%;
                padding: 12px;
            }

            footer {
                padding: 10px 0;
            }
        }

        @media (max-width: 480px) {
            h3 {
                font-size: 1.3rem;
            }

            .dropzone {
                font-size: 1rem;
                padding: 15px;
            }

            .form-select {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
<?php require 'includes/navbar.php'; ?>

<div class="container mt-5">
    <h3 class="mb-4">رفع ملفاتك باستخدام السحب والإفلات</h3>

    <form method="POST" class="mb-4" id="folderForm">
        <label for="folder_id" class="form-label">اختر المجلد:</label>
        <select id="folder_id" name="folder_id" class="form-select" required>
            <?php foreach ($folders as $folder): ?>
                <option value="<?= $folder['id'] ?>"><?= htmlspecialchars($folder['folder_name']) ?></option>
            <?php endforeach; ?>
        </select>
    </form>

    <form action="upload_handler.php" class="dropzone" id="fileDropzone">
        <span>اسحب الملفات هنا لرفعها</span>
    </form>

    <div class="alert" style="display: none;" id="uploadSuccessAlert">
        تم رفع الملف بنجاح!
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
<script>
    Dropzone.autoDiscover = false;

    const folderId = document.getElementById('folder_id');
    const myDropzone = new Dropzone("#fileDropzone", {
        url: "upload_handler.php",
        paramName: "file",
        maxFilesize: 10000, // 100MB limit per file
        //acceptedFiles: ".jpg,.png,.pdf,.docx,.zip,.mp4,.mp3,.m4a", // Specify accepted file types
        acceptedFiles: ".jpg,.jpeg,.png,.gif,.bmp,.webp,.tiff,.svg,.mp3,.wav,.ogg,.m4a,.flac,.aac,.wma,.mp4,.mov,.avi,.mkv,.webm,.wmv,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.pdf,.txt,.zip,.rar,.7z"
        addRemoveLinks: true,
        init: function () {
            this.on("sending", function (file, xhr, formData) {
                formData.append("folder_id", folderId.value);
            });
            this.on("success", function (file, response) {
                console.log("تم الرفع:", response);
                document.getElementById('uploadSuccessAlert').style.display = 'block';
            });
            this.on("error", function (file, errorMessage, xhr) {
                console.error("حدث خطأ:", errorMessage);
            });
        }
    });

    folderId.addEventListener('change', function () {
        myDropzone.removeAllFiles();
    });
</script>

<footer>
    <p>&copy; 2025 جميع الحقوق محفوظة</p>
</footer>

</body>
</html>
<?php /* */?>