<?
include('api/common.php');
getstate();
$me = me();
db_incrstats('dash');

if($me['booking_id']){
  $car = car_info($me['booking']['carId']);
}
$started = $me['booking']['details'][0]['createdAt'];
$inBooking = strtotime('now') - strtotime($started);
$minutes = ceil($inBooking / 60);
$hours = floor($minutes / 60);
$seconds = $inBooking % 60;
$timeStr = $minutes % 60;

if($hours > 0) {
  $timeStr = "${hours}hr. ${timeStr}";
}

$name = $car['license'];
ob_start("sanitize_output");
doheader('Current Booking', ['extraHtml' => '<meta http-equiv="refresh" content="90">']);
?>
  <div class=box>
    <? showLocation($car); ?>
    <div align=center>
      <h4><b><?= $timeStr ?>min</b> in <?= $car['license']; ?></h4>
      <a class=isolated href=control/end>Park and End Ride</a>
    </div>

    <? actionList('control', [
      ['unlock', 'Unlock', 2],
      ['lock', 'Lock', 2]
    ]); ?>
  </div>

<? infobox("Exclusive for Basic Users", "Add a shortcut on your smartphone home screen for quickly locking and unlocking WaiveCars. <a href=/shortcuts.php>Find out more!</a>", 'info'); ?>
</body>
</html>
