<?php
session_start(); // Tambahkan ini di awal
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $date = $conn->real_escape_string($_POST['date']);
    $time_slot = $conn->real_escape_string($_POST['time_slot']);

    // Cek kapasitas tersedia
    $check_capacity = $conn->prepare("
        SELECT s.max_capacity, 
               (s.max_capacity - COUNT(b.id)) as available 
        FROM available_schedules s
        LEFT JOIN bookings b ON b.booking_date = s.date AND b.time_slot = s.time_slot
        WHERE s.date = ? AND s.time_slot = ?
        GROUP BY s.max_capacity
    ");
    $check_capacity->bind_param("ss", $date, $time_slot);
    $check_capacity->execute();
    $capacity_result = $check_capacity->get_result();
    
    if ($capacity_result->num_rows == 0) {
        $_SESSION['booking_notification'] = [
            'type' => 'error',
            'message' => 'Jadwal tidak valid atau tidak ditemukan'
        ];
        header("Location: tes.php#schedule");
        exit();
    }

    $capacity = $capacity_result->fetch_assoc();

    if ($capacity['available'] > 0) {
        $stmt = $conn->prepare("INSERT INTO bookings (customer_name, phone, booking_date, time_slot) 
                               VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $phone, $date, $time_slot);
        
        if ($stmt->execute()) {
            $_SESSION['booking_notification'] = [
                'type' => 'success',
                'message' => 'Booking Berhasil! Kami akan menghubungi Anda untuk konfirmasi',
                'details' => [
                    'name' => $name,
                    'date' => date('d F Y', strtotime($date)),
                    'time' => $time_slot
                ]
            ];
            header("Location: tes.php#schedule");
        } else {
            $_SESSION['booking_notification'] = [
                'type' => 'error',
                'message' => 'Gagal melakukan booking: ' . $stmt->error
            ];
            header("Location: tes.php#schedule");
        }
        exit();
    } else {
        $_SESSION['booking_notification'] = [
            'type' => 'error',
            'message' => 'Maaf, slot waktu ini sudah penuh'
        ];
        header("Location: tes.php#schedule");
        exit();
    }
}

// Jika akses langsung ke booking.php
header("Location: tes.php");
exit();
?>