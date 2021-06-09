<?
include('api/common.php');
$me = getstate();
db_incrstats('book');

if($me['booking_id']){
  $car = car_info($me['booking']['carId']);
}

$expire = $me['booking']['reservationEnd'];
$inBooking = strtotime($expire) - strtotime('now');
$minutes = round($inBooking/60);
$absMinutes = abs($minutes);
$seconds = $inBooking % 60;
$isExtended = false;
if($me['booking']['flags']) {
  $isExtended = strpos($me['booking']['flags'], 'extended');
}

ob_start("sanitize_output");

doheader('Get to Your WaiveCar', [
  'extraHtml' =>  '<meta http-equiv=refresh content=80>'
]);
?>
  <div class='box gettocar prompt'>
    <h1>Get to your Vehicle</h1>
    <?= showLocation($car, ['nozone' => true]) ?> 
    <? if ($isExtended) {?> 
      <h4><?= $car['license'] ?> is yours!</h4>
    <? } else { ?>
      <h4><b><?= $minutes ?>min</b> left to get to <?= $car['license']; ?></h4>
    <? } ?> 
    <p>
    <? if ($isExtended) {?> 
      Your extension <?= $minutes > 0 ? "begins in ${minutes}min" : "began ${absMinutes}min ago" ?>.
    <? } else { ?>
      Need longer?<br/><a class=encourage href=control/extend?remain=<?=max(0, $minutes -1)?>>Extend your reservation</a>
    <? } ?> 
    </p>

    <div class=message>
    <? actionList('control', [
      ['cancel', 'Cancel Booking', '2 danger'],
      ['start', 'Start Ride', '2 geo']
     ]); ?>
    </div>
  </div>
  <script src=script.js></script>
</body>
</html>
