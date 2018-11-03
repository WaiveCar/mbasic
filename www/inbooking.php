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
doheader('Current Booking', true, '<meta http-equiv="refresh" content="90">');
?>
  <div class=box>
    <h1><?= $car['license']; ?></h1>
    <? showLocation($car); ?>
    <div align=center>
      <h4>You started <b><?= $timeStr ?>min</b> ago.</h4>
      <a class=isolated href=control/end>Park and end ride</a>
    </div>

    <? actionList('control', [
      ['unlock', 'Unlock', 2],
      ['lock', 'Lock', 2]
    ]); ?>
  </div>

</body>
</html>
