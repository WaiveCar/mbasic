<?
include('api/common.php');
$me = getstate();
db_incrstats('dash');

if($me['booking_id']){
  $car = car_info($me['booking']['carId']);
}
if(isWaiveWork()) {
  $timeStr = 'WaiveWorking';
} else {
  $timeStr = 'WaiveRushed';

  if(!hasFlag('rush')) {
    $inBooking = strtotime('now') - strtotime(aget($me, 'booking.details.0.createdAt'));
    $minutes = ceil($inBooking / 60);
    $hours = floor($minutes / 60);
    $timeStr = $minutes % 60;

    if($hours > 0) {
      $timeStr = "${hours}hr. ${timeStr}";
    }

    $timeStr .= 'min';
  }
}
ob_start("sanitize_output");
doheader('Current Booking', ['extraHtml' => '<meta http-equiv=refresh content=600>']);
?>
<div class=box>
  <div class=tab-container>
    <span>&#128663; <?= $car['license'] ?></span>
    <a class=inactive href=fuel>&#128268; Chargers</a>
  </div>
  <? showLocation($car); ?>
  <div align=center>
    <h4><b><?= $timeStr ?></b> in <?= $car['license']; ?></h4>
    <? if(!isWaiveWork()) { ?>
    <a class=isolated href=end>Park and End Ride</a>
    <? } ?>
  </div>
  <div class=content>
  <? actionList(false, [
    ['unlock', 'Unlock', 2],
    ['lock', 'Lock', 2]
  ]); ?>
  </div>
</div>
</body>
</html>
