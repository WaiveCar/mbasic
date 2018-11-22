<?
include('api/common.php');
getstate();
$me = me();
db_incrstats('book');

if($me['booking_id']){
  $car = car_info($me['booking']['carId']);
}

$expire = $me['booking']['reservationEnd'];
$inBooking = strtotime($expire) - strtotime('now');
$minutes = round($inBooking/60);
$seconds = $inBooking % 60;
$isExtended = false;
if($me['booking']['flags']) {
  $isExtended = strpos($me['booking']['flags'], 'extended');
}

ob_start("sanitize_output");

doheader('Get to Your WaiveCar', [
  'extraHtml' =>  '<meta http-equiv=refresh content=30>'
]);
?>
  <div class='box gettocar'>
    <?= showLocation($car, ['nozone' => true]) ?> 
    <? if ($isExtended) {?> 
      <h4><?= $car['license'] ?> is yours!</h4>
    <? } else { ?>
      <h4><b><?= $minutes ?>min</b> left to get to <?= $car['license']; ?></h4>
    <? } ?> 
    <p align=center>
    <? if ($isExtended) {?> 
      Your extension <?= $minutes > 0 ? "begins in ${minutes}min" : "began ${minutes} ago" ?>.
    <? } else { ?>
      Need longer? <a href="control/extend">Extend your reservation</a>.
    <? } ?> 
    </p>

    <? actionList('control', [
      ['cancel', 'Cancel Booking', '2 danger'],
      ['start', 'Start Ride', 2]
    ]); ?>
  </div>

</body>
</html>
