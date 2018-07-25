<?
include('api/common.php');
getstate();
$me = me();
if($me['booking_id']){
  $car = car_info($me['booking']['carId']);
}
$started = $me['booking']['details'][0]['createdAt'];
$inBooking = strtotime('now') - strtotime($started);
$minutes = ceil($inBooking/60);
$seconds = $inBooking % 60;
doheader('Current Booking');
?>
  <div class='box'>
    <h1><?= $car['license']; ?></h1>
    <? showLocation($car); ?>
    <div align='center'>
      <h4>You have had <?= $car['license'] ?> for <b><?= $minutes ?>min</b>.</h4>
      <a class='isolated' href="control/end">End Ride</a>
      Can't find parking? <a href='control/handoff'>Try and hand-off the car to someone else!</a>
    </div>

    <? actionList('control', [
      ['unlock', 'Unlock', 2],
      ['lock', 'Lock', 2]
    ]); ?>
  </div>

</body>
</html>
