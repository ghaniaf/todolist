<?php
// ===================================================
// KELOMPOK PEKERJAAN 2 & 3: STRUKTUR DATA & LOGIKA
// ===================================================

session_start();

// Inisialisasi data array jika session belum ada
if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = [
        ["id" => 1, "title" => "Belajar PHP & Bootstrap", "status" => "belum"],
        ["id" => 2, "title" => "Bikin tampilan pastel hijau kuning", "status" => "selesai"]
    ];
}

// Reusable Function untuk badge status pastel
function getStatusBadge($status) {
    if ($status === 'selesai') {
        return '<span class="badge badge-pastel-success">Selesai</span>';
    }
    return '<span class="badge badge-pastel-warning">Belum Selesai</span>';
}

// 1. Logika Ubah Status Single (Toggle Checkbox)
if (isset($_POST['action']) && $_POST['action'] === 'toggle' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    foreach ($_SESSION['tasks'] as &$task) {
        if ($task['id'] === $id) {
            $task['status'] = ($task['status'] === 'selesai') ? 'belum' : 'selesai';
            break;
        }
    }
    header("Location: index.php");
    exit();
}

// 2. Logika CEKLIS SEMUA
if (isset($_GET['action']) && $_GET['action'] === 'check_all') {
    foreach ($_SESSION['tasks'] as &$task) {
        $task['status'] = 'selesai';
    }
    header("Location: index.php");
    exit();
}

// 3. Logika UN-CHECK ALL
if (isset($_GET['action']) && $_GET['action'] === 'uncheck_all') {
    foreach ($_SESSION['tasks'] as &$task) {
        $task['status'] = 'belum';
    }
    header("Location: index.php");
    exit();
}

// 4. Logika HAPUS SEMUA
if (isset($_GET['action']) && $_GET['action'] === 'clear_all') {
    $_SESSION['tasks'] = [];
    header("Location: index.php");
    exit();
}

// 5. Logika Hapus Single
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    foreach ($_SESSION['tasks'] as $key => $task) {
        if ($task['id'] === $id) {
            unset($_SESSION['tasks'][$key]);
            break;
        }
    }
    $_SESSION['tasks'] = array_values($_SESSION['tasks']);
    header("Location: index.php");
    exit();
}

// 6. Logika Tambah Tugas Baru
if (isset($_POST['add_task']) && !empty(trim($_POST['title']))) {
    $tasks = $_SESSION['tasks'];
    $newId = count($tasks) > 0 ? end($tasks)['id'] + 1 : 1;
    $_SESSION['tasks'][] = [
        "id" => $newId,
        "title" => htmlspecialchars($_POST['title']),
        "status" => "belum"
    ];
    header("Location: index.php");
    exit();
}

// 7. Logika SIMPAN EDIT TUGAS
if (isset($_POST['edit_task']) && isset($_POST['id']) && !empty(trim($_POST['title']))) {
    $id = (int)$_POST['id'];
    $newTitle = htmlspecialchars(trim($_POST['title']));
    foreach ($_SESSION['tasks'] as &$task) {
        if ($task['id'] === $id) {
            $task['title'] = $newTitle;
            break;
        }
    }
    header("Location: index.php");
    exit();
}

// Menyiapkan data untuk mode edit
$editTaskData = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $editId = (int)$_GET['id'];
    foreach ($_SESSION['tasks'] as $task) {
        if ($task['id'] === $editId) {
            $editTaskData = $task;
            break;
        }
    }
}

$tasks = $_SESSION['tasks'];

// Cek apakah semua tugas sudah selesai
$allCompleted = !empty($tasks) && array_reduce($tasks, function($carry, $item) {
    return $carry && ($item['status'] === 'selesai');
}, true);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi To-Do List</title>
    <!-- Framework CSS: Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Font: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f4; /* Background Soft Minty Gray */
            color: #2d3748; /* Teks Hitam Pekat/Dark Slate agar selalu jelas terbaca */
        }
        .card-pastel {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            background: #ffffff;
        }
        .card-header-pastel {
            /* Perpaduan Gradient Sage Green dan Soft Mustard/Yellow */
            background: linear-gradient(135deg, #556b2f 0%, #3e5223 100%);
            border-top-left-radius: 16px !important;
            border-top-right-radius: 16px !important;
            color: #ffffff;
        }
        .btn-pastel-primary {
            background-color: #6b8e23; /* Olive/Sage Green */
            border: none;
            color: #ffffff;
            font-weight: 500;
        }
        .btn-pastel-primary:hover {
            background-color: #556b2f;
            color: #ffffff;
        }
        .btn-pastel-action {
            background-color: #fef08a; /* Soft Butter Yellow */
            color: #713f12; /* Teks Cokelat Tua agar Terbaca Jelas */
            border: none;
            font-size: 0.82rem;
            font-weight: 500;
        }
        .btn-pastel-action:hover {
            background-color: #fde047;
            color: #713f12;
        }
        .btn-pastel-danger {
            background-color: #fee2e2;
            color: #991b1b;
            border: none;
            font-size: 0.82rem;
            font-weight: 500;
        }
        .btn-pastel-danger:hover {
            background-color: #fca5a5;
            color: #7f1d1d;
        }
        .badge-pastel-success {
            background-color: #dcfce7; /* Hijau Muda Segar */
            color: #14532d; /* Teks Hijau Tua Pekat */
            border-radius: 12px;
            padding: 6px 12px;
            font-weight: 500;
        }
        .badge-pastel-warning {
            background-color: #fef08a; /* Kuning Lembut */
            color: #713f12; /* Teks Cokelat Pekat */
            border-radius: 12px;
            padding: 6px 12px;
            font-weight: 500;
        }
        .form-check-input:checked {
            background-color: #6b8e23;
            border-color: #6b8e23;
        }
    </style>
</head>
<body class="py-5">
    <div class="container" style="max-width: 620px;">
        <div class="card card-pastel">
            <!-- Header Kartu -->
            <div class="card-header card-header-pastel p-4 text-center">
                <h3 class="fw-bold mb-1">Aplikasi To-Do List</h3>
                <p class="mb-0 small opacity-90">Kelola dan pantau tugas harianmu dengan mudah</p>
            </div>

            <div class="card-body p-4">
                <!-- Form Tambah / Edit Tugas -->
                <form method="POST" action="" class="mb-4">
                    <?php if ($editTaskData): ?>
                        <!-- Form Mode Edit -->
                        <div class="mb-2">
                            <span class="badge bg-secondary mb-1">Mode Edit</span>
                        </div>
                        <div class="input-group">
                            <input type="hidden" name="id" value="<?= $editTaskData['id']; ?>">
                            <input type="text" name="title" class="form-control rounded-start-pill border-end-0 px-3" value="<?= $editTaskData['title']; ?>" required>
                            <button type="submit" name="edit_task" class="btn btn-success px-4">Simpan</button>
                            <a href="index.php" class="btn btn-outline-secondary rounded-end-pill px-3">Batal</a>
                        </div>
                    <?php else: ?>
                        <!-- Form Mode Tambah -->
                        <div class="input-group">
                            <input type="text" name="title" class="form-control rounded-start-pill border-end-0 px-3" placeholder="Tulis tugas baru di sini..." required>
                            <button type="submit" name="add_task" class="btn btn-pastel-primary rounded-end-pill px-4">Tambah</button>
                        </div>
                    <?php endif; ?>
                </form>

                <!-- Tombol Aksi Massal -->
                <?php if (!empty($tasks)): ?>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <?php if ($allCompleted): ?>
                                <a href="?action=uncheck_all" class="btn btn-pastel-action rounded-pill px-3 me-1">
                                    Batalkan Semua
                                </a>
                            <?php else: ?>
                                <a href="?action=check_all" class="btn btn-pastel-action rounded-pill px-3 me-1">
                                    Ceklis Semua
                                </a>
                            <?php endif; ?>
                        </div>
                        <div>
                            <a href="?action=clear_all" class="btn btn-pastel-danger rounded-pill px-3" onclick="return confirm('Yakin ingin menghapus semua tugas?')">
                                Hapus Semua
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Daftar Tugas -->
                <ul class="list-group list-group-flush border-top">
                    <?php if (empty($tasks)): ?>
                        <li class="list-group-item text-center text-muted py-4 bg-transparent border-0">
                            Belum ada tugas. Silakan tambah tugas baru di atas.
                        </li>
                    <?php else: ?>
                        <?php foreach ($tasks as $task): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent py-3 px-2">
                                <div class="d-flex align-items-center">
                                    <!-- Checkbox Auto-Submit -->
                                    <form method="POST" action="" class="d-inline me-3">
                                        <input type="hidden" name="action" value="toggle">
                                        <input type="hidden" name="id" value="<?= $task['id']; ?>">
                                        <input class="form-check-input" type="checkbox" 
                                               <?= $task['status'] === 'selesai' ? 'checked' : ''; ?> 
                                               onchange="this.form.submit()" 
                                               style="cursor: pointer; width: 1.3em; height: 1.3em;">
                                    </form>

                                    <!-- Judul Tugas -->
                                    <span class="<?= $task['status'] === 'selesai' ? 'text-decoration-line-through text-muted' : 'fw-medium'; ?>">
                                        <?= $task['title']; ?>
                                    </span>
                                </div>

                                <div class="d-flex align-items-center">
                                    <?= getStatusBadge($task['status']); ?>
                                    
                                    <!-- Tombol Edit -->
                                    <a href="?action=edit&id=<?= $task['id']; ?>" class="btn btn-sm btn-outline-secondary ms-2 py-1 px-2" title="Edit Tugas">
                                        Edit
                                    </a>

                                    <!-- Tombol Hapus -->
                                    <a href="?action=delete&id=<?= $task['id']; ?>" class="btn btn-sm btn-outline-danger ms-1 py-1 px-2" title="Hapus Tugas" onclick="return confirm('Hapus tugas ini?')">
                                        Hapus
                                    </a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>