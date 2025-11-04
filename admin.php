<?php

// 1. Session start HARUS di paling atas
session_start([
    'cookie_lifetime' => 86400,
    'cookie_secure'   => false,
    'cookie_httponly' => true,
    'use_strict_mode' => true
]);

// 2. Header anti-cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// 3. Include config
require 'config.php';

// 4. Fungsi force logout
function force_logout() {
    session_unset();
    session_destroy();
    setcookie(session_name(), '', time() - 3600, '/');
    header("Location: admin.php");
    exit();
}

// 5. Handle action logout
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    force_logout();
}

// 6. Validasi session
$valid_session = isset($_SESSION['admin_logged_in']) && 
                 $_SESSION['admin_logged_in'] === true && 
                 isset($_SESSION['admin_id']);

// 7. Handle login attempt (Menggunakan database)
if (!$valid_session) {
    $error = '';
    
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // Gunakan prepared statement
    $stmt = $conn->prepare("SELECT id, username, password FROM admin_users WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        
        // Debug: Lihat hash password dari database
        error_log("Password Hash from DB: " . $admin['password']);
        error_log("Input Password: " . $password);
        
        // Verifikasi password
        if (password_verify($password, $admin['password'])) {
            // Pastikan session baru
            session_regenerate_id(true);
            
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['login_time'] = time();
            
            // Update last login
            $update = $conn->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
            $update->bind_param("i", $admin['id']);
            $update->execute();
            
            header("Location: admin.php");
            exit();
        } else {
            $error = "Password salah!";
            error_log("Login failed: Password mismatch");
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}

    // 8. Tampilkan form login
    ?>
<!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login Admin - Kampus Susu Bergoyang</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #2E8B57, #3CB371);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 10px; /* Supaya nggak mepet di HP */
            }

            .login-container {
                background: white;
                padding: 50px;
                border-radius: 10px;
                box-shadow: 0 10px 20px rgba(0,0,0,0.2);
                max-width: 400px;
                width: 100%;
                box-sizing: border-box;
                text-align: center;
                margin: 0 auto;
            }
            .login-container h2 {
                color: #2E8B57;
                margin-bottom: 25px;
            }
            .input-group {
                margin-bottom: 20px;
                text-align: left;
            }
            .input-group label {
                display: block;
                margin-bottom: 5px;
                color: #2F4F4F;
            }
            .input-group input {
                width: 100%;
                padding: 10px;
                border: 1px solid #ddd;
                border-radius: 5px;
                font-size: 1rem;
            }
            .btn {
                background-color: #2E8B57;
                color: white;
                border: none;
                padding: 12px 50px;
                border-radius: 5px;
                cursor: pointer;
                font-size: 1rem;
                display: block;
                margin: 20px auto; 

            }
            .btn:hover {
                background-color: #006400;
            }
            .error {
                color: #ff4444;
                margin-bottom: 15px;
            }

        </style>
    </head>
     <body>
        <div class="login-container">
            <h2><i class="fas fa-lock"></i> Admin Login</h2>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <form method="post">
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn">Login</button>
            </form>
        </div>
    </body>
</html>
<?php
    exit();
}

// Fungsi CRUD
if (isset($_GET['action'])) {
    switch ($_GET['action']) {

        //CRUD UNTUK SCHEDULE
        case 'add_schedule':
            if (isset($_POST['date'], $_POST['time_slot'])) {
                $date = $conn->real_escape_string($_POST['date']);
                $time = $conn->real_escape_string($_POST['time_slot']);
                $capacity = (int)$_POST['max_capacity'];
                
                $conn->query("INSERT INTO available_schedules (date, time_slot, max_capacity) 
                            VALUES ('$date', '$time', $capacity)");
            }
            header("Location: admin.php#available-schedules");
            break;

        case 'delete_schedule':
            if (isset($_GET['id'])) {
                $id = (int)$_GET['id'];
                $conn->query("DELETE FROM available_schedules WHERE id = $id");
            }
            header("Location: admin.php#available-schedules");
            break;
        
        case 'edit_schedule':
            if (isset($_POST['id'], $_POST['date'], $_POST['time_slot'], $_POST['max_capacity'])) {
                $id = (int)$_POST['id'];
                $date = $conn->real_escape_string($_POST['date']);
                $time = $conn->real_escape_string($_POST['time_slot']);
                $capacity = (int)$_POST['max_capacity'];
                
                $stmt = $conn->prepare("UPDATE available_schedules SET date=?, time_slot=?, max_capacity=? WHERE id=?");
                $stmt->bind_param("ssii", $date, $time, $capacity, $id);
                
                if ($stmt->execute()) {
                    $_SESSION['alert'] = ['type' => 'success', 'message' => 'Jadwal berhasil diupdate'];
                } else {
                    $_SESSION['alert'] = ['type' => 'error', 'message' => 'Gagal update jadwal: ' . $stmt->error];
                }
            }
            header("Location: admin.php#available-schedules");
            break;

        case 'delete_product':
            if (isset($_GET['id'])) {
                $id = (int)$_GET['id'];
                $conn->query("DELETE FROM products WHERE id = $id");
            }
            break;
            
        case 'add_product':
            if (isset($_POST['name'], $_POST['description'], $_POST['price'], $_FILES['image'])) {
                $name = $conn->real_escape_string($_POST['name']);
                $description = $conn->real_escape_string($_POST['description']);
                $price = (float)$_POST['price'];

                // Upload gambar
                $imageName = $_FILES['image']['name'];
                $imageTmp = $_FILES['image']['tmp_name'];
                $uploadDir = "uploads/";
                $imagePath = $uploadDir . basename($imageName);

                if (move_uploaded_file($imageTmp, $imagePath)) {
                    $conn->query("INSERT INTO products (name, description, price, image) 
                          VALUES ('$name', '$description', $price, '$imageName')");
                } else {
                    echo "Gagal upload gambar!";
                }
            }
            break;
        
        case 'edit_product':
            if (isset($_POST['id'], $_POST['name'], $_POST['description'], $_POST['price'], $_POST['old_image'])) {
                $id = (int)$_POST['id'];
                $name = $conn->real_escape_string($_POST['name']);
                $description = $conn->real_escape_string($_POST['description']);
                $price = (float)$_POST['price'];
                $old_image = $conn->real_escape_string($_POST['old_image']);

                // Cek jika ada file baru diupload
                if (isset($_FILES['image']) && $_FILES['image']['name'] != '') {
                    $imageName = $_FILES['image']['name'];
                    $imageTmp = $_FILES['image']['tmp_name'];
                    $uploadDir = "uploads/";
                    $imagePath = $uploadDir . basename($imageName);

                    if (move_uploaded_file($imageTmp, $imagePath)) {
                        $image = $imageName;
                    } else {
                        echo "Gagal upload gambar!";
                        exit();
                    }
                } else {
                    $image = $old_image; // Jika tidak upload baru, pakai gambar lama
                }

                $conn->query("UPDATE products SET 
                             name='$name', 
                             description='$description', 
                             price=$price, 
                             image='$image' 
                             WHERE id=$id");
            }
            break;
        case 'delete_booking':
            if (isset($_GET['id'])) {
                $id = (int)$_GET['id'];
                $conn->query("DELETE FROM bookings WHERE id = $id");
            }
            break;
        case 'update_contact':
            if (isset($_POST['phone'], $_POST['email'], $_POST['address'], $_POST['whatsapp'], $_POST['instagram'], $_POST['facebook'])) {
                $phone = $conn->real_escape_string($_POST['phone']);
                $email = $conn->real_escape_string($_POST['email']);
                $address = $conn->real_escape_string($_POST['address']);
                $whatsapp = $conn->real_escape_string($_POST['whatsapp']);
                $instagram = $conn->real_escape_string($_POST['instagram']);
                $facebook = $conn->real_escape_string($_POST['facebook']);
                
                $conn->query("UPDATE contacts SET 
                             phone = '$phone',
                             email = '$email',
                             address = '$address',
                             whatsapp = '$whatsapp',
                             instagram = '$instagram',
                             facebook = '$facebook'
                             LIMIT 1");
            }
            header("Location: admin.php#contacts");
            exit();

        case 'update_map':
            if (isset($_POST['embed_code'])) {
                $embed_code = $conn->real_escape_string($_POST['embed_code']);
                $conn->query("UPDATE maps SET embed_code = '$embed_code' LIMIT 1");
            }
            header("Location: admin.php#maps");
            exit();


        case 'change_password':
        if (isset($_POST['current_password'], $_POST['new_password'], $_POST['confirm_password'])) {
            $current = $_POST['current_password'];
            $new = $_POST['new_password'];
            $confirm = $_POST['confirm_password'];
            $admin_id = $_SESSION['admin_id'];
            
           // 1. Validasi panjang password
        if (strlen($new) < 8) {
            $_SESSION['alert'] = ['type' => 'error', 'message' => 'Password harus minimal 8 karakter!'];
            header("Location: admin.php#change-password");
            exit();
        }
        
        // 2. Validasi kecocokan password baru dan konfirmasi
        if ($new !== $confirm) {
            $_SESSION['alert'] = ['type' => 'error', 'message' => 'Password baru dan konfirmasi tidak cocok!'];
            header("Location: admin.php#change-password");
            exit();
        }
        
        // 3. Validasi kompleksitas password (huruf + angka)
        if (!preg_match('/[A-Za-z]/', $new) || !preg_match('/[0-9]/', $new)) {
            $_SESSION['alert'] = ['type' => 'error', 'message' => 'Password harus mengandung huruf dan angka!'];
            header("Location: admin.php#change-password");
            exit();
        }
                                                    
            if ($new !== $confirm) {
                $_SESSION['alert'] = ['type' => 'error', 'message' => 'Password baru dan konfirmasi tidak cocok!'];
                header("Location: admin.php#change-password");
                exit();
            }
        
        // Dapatkan password saat ini dari database
        $stmt = $conn->prepare("SELECT password FROM admin_users WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();
        
        if (password_verify($current, $admin['password'])) {
            // Hash password baru
            $hashed_password = password_hash($new, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashed_password, $admin_id);
            $stmt->execute();
            
            $_SESSION['alert'] = ['type' => 'success', 'message' => 'Password berhasil diubah!'];
        } else {
            $_SESSION['alert'] = ['type' => 'error', 'message' => 'Password saat ini salah!'];
        }
    }
    header("Location: admin.php#change-password");
    exit();
    }
    header("Location: admin.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Kampus Susu Bergoyang</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2E8B57;
            --light: #F0FFF0;
            --dark: #006400;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            display: flex;
            background-color: #f5f5f5;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background-color: var(--dark);
            color: white;
            min-height: 100vh;
            position: fixed;
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header h3 {
            margin-top: 10px;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .sidebar-menu ul {
            list-style: none;
        }
        
        .sidebar-menu li a {
            display: block;
            padding: 15px 20px;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .sidebar-menu li a:hover, 
        .sidebar-menu li a.active {
            background-color: var(--primary);
            padding-left: 25px;
        }
        
        .sidebar-menu li a i {
            margin-right: 10px;
        }
        
                    /* Nomor urut tabel */
        .table th:first-child,
        .table td:first-child {
            width: 50px;
            text-align: center;
         } 

        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .card-header h3 {
            color: var(--primary);
        }
        
        .btn {
            display: inline-block;
            background-color: var(--primary);
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
        }
        
        .btn:hover {
            background-color: var(--dark);
        }
        
        .btn i {
            margin-right: 5px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th, .table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .table th {
            background-color: #f8f9fa;
            color: var(--dark);
            font-weight: 600;
        }
        
        .table tr:hover {
            background-color: #f1f1f1;
        }
        
        .actions a {
            display: inline-block;
            margin-right: 10px;
            color: var(--primary);
            text-decoration: none;
        }
        
        .actions a.delete {
            color: #dc3545;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .form-group input, 
        .form-group select, 
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .form-row .form-group {
            flex: 1;
            margin-bottom: 0;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            width: 500px;
            max-width: 90%;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
            .contact-info p {
        margin-bottom: 10px;
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 4px;
        }
    
            .map-preview {
        width: 100%;
        height: 400px;
        background-color: #f5f5f5;
        border-radius: 4px;
        overflow: hidden;
        }
    
            .map-preview iframe {
        width: 100%;
        height: 100%;
        border: none;
         }

            .alert {
        padding: 10px;
        margin-bottom: 15px;
        border-radius: 4px;
        font-weight: 500;
        }

            .alert.success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
        }

            .alert.error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
        }
        .close {
            font-size: 1.5rem;
            cursor: pointer;
        }

        .alert {
            padding: 15px;
            margin: 15px 20px;
            border-radius: 4px;
            font-size: 14px;
            position: relative;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Animasi fade in */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .alert {
            animation: fadeIn 0.3s ease-out;
        }

        /* Tombol close */
        .alert .close {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-cow fa-3x"></i>
            <h3>Admin Panel</h3>
        </div>
        <div class="sidebar-menu">
        <ul>
            <li><a href="#"><i class="fas fa-home"></i> Admin</a></li>
            <li><a href="#products"><i class="fas fa-wine-bottle"></i> Produk</a></li>
            <li><a href="#bookings"><i class="fas fa-book"></i> Pemesanan</a></li>
            <li><a href="#available-schedules"><i class="fas fa-calendar-alt"></i> Jadwal Tersedia</a></li>
            <li><a href="#bookings"><i class="fas fa-book"></i> Daftar Pemesanan</a></li>
            <li><a href="#contacts"><i class="fas fa-address-book"></i> Kontak</a></li>
            <li><a href="#maps"><i class="fas fa-map-marked-alt"></i> Peta</a></li>
            <li><a href="#change-password"><i class="fas fa-key"></i> Ganti Password</a></li>
            <li class="logout-btn">
            <a href="logout.php?action=logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </li>
        </div>  
    </div>
    
<!-- Main Content -->
<div class="main-content">  
    <div class="header">
        <h2>Dashboard Admin</h2>
        <a href="logout.php" class="btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h3>Ringkasan</h3>
        </div>
        <div class="form-row">
            <div class="card">
                <h4>Total Produk</h4>
                <h2>
                    <?php
                    $sql = "SELECT COUNT(*) as total FROM products";
                    $result = $conn->query($sql);
                    $row = $result->fetch_assoc();
                    echo $row['total'];
                    ?>
                </h2>
            </div>
            <div class="card">
                <h4>Total Pemesanan</h4>
                <h2>
                    <?php
                    $sql = "SELECT COUNT(*) as total FROM bookings";
                    $result = $conn->query($sql);
                    $row = $result->fetch_assoc();
                    echo $row['total'];
                    ?>
                </h2>
            </div>

            <div class="card">
                <h4>Pemesanan Hari Ini</h4>
                <h2>
                    <?php
                    $today = date('Y-m-d');
                    $sql = "SELECT COUNT(*) as total FROM bookings WHERE booking_date = '$today'";
                    $result = $conn->query($sql);
                    $row = $result->fetch_assoc();
                    echo $row['total'];
                    ?>
                </h2>
            </div>
        </div>
    </div>

    <!--Available schedule section-->
    <div class="card" id="available-schedules">
            <div class="card-header">
                <h3>Kelola Jadwal Tersedia</h3>
                <button class="btn" onclick="openModal('addScheduleModal')">
                    <i class="fas fa-plus"></i> Tambah Slot Jadwal
                </button>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Kapasitas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM available_schedules ORDER BY date, time_slot";
                    $result = $conn->query($sql);
                    
                    if ($result->num_rows > 0) {
                        $no = 1;
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$no}</td>
                                    <td>{$row['date']}</td>
                                    <td>{$row['time_slot']}</td>
                                    <td>{$row['max_capacity']}</td>
                                    <td class='actions'>
                                        <a href='admin.php?action=delete_schedule&id={$row['id']}' class='delete' onclick='return confirm(\"Yakin ingin menghapus?\")'>
                                            <i class='fas fa-trash'></i>
                                        </a>
                                    </td>
                                </tr>";
                            $no++;
                        }
                    } else {
                        echo "<tr><td colspan='5'>Belum ada jadwal tersedia</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>


        <!--Modal add schedule-->
        <div id="addScheduleModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Tambah Slot Jadwal Baru</h3>
                <span class="close" onclick="closeModal('addScheduleModal')">&times;</span>
            </div>
            <form method="post" action="admin.php?action=add_schedule">
                <div class="form-group">
                    <label for="schedule_date">Tanggal</label>
                    <input type="date" id="schedule_date" name="date" required min="<?= date('Y-m-d') ?>">
                </div>
                <div class="form-group">
                    <label for="schedule_time">Jam</label>
                    <input type="time" id="schedule_time" name="time_slot" required>
                </div>
                <div class="form-group">
                    <label for="max_capacity">Kapasitas Maksimal</label>
                    <input type="number" id="max_capacity" name="max_capacity" min="1" value="10" required>
                </div>
                <button type="submit" class="btn">Simpan Jadwal</button>
            </form>
        </div>
    </div>

    <!-- Modal Edit Jadwal -->
    <div id="editScheduleModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Jadwal</h3>
                <span class="close" onclick="closeModal('editScheduleModal')">&times;</span>
            </div>
            <form method="post" action="admin.php?action=edit_schedule">
                <input type="hidden" name="id" id="schedule_id">
                <div class="form-group">
                    <label for="schedule_date">Tanggal</label>
                    <input type="date" id="schedule_date" name="date" required min="<?= date('Y-m-d') ?>">
                </div>
                <div class="form-group">
                    <label for="schedule_time">Jam</label>
                    <input type="time" id="schedule_time" name="time_slot" required>
                </div>
                <div class="form-group">
                    <label for="max_capacity">Kapasitas Maksimal</label>
                    <input type="number" id="max_capacity" name="max_capacity" required min="1">
                </div>
                <button type="submit" class="btn">Simpan Perubahan</button>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, date, time, capacity) {
        document.getElementById('schedule_id').value = id;
        document.getElementById('schedule_date').value = date;
        document.getElementById('schedule_time').value = time;
        document.getElementById('max_capacity').value = capacity;

        // Tampilkan modal
        document.getElementById('editScheduleModal').style.display = 'flex'; // Flex agar bisa pakai justify-center
    }

        function closeModal(id) {
        document.getElementById(id).style.display = 'none';
        }

        // Tutup modal jika klik di luar modal-content
        window.onclick = function(event) {
        const modal = document.getElementById('editScheduleModal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    }
    </script>
        
    <!-- Produk Section -->
    <div class="card" id="products">
        <div class="card-header">
            <h3>Kelola Produk</h3>
            <button class="btn" onclick="openModal('addProductModal')">
                <i class="fas fa-plus"></i> Tambah Produk
            </button>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Deskripsi</th>
                    <th>Harga</th>
                    <th>Gambar</th> 
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $sql = "SELECT * FROM products ORDER BY id ASC";
                $result = $conn->query($sql);
                
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$no}</td>
                                <td>{$row['name']}</td>
                                <td>{$row['description']}</td>
                                <td>Rp " . number_format($row['price'],0,',','.') . "</td>
                                <td><img src='uploads/{$row['image']}' width='100' style='max-height: 100px; object-fit: contain;'></td>
                                <td class=\"actions\">
                                    <a href=\"#\" onclick=\"openEditModal({$row['id']}, '{$row['name']}', '{$row['description']}', {$row['price']}, '{$row['image']}')\">
                                        <i class=\"fas fa-edit\"></i>
                                    </a>
                                    <a href=\"admin.php?action=delete_product&id={$row['id']}\" class=\"delete\" onclick=\"return confirm('Yakin ingin menghapus?')\">
                                        <i class=\"fas fa-trash\"></i>
                                    </a>
                                </td>
                            </tr>";
                            $no++;
                    }
                } else {
                    echo "<tr><td colspan='6'>Tidak ada produk</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pemesanan Section -->
    <div class="card" id="bookings">
        <div class="card-header">
            <h3>Daftar Pemesanan</h3>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>No. Telepon</th>
                    <th>Tanggal Booking</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $sql = "SELECT * FROM bookings ORDER BY booking_time DESC";
                $result = $conn->query($sql);
                
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$no}</td>
                                <td>{$row['customer_name']}</td>
                                <td>{$row['booking_date']}</td>
                                <td>{$row['time_slot']}</td>
                                <td>{$row['phone']}</td>
                                <td>{$row['booking_time']}</td>
                                <td class=\"actions\">
                                    <a href=\"https://wa.me/62{$row['phone']}\" target=\"_blank\">
                                        <i class=\"fab fa-whatsapp\"></i>
                                    </a>
                                    <a href=\"admin.php?action=delete_booking&id={$row['id']}\" class=\"delete\" onclick=\"return confirm('Yakin ingin menghapus?')\">
                                        <i class=\"fas fa-trash\"></i>
                                    </a>
                                </td>
                            </tr>";
                            $no++;
                    }
                } else {
                    echo "<tr><td colspan='7'>Tidak ada pemesanan</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    
    <!-- Kontak Section -->
    <div class="card" id="contacts">
        <div class="card-header">
            <h3>Kelola Kontak</h3>
            <button class="btn" onclick="openModal('editContactModal')">
                <i class="fas fa-edit"></i> Edit Kontak
            </button>
        </div>
        <div class="card-body">
            <?php
            $contact = $conn->query("SELECT * FROM contacts LIMIT 1")->fetch_assoc();
            if (!$contact) {
                $conn->query("INSERT INTO contacts (phone, email, address, whatsapp, instagram, facebook) 
                             VALUES ('62895390892346', 'info@kampussusubergoyang.com', 'Jl.Raya Kopeng, Pijil, Sumogawe', '62895390892346', 'kampussusubergoyang', 'kampussusubergoyang')");
                $contact = $conn->query("SELECT * FROM contacts LIMIT 1")->fetch_assoc();
            }
            ?>
            <div class="contact-info">
                <p><strong>Telepon:</strong> <?= $contact['phone'] ?></p>
                <p><strong>Email:</strong> <?= $contact['email'] ?></p>
                <p><strong>Alamat:</strong> <?= $contact['address'] ?></p>
                <p><strong>WhatsApp:</strong> <?= $contact['whatsapp'] ?></p>
                <p><strong>Instagram:</strong> @<?= $contact['instagram'] ?></p>
                <p><strong>Facebook:</strong> <?= $contact['facebook'] ?></p>
            </div>
        </div>
    </div>
    
    <!-- Maps Section -->
    <div class="container" id="maps">
        <div class="container">
            <h3>Kelola Lokasi Peta</h3>
            <button class="btn" onclick="openModal('editMapModal')">
                <i class="fas fa-map-marked-alt"></i> Edit Peta
            </button>
        </div>
        <div class="map-container">
            <?php
            $map = $conn->query("SELECT * FROM maps LIMIT 1")->fetch_assoc();
            if (!$map) {
                $conn->query("INSERT INTO maps (embed_code) VALUES ('<iframe src=\"https://www.google.com/maps/embed?pb=...\" width=\"100%\" height=\"400\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\"></iframe>')");
                $map = $conn->query("SELECT * FROM maps LIMIT 1")->fetch_assoc();
            }
            ?>
            <div class="map-preview">
                <?= htmlspecialchars_decode($map['embed_code']) ?>
            </div>
        </div>
    </div>

    <?php 
    if (isset($_SESSION['alert'])) {
    echo '<div class="alert ' . $_SESSION['alert']['type'] . '">' . $_SESSION['alert']['message'] . '</div>';
    unset($_SESSION['alert']); // Hapus alert setelah ditampilkan
}

// 3. Debugging - letakkan di bagian change_password action
// (Ini sebenarnya sudah ada di kode Anda, tapi saya tunjukkan letak yang tepat)
if (isset($_GET['action']) && $_GET['action'] == 'change_password') {
    if (isset($_POST['current_password'], $_POST['new_password'], $_POST['confirm_password'])) {
        error_log("Current password input: " . $_POST['current_password']);
        error_log("New password input: " . $_POST['new_password']);
        error_log("Confirm password input: " . $_POST['confirm_password']);

    }
}
    if (isset($_SESSION['alert'])): ?>
    <div style="padding:10px; background-color:<?= $_SESSION['alert']['type'] == 'error' ? '#f8d7da' : '#d4edda' ?>;
                color:<?= $_SESSION['alert']['type'] == 'error' ? '#721c24' : '#155724' ?>;
                border:1px solid <?= $_SESSION['alert']['type'] == 'error' ? '#f5c6cb' : '#c3e6cb' ?>;
                margin-bottom: 15px;">
        <?= $_SESSION['alert']['message'] ?>
    </div>
    <?php unset($_SESSION['alert']); ?>
<?php endif; ?>

    <!-- Change Password Section -->
<div class="card" id="change-password">
    <div class="card-header">
        <h3>Ganti Password Admin</h3>
    </div>
    <form method="post" action="admin.php?action=change_password">
        <div class="form-group">
            <label for="current_password">Password Saat Ini</label>
            <input type="password" id="current_password" name="current_password" required>
        </div>
        <div class="form-group">
            <label for="new_password">Password Baru</label>
            <input type="password" id="new_password" name="new_password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Konfirmasi Password Baru</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn">Ganti Password</button>
    </form>
</div>

<!-- Modal Edit Kontak -->
<div id="editContactModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Kontak</h3>
            <span class="close" onclick="closeModal('editContactModal')">&times;</span>
        </div>
        <form method="post" action="admin.php?action=update_contact">
            <?php
            $contact = $conn->query("SELECT * FROM contacts LIMIT 1")->fetch_assoc();
            ?>
            <div class="form-group">
                <label for="contact_phone">Telepon</label>
                <input type="text" id="contact_phone" name="phone" value="<?= $contact['phone'] ?>" required>
            </div>
            <div class="form-group">
                <label for="contact_email">Email</label>
                <input type="email" id="contact_email" name="email" value="<?= $contact['email'] ?>" required>
            </div>
            <div class="form-group">
                <label for="contact_address">Alamat</label>
                <textarea id="contact_address" name="address" rows="3" required><?= $contact['address'] ?></textarea>
            </div>
            <div class="form-group">
                <label for="contact_whatsapp">WhatsApp</label>
                <input type="text" id="contact_whatsapp" name="whatsapp" value="<?= $contact['whatsapp'] ?>" required>
            </div>
            <div class="form-group">
                <label for="contact_instagram">Instagram</label>
                <input type="text" id="contact_instagram" name="instagram" value="<?= $contact['instagram'] ?>" required>
            </div>
            <div class="form-group">
                <label for="contact_facebook">Facebook</label>
                <input type="text" id="contact_facebook" name="facebook" value="<?= $contact['facebook'] ?>" required>
            </div>
            <button type="submit" class="btn">Simpan Perubahan</button>
        </form>
    </div>
</div>

<!-- Modal Edit Peta -->
<div id="editMapModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Embed Peta</h3>
            <span class="close" onclick="closeModal('editMapModal')">&times;</span>
        </div>
        <form method="post" action="admin.php?action=update_map">
            <?php
            $map = $conn->query("SELECT * FROM maps LIMIT 1")->fetch_assoc();
            ?>
            <div class="form-group">
                <label for="map_embed">Kode Embed Google Maps</label>
                <textarea id="map_embed" name="embed_code" rows="6" required><?= htmlspecialchars($map['embed_code']) ?></textarea>
                <small>Dapatkan kode embed dari Google Maps dengan memilih "Bagikan" > "Sematkan Peta"</small>
            </div>
            <button type="submit" class="btn">Simpan Perubahan</button>
        </form>
    </div>
</div>

    <!-- Modal Tambah Produk -->
    <div id="addProductModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Tambah Produk Baru</h3>
                <span class="close" onclick="closeModal('addProductModal')">&times;</span>
            </div>
            <form method="post" action="admin.php?action=add_product" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Nama Produk</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="description">Deskripsi</label>
                    <textarea id="description" name="description" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label for="price">Harga (Rp)</label>
                    <input type="number" id="price" name="price" required>
                </div>
                <div class="form-group">
                    <label for="image">Gambar Produk</label>
                    <input type="file" id="image" name="image" accept="image/*" required>
                </div>
                <button type="submit" class="btn">Simpan</button>
            </form>
        </div>
    </div>
    
    <!-- Modal Edit Produk -->
    <div id="editProductModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Produk</h3>
                <span class="close" onclick="closeModal('editProductModal')">&times;</span>
            </div>
            <form method="post" action="admin.php?action=edit_product" enctype="multipart/form-data">
                <input type="hidden" id="edit_id" name="id">
                <div class="form-group">
                    <label for="edit_name">Nama Produk</label>
                    <input type="text" id="edit_name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="edit_description">Deskripsi</label>
                    <textarea id="edit_description" name="description" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label for="edit_price">Harga (Rp)</label>
                    <input type="number" id="edit_price" name="price" required>
                </div>
                <div class="form-group">
                    <label for="edit_image">Gambar Produk (Kosongkan jika tidak diubah)</label>
                    <input type="file" id="edit_image" name="image" accept="image/*">
                </div>
                <input type="hidden" id="old_image" name="old_image">
                <button type="submit" class="btn">Simpan Perubahan</button>
            </form>
        </div>
    </div>
    
    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'flex';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        function openEditModal(id, name, description, price, image) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_description').value = description;
        document.getElementById('edit_price').value = price;
        document.getElementById('old_image').value = image; // Tambahan
        openModal('editProductModal');
}

        
        // Close modal jika klik di luar modal
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>

    <script>
    
    function setActiveSidebarMenu(menuItem) { //sidebar
        const menuItems = document.querySelectorAll('.sidebar-menu li a');
        menuItems.forEach(item => {
            item.classList.remove('active');
        });
        menuItem.classList.add('active');
    }
    document.addEventListener('DOMContentLoaded', function() {
        const menuItems = document.querySelectorAll('.sidebar-menu li a');
        menuItems.forEach(item => {
            item.addEventListener('click', function() {
                setActiveSidebarMenu(this);
            });
        });
    });
</script>
</body>
</html> 
