<?
include('api/common.php');
getstate();
$me = me();
if($me['booking_id']){
  $car = car_info($me['booking']['carId']);
}
$started = $me['booking']['details'][0]['createdAt'];
$inBooking = strtotime('now') - strtotime($started);
$minutes = floor($inBooking/60);
$seconds = $inBooking % 60;
doheader('Current Booking');
?>
  <div class='box'>
    <h1><?= $car['license']; ?></h1>
    <? showLocation($car); ?>
    <h4>
    You have had <?= $car['license'] ?> for <b><?= $minutes ?>min</b>.
    </h4>
    <p align="center">
        <a href="api/carcontrol.php?action=finish">End Ride</a>
    </p>

    <? actionList('api/carcontrol.php', [
      ['unlock', 'Unlock', 2],
      ['lock', 'Lock', 2]
    ]); ?>
  </div>

</body>
</html>
