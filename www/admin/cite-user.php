<?
include ($_SERVER['DOCUMENT_ROOT'] . '/api/common.php');

$type = $_POST['type'];
$booking = $_POST['booking'];
post("/parking/cite/$type", ['bookingId' => $booking], ['raw' => true]);

