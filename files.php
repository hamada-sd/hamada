<?php

session_start();
require 'includes/auth.php';
require 'includes/db.php';

$stmt = $pdo->prepare("SELECT * FROM files WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$files = $stmt->fetchAll();
$stmt = $pdo->prepare("SELECT * FROM folders WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$folders = $stmt->fetchAll();
/*?>
<!DOCTYPE html>
<html>
<head><title>ملفاتي</title></head>
<body>
<?php require 'includes/navbar.php';?>
    <h2>قائمة الملفات</h2>
    <a href="upload.php">رفع ملف جديد</a> | <a href="logout.php">تسجيل الخروج</a>
    <ul>
        <?php foreach ($files as $file): ?>
            <li>
                <?= htmlspecialchars($file['filename']) ?>
                - <a href="download.php?file=<?= urlencode($file['filepath']) ?>">تحميل</a>
            </li>
        <?php endforeach; ?>
        <script>
            function fetchFiles(folderId) {
  fetch('get_files.php?folder_id=' + folderId)
    .then(response => response.json())
    .then(data => {
      const fileContainer = document.getElementById('file-container');
      if (data.length > 0) {
        let html = '<div class="file-grid">';
        data.forEach(file => {
          const ext = file.filename.split('.').pop().toLowerCase();
          const isImage = ['jpg', 'jpeg', 'png', 'gif'].includes(ext);
          const thumbnail = isImage ? `<img src="${file.filepath}" alt="${file.filename}" class="thumb">` : `<div class="file-icon">📄</div>`;

          html += `
            <div class="file-card">
              ${thumbnail}
              <div class="file-details">
                <span class="filename">${file.filename}</span>
                ${
                  file.token !== "غير قابل للتنزيل"
                    ? `<a class="btn btn-download" href="download.php?token=${file.token}">تحميل</a>
                       <div class="note">مرات التحميل المتبقية: ${file.usage_limit ?? 'غير محدد'}</div>`
                    : `<a class="btn btn-warning" href="link.php?file_id=${file.id}">إنشاء رابط</a>`
                }
              </div>
            </div>
          `;
        });
        html += '</div>';
        fileContainer.innerHTML = html;
      } else {
        fileContainer.innerHTML = '<p class="info">لا توجد ملفات في هذا المجلد.</p>';
      }
    });
}

        </script>

    </ul>
</body>
</html>

*/ ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة الملفات</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg">
  <?php require 'includes/navbar.php'; ?>

    <main class="container">
        <div class="flex-wrap">
            <!-- قائمة المجلدات -->
            <aside class="sidebar">
                <h3>📂 المجلدات</h3>
                <ul class="folder-list">
                    <?php foreach ($folders as $folder): ?>
                        <li>
                            <a href="#" class="folder-link" data-folder-id="<?= $folder['id'] ?>">
                                <?= htmlspecialchars($folder['folder_name']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </aside>

            <!-- قائمة الملفات -->
            <section class="content">
                <h3>📁 الملفات</h3>
                <div id="file-container">
                    <p class="info">اختر مجلدًا لعرض الملفات.</p>
                </div>
            </section>
        </div>
    </main>

    <script>
        document.querySelectorAll('.folder-link').forEach(item => {
            item.addEventListener('click', function (e) {
                e.preventDefault();
                const folderId = this.getAttribute('data-folder-id');
                fetchFiles(folderId);
            });
        });

        function fetchFiles(folderId) {
            fetch('get_files.php?folder_id=' + folderId)
                .then(response => response.json())
                .then(data => {
                    const fileContainer = document.getElementById('file-container');
                    if (data.length > 0) {
                        let html = '<div class="file-grid">';
                        data.forEach(file => {
                            const ext = file.filename.split('.').pop().toLowerCase();
                            const isImage = ['jpg', 'jpeg', 'png', 'gif'].includes(ext);
                            const thumbnail = isImage
                                ? `<img src="${file.filepath}" alt="${file.filename}" class="thumb">`
                                : `<div class="file-icon">📄</div>`;

                            html += `
                                <div class="file-card">
                                    ${thumbnail}
                                    <div class="file-details">
                                        <span class="filename">${file.filename}</span>
                                        ${
                                            file.token !== "غير قابل للتنزيل"
                                                ? `<a class="btn btn-download" href="download.php?token=${file.token}">تحميل</a>
                                                   <div class="note">مرات التحميل المتبقية: ${file.usage_limit ?? 'غير محدد'}</div>`
                                                : `<a class="btn btn-warning" href="shared.php?file_id=${file.id}">إنشاء رابط</a>`
                                        }
                                    </div>
                                </div>
                            `;
                        });
                        html += '</div>';
                        fileContainer.innerHTML = html;
                    } else {
                        fileContainer.innerHTML = '<p class="info">لا توجد ملفات في هذا المجلد.</p>';
                    }
                });
        }
    </script>

</body>
</html>
