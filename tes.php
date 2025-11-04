<?php 
session_start();
include 'config.php';
// Ambil data kontak dari database
$contact = $conn->query("SELECT * FROM contacts LIMIT 1")->fetch_assoc();
if (!$contact) {
    // Jika tidak ada data, buat default
    $contact = [
        'phone' => '62895390892346',
        'email' => 'info@kampussusubergoyang.com',
        'address' => 'Jl.Raya Kopeng, Pijil, kopeng sjjsd',
        'whatsapp' => '62895390892346',
        'instagram' => 'kampussusubergoyang',
        'facebook' => 'kampussusubergoyang'
    ];
}
 ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kampus Susu Bergoyang</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
                --primary: #2E8B57;
                --secondary: #3CB371; 
                --accent: #90EE90; 
                --light: #F0FFF0; 
                --dark: #006400; 
                --text: #2F4F4F; 
                --success: #4CAF50;
            }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: var(--light);
            color: var(--text);
            line-height: 1.6;
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        header {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            padding: 20px 0;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }
        
        .cow-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.1;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath fill='%23ffffff' d='M18.5,15C19.9,15 21,13.9 21,12.5C21,11.1 19.9,10 18.5,10H16.91C17.59,9.26 18,8.31 18,7.26C18,5.6 16.66,4.26 15,4.26C14.22,4.26 13.5,4.53 12.91,5L12,5.76L11.09,5C10.5,4.53 9.78,4.26 9,4.26C7.34,4.26 6,5.6 6,7.26C6,8.31 6.41,9.26 7.09,10H5.5C4.1,10 3,11.1 3,12.5C3,13.9 4.1,15 5.5,15H7.05C7,15.16 7,15.33 7,15.5C7,17.71 8.79,19.5 11,19.5C12.15,19.5 13.18,19 14,18.23C14.82,19 15.85,19.5 17,19.5C19.21,19.5 21,17.71 21,15.5C21,15.33 21,15.16 20.95,15H18.5M5.5,12C5.22,12 5,12.22 5,12.5C5,12.78 5.22,13 5.5,13H11V12H5.5M18.5,13C18.78,13 19,12.78 19,12.5C19,12.22 18.78,12 18.5,12H13V13H18.5Z'/%3E%3C/svg%3E");
            background-repeat: repeat;
            z-index: 0;
        }
        
        .logo {
            position: relative;
            z-index: 1;
        }
        
        .logo h1 {
            font-size: 2.5rem;
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            margin-bottom: 10px;
            letter-spacing: 2px;
        }
        
        .logo p {
            color: var(--accent);
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto;
        }
        
        nav {
            background-color: var(--dark);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        nav ul {
            display: flex;
            justify-content: center;
            list-style: none;
            flex-wrap: wrap;
            margin: 0 auto;
            padding: 0;
            width: fit-content;
        }
        
        nav ul li {
            margin: 0 15px;
            text-align: center;
            min-width: max-content;
        }
        
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 8px 15px;
            border-radius: 30px;
            transition: all 0.3s ease;
        }
        
        nav ul li a:hover, 
        nav ul li a.active {
            background-color: var(--primary);
            color: var(--accent);
        }
        
        section {
            padding: 60px 0;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }
        
        .section-title h2 {
            font-size: 2.2rem;
            color: var(--primary);
            display: inline-block;
            padding: 0 20px;
            background-color: var(--light);
            position: relative;
            z-index: 1;
        }
        
        .section-title::after {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background-color: var(--secondary);
            z-index: 0;
        }
        
        .about-content {
            display: flex;
            gap: 40px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .about-text {
            flex: 1;
            min-width: 300px;
        }
        
        .about-image {
            flex: 1;
            min-width: 300px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .about-image img {
            width: 100%;
            height: auto;
            display: block;
            transition: transform 0.5s ease;
        }
        
        .about-image:hover img {
            transform: scale(1.05);
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }
        
        .product-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .product-image {
            height: 200px;
            background-color: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: var(--primary);
        }
        
        .product-info {
            padding: 20px;
        }
        
        .product-info h3 {
            color: var(--primary);
            margin-bottom: 10px;
        }
        
        .product-info .price {
            font-weight: bold;
            color: var(--dark);
            font-size: 1.2rem;
            margin: 10px 0;
        }
        
        .product-info .description {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 15px;
        }
        
        .schedule-container {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 0 auto;
        }
        
        .schedule-list {
            margin: 30px 0;
        }
        
        .schedule-item {
            display: flex;
            justify-content: space-between;
            padding: 15px 20px;
            background-color: var(--light);
            margin-bottom: 15px;
            border-radius: 8px;
            border-left: 4px solid var(--primary);
        }
        
        .schedule-item:last-child {
            margin-bottom: 0;
        }
        
        .booking-form {
            margin-top: 40px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }
        
        .form-group input, 
        .form-group select, 
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus, 
        .form-group select:focus, 
        .form-group textarea:focus {
            border-color: var(--primary);
            outline: none;
        }
        
        .btn {
            display: inline-block;
            background-color: var(--primary);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
        }
        
        .btn:hover {
            background-color: var(--dark);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .btn-block {
            display: block;
            width: 100%;
        }
        
        .map-container {
        height: 500px;
        border-radius: 10px;
        overflow: hidden;
        }

        .map-container iframe {
        width: 100%;
        height: 100%;
        border: none;
        }
        
        footer {
            background-color: var(--dark);
            color: white;
            padding: 40px 0 20px;
            text-align: center;
        }
        
        .footer-content {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }
        
        .footer-section {
            flex: 1;
            min-width: 250px;
            margin-bottom: 20px;
        }
        
        .footer-section h3 {
            margin-bottom: 20px;
            color: var(--accent);
        }
        
        .social-links {
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        
        .social-links a {
            display: inline-block;
            width: 40px;
            height: 40px;
            background-color: rgba(255,255,255,0.1);
            border-radius: 50%;
            text-align: center;
            line-height: 40px;
            color: white;
            transition: all 0.3s ease;
        }
        
        .social-links a:hover {
            background-color: var(--primary);
            transform: translateY(-5px);
        }
        
        .copyright {
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            font-size: 0.9rem;
            color: rgba(255,255,255,0.7);
        }
        
        .profile-menu {
            margin-left: auto; /* Pindah ke sebelah kanan */
        }

        .profile-pic {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--accent);
            transition: all 0.3s ease;
        }

        .profile-pic:hover {
            transform: scale(1.1);
            border-color: var(--primary);
        }

        .booking-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            max-width: 400px;
            padding: 15px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
            z-index: 1000;
            animation: slideIn 0.3s ease-out;
        }

        .booking-notification.success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
        }

        .booking-notification.error {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }

        .booking-notification i {
            font-size: 1.5rem;
        }

        .booking-notification button {
            background: none;
            border: none;
            cursor: pointer;
            color: inherit;
        }

@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}


        @media (max-width: 768px) {
            .about-content {
                flex-direction: column;
            }
            
            .section-title h2 {
                font-size: 1.8rem;
            }
            
            nav ul {
                flex-direction: column;
                align-items: center;
            }
            
            nav ul li {
                margin: 5px 0;
            }

            html {
                scroll-behavior: smooth;
            }

             .profile-menu {
                margin-left: 0;
                order: -1; /* Pindah ke atas di mode mobile */
            }
            
            .profile-pic {
                width: 35px;
                height: 35px;
            }

        }
    </style>
</head>
<script>
// Auto close notifikasi setelah 5 detik
document.addEventListener('DOMContentLoaded', function() {
    const notification = document.querySelector('.booking-notification');
    if (notification) {
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }
});
</script>
<body>
    <?php if (isset($_SESSION['booking_notification'])): ?>
        <div class="booking-notification <?= $_SESSION['booking_notification']['type'] ?>">
            <i class="fas <?= $_SESSION['booking_notification']['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
            <div>
                <p><?= $_SESSION['booking_notification']['message'] ?></p>
                <?php if (isset($_SESSION['booking_notification']['details'])): ?>
                    <ul>
                        <li>Nama: <?= $_SESSION['booking_notification']['details']['name'] ?></li>
                        <li>Tanggal: <?= $_SESSION['booking_notification']['details']['date'] ?></li>
                        <li>Waktu: <?= $_SESSION['booking_notification']['details']['time'] ?></li>
                    </ul>
                <?php endif; ?>
            </div>
            <button onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php unset($_SESSION['booking_notification']); ?>
    <?php endif; ?>

    <!-- Header -->
    <header>
        <div class="cow-bg"></div>
        <div class="container logo">
            <h1>SELAMAT DATANG DI KAMPUS SUSU BERGOYANG</h1>
            <p>Kampung Susu Bergoyang menyediakan layanan reservasi joglo sebagai sarana edukasi dan menjual berbagai produk hasil olahan susu sapi.</p>
        </div>
    </header>

    
    <!-- Navigation -->
    <nav>
        <div class="container">
            <ul>
                <li><a href="#pemilik">Pemilik</a></li>
                <li><a href="#about">Tentang Kami</a></li>
                <li><a href="#products">Produk</a></li>
                <li><a href="#schedule">Jadwal & Booking</a></li>
                <li><a href="#maps">Kontak</a></li> 
                <li class="profile-menu">
                <a href="admin.php" class="profile-link">
                    <img src="uploads/ibu.jpg" alt="Admin" class="profile-pic">
                </a>
            </li>
            </ul>
        </div>
    </nav>

    <!-- Pemilik Section -->
    <section id="pemilik">
        <div class="container">
            <div class="section-title">
                <h2>Tentang Pemilik</h2>
            </div>
            <div class="about-content">
                <div class="about-text">
                    <p>Usaha ini berawal dari masa sulit pada tahun 2008, ketika krisis moneter menghantam dan banyak usaha, termasuk peternakan, mengalami keruntuhan. Dari sinilah muncul dorongan dari Dinas dan Kecamatan Getasan untuk memberdayakan masyarakat melalui pemanfaatan susu—tidak hanya sebagai minuman, tetapi juga sebagai bahan baku produk lain seperti es krim, yoghurt, hingga sabun. Awalnya, sang pemilik tidak berniat membangun usaha; niatnya hanya ingin fokus membesarkan peternakan. Karena tidak cocok bekerja di bawah perintah orang lain, akhirnya ia mulai membuat sabun susu dan membagikannya ke keluarga—tanpa terpikir akan menjadi usaha besar.</p>
                    <p>Namun, jalan menuju keberhasilan tidak semulus yang dibayangkan. Usaha ini tumbuh lewat proses jatuh bangun, hingga akhirnya dikenal masyarakat luas. Sejak awal, belum banyak pesaing, dan dari situlah keunikan produk mulai menonjol. Pada tahun 2008, tempat ini sudah menerima tamu, tetapi kegiatan edukasi baru benar-benar berjalan pada 2017. Di Desa Sumogawe, sempat terbentuk kelompok sadar wisata (pokdarwis) dan muncullah nama “Kampung Susu Sumogawe” yang sempat menjadi daya tarik. Sayangnya, karena muncul konflik internal antara pihak pribadi dan pokdarwis, Kepala Desa akhirnya membentuk rest area baru dan nama “Kampung Susu” diklaim oleh pihak lain.</p>
                    <p>Demi menghindari konflik, tempat ini berganti nama menjadi Kampus Susu Bergoyang, atas saran dari Dinas Pariwisata yang meyakinkan bahwa rebranding bukanlah akhir dari perjalanan. Kata “kampus” sendiri diambil dari kata “kampung”, tempat berbagi ilmu dan pengalaman. Sementara “susu bergoyang” mencerminkan filosofi bahwa susu bisa “bergoyang”—diolah menjadi berbagai produk kreatif seperti sabun, yoghurt, permen, hingga kerupuk. Kini, orang-orang mengenal sabun susu Thalita sebagai salah satu produk andalan dari tempat ini, yang terus tumbuh sebagai pusat edukasi dan inovasi produk berbasis susu.</p>
                </div>
                <div class="about-image">
                <img src="uploads/ibu.jpg" alt="Sapi Kami" style="width:100%; height:100%; object-fit:cover;">
                </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- About Section -->
    <section id="about">
        <div class="container">
            <div class="section-title">
                <h2>Tentang Kampus Susu Bergoyang</h2>
            </div>
            <div class="about-content">
                <div class="about-text">
                    <p>Kampus Susu Bergoyang adalah destinasi wisata edukasi peternakan sapi perah yang terletak di kawasan sejuk dengan pemandangan indah. Kami menawarkan pengalaman langsung mengenal proses produksi susu sapi segar menjadi sabun dari kandang hingga siap digunakan.</p>
                    <p>Selain sebagai tempat edukasi, kami juga memproduksi berbagai olahan dari susu berkualitas tinggi yang diolah secara higienis dengan tetap menjaga nutrisi asli dari susu sapi segar.</p>
                    <p>Fasilitas yang kami sediakan meliputi area peternakan, pabrik pengolahan susu, dan joglo tradisional yang dapat disewa untuk berbagai acara seperti gathering, ulang tahun, atau pertemuan keluarga.</p>
                </div>
                <div class="about-image">
                <img src="uploads/rumah.jpg" alt="Sapi Kami" style="width:100%; height:100%; object-fit:cover;">
                </div>
                </div>
            </div>
        </div>
    </section>
    
  <!-- Products Section -->
<section id="products" style="background-color: #FFF8DC; padding: 50px 0;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <div class="section-title" style="text-align: center; margin-bottom: 40px;">
            <h2 style="color: #2E8B57; font-size: 32px;">Produk Olahan Susu Kami</h2>
            <p style="color: #2E8B57;">Berbagai olahan susu segar dengan kualitas terbaik</p>
        </div>
        
        <div class="products-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px;">
            <?php
            // Ambil 3 produk pertama untuk halaman utama
            $sql = "SELECT * FROM products LIMIT 3";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<div class="product-card" style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1); transition: transform 0.3s;">
                            <div class="product-image" style="height: 200px; display: flex; justify-content: center; align-items: center; background-color: #F5DEB3;">
                                <img src="uploads/'.$row['image'].'" alt="'.$row['name'].'" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                            </div>
                            <div class="product-info" style="padding: 20px;">
                                <h3 style="margin: 0 0 10px; color: #2E8B57; font-size: 20px;">'.$row['name'].'</h3>
                                <div class="price" style="font-weight: bold; color: #2E8B57; margin-bottom: 10px;">Rp '.number_format($row['price'],0,',','.').'</div>
                                <div class="description" style="color: #666; margin-bottom: 15px; height: 60px; overflow: hidden;">'.$row['description'].'</div>
                                <a href="https://wa.me/62895390892346?text=Halo%20saya%20ingin%20membeli%20produk%20'.$row['name'].'%20dari%20Kampus%20Susu%20Bergoyang" class="btn" target="_blank" style="display: inline-block; background-color: #2E8B57; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; text-align: center; width: 100%;">Beli Sekarang</a>
                            </div>
                        </div>';
                }
            } else {
                echo "<p style='text-align: center; grid-column: 1/-1;'>Tidak ada produk tersedia</p>";
            }
            ?>
        </div>
        
        <div style="text-align: center; margin-top: 40px;">
            <a href="produk.php" class="btn-view-more" style="display: inline-block; background-color: #2E8B57; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; transition: background-color 0.3s;">Lihat Semua Produk</a>
        </div>
    </div>
</section>
    
<!-- Jadwal & Booking Section -->
<section id="schedule">
    <div class="container">
        <div class="section-title">
            <h2>Jadwal & Booking Joglo</h2>
        </div>
        <div class="schedule-container">
            <h3>Form Pemesanan Joglo</h3>
            <form id="bookingForm" action="booking.php" method="POST">
                <div class="form-group">
                    <label for="bookingDate">Pilih Tanggal</label>
                    <select id="bookingDate" name="date" required>
                        <option value="">-- Pilih Tanggal --</option>
                        <?php
                        $sql = "SELECT DISTINCT date FROM available_schedules 
                                WHERE date >= CURDATE() 
                                ORDER BY date";
                        $result = $conn->query($sql);
                        while($row = $result->fetch_assoc()) {
                            $formattedDate = date('d F Y', strtotime($row['date']));
                            echo "<option value='{$row['date']}'>{$formattedDate}</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="time_slot">Pilih Jam</label>
                    <select id="time_slot" name="time_slot" required disabled>
                        <option value="">-- Pilih Tanggal terlebih dahulu --</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" id="name" name="name" placeholder="Masukkan nama lengkap" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">No. Telepon</label>
                    <input type="tel" id="phone" name="phone" placeholder="Masukkan nomor telepon" required>
                </div>
                
                <button type="submit" class="btn btn-block">Booking Sekarang</button>
            </form>
        </div>
    </div>
</section>

<script>
document.getElementById('bookingDate').addEventListener('change', function() {
    const date = this.value;
    const timeSlotSelect = document.getElementById('time_slot');
    
    if (date) {
        fetch(`get_available_times.php?date=${date}`)
            .then(response => response.json())
            .then(data => {
                timeSlotSelect.innerHTML = '';
                timeSlotSelect.disabled = false;
                
                if (data.length > 0) {
                    data.forEach(time => {
                        const option = document.createElement('option');
                        option.value = time.time_slot;
                        option.textContent = time.time_slot + 
                                            ` (Tersedia: ${time.available_capacity}/${time.max_capacity})`;
                        timeSlotSelect.appendChild(option);
                    });
                } else {
                    const option = document.createElement('option');
                    option.textContent = 'Tidak ada jadwal tersedia untuk tanggal ini';
                    timeSlotSelect.appendChild(option);
                }
            });
    } else {
        timeSlotSelect.innerHTML = '<option value="">-- Pilih Tanggal terlebih dahulu --</option>';
        timeSlotSelect.disabled = true;
    }
});
</script>

<script>
document.getElementById('bookingDate').addEventListener('change', function() {
    const selectedDate = this.value;
    
    // Check availability
    fetch('check_availability.php?date=' + selectedDate)
        .then(response => response.json())
        .then(data => {
            if(!data.available) {
                alert('Maaf, tanggal ini sudah penuh');
                this.value = '';
            }
        });
});
</script>

    <!-- Map Section -->
    <section id="maps">
    <div class id="contact">
        <div class = "container">
            <div class= "section-title"> 
            <h2>Lokasi Kami</h2> 
            </div>
        
            <div class="map-container">
                <?php
                $map = $conn->query("SELECT * FROM maps LIMIT 1")->fetch_assoc();
                if (!$map) {
                $conn->query("INSERT INTO maps (embed_code) VALUES ('<iframe src=\"https://www.google.com/maps/embed?pb=...\" width=\"100%\" height=\"500\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\"></iframe>')");
                $map = $conn->query("SELECT * FROM maps LIMIT 1")->fetch_assoc();
             }
                ?>
                <div class="map-container">
                <?= htmlspecialchars_decode($map['embed_code']) ?>
                </div>
            </div>
        </div>
    </div>
    </section>

    <!-- Footer -->
<footer>
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Kampus Susu Bergoyang</h3>
                <p>Destinasi wisata edukasi peternakan sapi perah dengan berbagai produk olahan susu berkualitas.</p>
            </div>
            
            <div class="footer-section">
                <h3>Kontak Kami</h3>
                <p><i class="fas fa-phone"></i> +<?= substr($contact['phone'], 0, 2) . '-' . substr($contact['phone'], 2) ?></p>
                <p><i class="fas fa-envelope"></i> <?= htmlspecialchars($contact['email']) ?></p>
                <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($contact['address']) ?></p>
            </div>
            
            <div class="footer-section">
                <h3>Follow Kami</h3>
                <div class="social-links">
                    <a href="https://facebook.com/<?= $contact['facebook'] ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://instagram.com/<?= $contact['instagram'] ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a href="https://wa.me/<?= $contact['whatsapp'] ?>" target="_blank"><i class="fab fa-whatsapp"></i></a>
                    <a href="mailto:<?= $contact['email'] ?>" target="_blank"><i class="fas fa-envelope"></i></a>
                </div>
            </div>
        </div>
        
        <div class="copyright">
            &copy; <?= date('Y') ?> Kampus Susu Bergoyang. Hak Cipta Dilindungi.
        </div>
    </div>
</footer>   
    <script>
        // Form submission handling
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            // Biarkan form submit secara normal
        });
        
        document.querySelectorAll('nav a').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                
                // Skip for admin link
                if(targetId.includes('admin.php') || targetId === '#') return;
                
                e.preventDefault();
                const targetElement = document.querySelector(targetId);
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
                
                // Update active link
                document.querySelectorAll('nav a').forEach(a => a.classList.remove('active'));
                this.classList.add('active');
            });
        });
        

        window.addEventListener('scroll', function() {
            const sections = document.querySelectorAll('section, header');
            const navLinks = document.querySelectorAll('nav a');
            
            let current = '';
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                
                if(pageYOffset >= (sectionTop - 100)) {
                    current = section.getAttribute('id');
                }
            });
            
            navLinks.forEach(link => {
                link.classList.remove('active');
                if(link.getAttribute('href') === `#${current}`) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>