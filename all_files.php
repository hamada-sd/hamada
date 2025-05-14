<?php
session_start();
require 'includes/auth.php';
require 'includes/db.php';

$user_id = $_SESSION['user_id'];

// جلب المجلدات الخاصة بالمستخدم
$stmt = $pdo->prepare("SELECT * FROM folders WHERE user_id = ?");
$stmt->execute([$user_id]);
$folders = $stmt->fetchAll();

// جلب الملفات الخاصة بالمستخدم
$stmt = $pdo->prepare("SELECT * FROM files WHERE user_id = ?");
$stmt->execute([$user_id]);
$files = $stmt->fetchAll();

// تنظيم الملفات حسب المجلدات
$files_by_folder = [];
foreach ($files as $file) {
    $files_by_folder[$file['folder_id']][] = $file;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>إدارة المجلدات والملفات</title>
  <link rel="stylesheet" href="style.css">
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
  max-width: 1200px;
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

.content {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

@media (min-width: 768px) {
  .content {
    flex-direction: row;
  }
}

.sidebar,
.main-panel {
  background-color: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.sidebar {
  flex: 1;
  max-height: 500px;
  overflow-y: auto;
}

.main-panel {
  flex: 3;
}

h1, h2 {
  margin-bottom: 15px;
  color: var(--main-color);
}

.folder-list,
.file-list {
  list-style: none;
  padding-right: 0;
}

.folder-link {
  display: block;
  padding: 10px 12px;
  background-color: #f9f9f9;
  color: var(--dark-color);
  border-radius: 5px;
  text-decoration: none;
  transition: background 0.3s;
}

.folder-link:hover {
  background-color: var(--main-color);
  color: white;
}

.file-item {
  padding: 12px;
  border: 1px solid var(--light-gray);
  border-radius: 5px;
  margin-bottom: 10px;
  background-color: #fafafa;
}

.file-info {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

@media (min-width: 600px) {
  .file-info {
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
  }
}

.btn-download {
  color: var(--main-color);
  text-decoration: none;
  font-weight: bold;
}

.btn-download:hover {
  text-decoration: underline;
}

.btn-warning {
  color: red;
  font-weight: bold;
  text-decoration: none;
}

.btn-warning:hover {
  text-decoration: underline;
}

.info {
  color: #666;
}

.note {
  font-size: 0.9em;
  color: #555;
}
.file-info span {
  max-width: 200px;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
  display: inline-block;
  direction: ltr;
}

  </style>
</head>
<body>
  <?php require 'includes/navbar.php'; ?>
  <header>
    <div class="container">
      <h1>نظام إدارة الملفات</h1>
    </div>
  </header>

  <main class="container">
    <div class="content">
      <!-- قائمة المجلدات -->
      <section class="sidebar">
        <h2>📂 المجلدات</h2>
        <ul class="folder-list">
          <?php foreach ($folders as $folder): ?>
            <li>
              <a href="#" class="folder-link" data-folder-id="<?= $folder['id'] ?>">
                <?= htmlspecialchars($folder['folder_name']) ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </section>

      <!-- قائمة الملفات -->
      <section class="main-panel">
        <h2>📁 الملفات</h2>
        <div id="file-container">
          <p class="info">اختر مجلدًا لعرض الملفات.</p>
        </div>
      </section>
    </div>
  </main>

  <footer>
    <p>&copy; 2025 جميع الحقوق محفوظة</p>
  </footer>

  <script>
    /*
    document.querySelectorAll('.folder-link').forEach(item => {
      item.addEventListener('click', function(e) {
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
            let html = '<ul class="file-list">';
            data.forEach(file => {
              html += `
                <li class="file-item">
                  <div class="file-info">
                    <span>${file.filename}</span>
                    ${
                      file.token !== "غير قابل للتنزيل"
                        ? `<div>
                            <a class="btn-download" href="download.php?token=${file.token}">تحميل</a>
                            <span class="note">مرات التحميل المتبقية: ${file.usage_limit ?? 'غير محدد'}</span>
                          </div>`
                        : `<a class="btn-warning" href="shared.php?file_id=${file.id}">إنشاء رابط جديد</a>`
                    }
                  </div>
                </li>`;
            });
            html += '</ul>';
            fileContainer.innerHTML = html;
          } else {
            fileContainer.innerHTML = '<p class="info">لا توجد ملفات في هذا المجلد.</p>';
          }
        });
    } /* */
  </script>
  <script>
  document.querySelectorAll('.folder-link').forEach(item => {
    item.addEventListener('click', function(e) {
      e.preventDefault();
      const folderId = this.getAttribute('data-folder-id');
      fetchFiles(folderId);
    });
  });

  function shortenFilename(filename, maxLength = 20) {
    const dotIndex = filename.lastIndexOf(".");
    if (dotIndex === -1 || filename.length <= maxLength) return filename;

    const name = filename.substring(0, dotIndex);
    const ext = filename.substring(dotIndex);

    const visibleChars = maxLength - ext.length - 3; // 3 for "..."
    if (visibleChars <= 0) return "..." + ext;
    
    return name.substring(0, visibleChars) + "... " + ext;
  }

  function fetchFiles(folderId) {
    fetch('get_files.php?folder_id=' + folderId)
      .then(response => response.json())
      .then(data => {
        const fileContainer = document.getElementById('file-container');
        if (data.length > 0) {
          let html = '<ul class="file-list">';
          data.forEach(file => {
            html += `
              <li class="file-item">
                <div class="file-info">
                  <span title="${file.filename}">${shortenFilename(file.filename)}</span>
                  ${
                    file.token !== "غير قابل للتنزيل"
                      ? `<div>
                          <a class="btn-download" href="download.php?token=${file.token}">تحميل</a>
                          <span class="note">مرات التحميل المتبقية: ${file.usage_limit ?? 'غير محدد'}</span>
                        </div>`
                      : `<a class="btn-warning" href="shared.php?file_id=${file.id}">إنشاء رابط جديد</a>`
                  }
                </div>
              </li>`;
          });
          html += '</ul>';
          fileContainer.innerHTML = html;
        } else {
          fileContainer.innerHTML = '<p class="info">لا توجد ملفات في هذا المجلد.</p>';
        }
      });
  }
</script>

</body>
</html>
