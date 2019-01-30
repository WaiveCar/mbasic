<?
include ('api/common.php');
$type = $_POST['type'];
$booking = $_POST['booking'];
post("/parking/cite/$type", ['bookingId' => $booking], ['raw' => true]);

