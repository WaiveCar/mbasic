<?
include('api/common.php');
getstate();
$me = me();
db_incrstats('dash');

if($me['booking_id']){
  $car = car_info($me['booking']['carId']);
}
$started = $me['booking']['details'][0]['createdAt'];
if(hasFlag('rush')) {
  $timeStr = 'WaiveRushed';
} else {
  $inBooking = strtotime('now') - strtotime($started);
  $minutes = ceil($inBooking / 60);
  $hours = floor($minutes / 60);
  $seconds = $inBooking % 60;
  $timeStr = $minutes % 60;

  if($hours > 0) {
    $timeStr = "${hours}hr. ${timeStr}";
  }

  $timeStr .= 'min';
}
$name = $car['license'];
ob_start("sanitize_output");
doheader('Current Booking', ['extraHtml' => '<meta http-equiv="refresh" content="600">']);
?>
  <div class=box>
    <div class=tab-container>
      <span> &#128663; <?= $car['license'] ?></span>
      <a class=inactive href=chargeList.php> &#128268; Chargers </a>
    </div>
    <? showLocation($car); ?>
    <div align=center>
      <h4><b><?= $timeStr ?></b> in <?= $car['license']; ?></h4>
      <a class=isolated href=control/end>Park and End Ride</a>
    </div>

    <div class='content'>
    <? actionList('control', [
      ['unlock', 'Unlock', 2],
      ['lock', 'Lock', 2]
    ]); ?>
    </div>
  </div>

</body>
</html>
