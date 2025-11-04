<?php
session_start();
if (!isset($_SESSION['booking_success'])) {
    header("Location: tes.php");
    exit();
}

$booking = $_SESSION['booking_data'];
unset($_SESSION['booking_success']);
unset($_SESSION['booking_data']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Booking Berhasil</title>
    <!-- Include CSS yang sama -->
</head>
<body>
    <div class="container">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h2>Booking Berhasil!</h2>
        <p>Detail Booking:</p>
        <ul>
            <li>Nama: <?= htmlspecialchars($booking['name']) ?></li>
            <li>Tanggal: <?= date('d F Y', strtotime($booking['date'])) ?></li>
            <li>Waktu: <?= htmlspecialchars($booking['time']) ?></li>
        </ul>
        <p>Kami akan menghubungi Anda untuk konfirmasi lebih lanjut.</p>
        <a href="tes.php" class="btn">Kembali ke Beranda</a>
    </div>
</body>
</html>