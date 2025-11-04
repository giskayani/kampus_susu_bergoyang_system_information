<?php
include 'config.php';

$date = $_GET['date'] ?? '';
$response = [];

if ($date) {
    $sql = "SELECT s.time_slot, s.max_capacity, 
                   (s.max_capacity - COUNT(b.id)) as available_capacity
            FROM available_schedules s
            LEFT JOIN bookings b ON b.booking_date = s.date AND b.time_slot = s.time_slot
            WHERE s.date = ?
            GROUP BY s.time_slot, s.max_capacity
            HAVING available_capacity > 0";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $response[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>