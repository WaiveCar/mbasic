<?
include('api/common.php');
getstate();

$me = me();
if($me['booking_id']){
  $car = car_info($me['booking']['carId']);
}

$expire = $me['booking']['reservationEnd'];
$inBooking = strtotime($expire) - strtotime('now');
$minutes = floor($inBooking/60);
$seconds = $inBooking % 60;
$isExtended = false;
if($me['booking']['flags']) {
  $isExtended = strpos($me['booking']['flags'], 'extended');
}

doheader('Get to Your WaiveCar');
?>
  <div class='box gettocar'>
  <h1><?= $car['license']; ?></h1>
    <?= showLocation($car) ?> 
    <h4>
    You have <b><?= $minutes ?>min</b> to get to <?= $car['license']; ?>
    </h4>
    <p align='center'>
    <? if ($isExtended) {?> 
      Reservation Extended
    <? } else { ?>
      <a href="api/carcontrol.php?action=extend">Need more time? Extend your reservation</a>
    <? } ?> 
    </p>

    <? actionList('api/carcontrol.php', [
      ['cancel', 'Cancel Booking', '2 danger'],
      ['start', 'Start Ride', 2]
    ]); ?>
  </div>

</body>
</html>
