<?
include('api/common.php');
$me = me();

if($me['booking_id']){
  $car = car_info($me['booking']['carId']);
}

$locationList = get('/locations');
$chargerList = array_filter($locationList, function($m) use ($region) { 
  return $m['type'] === 'chargingStation';
});

$mapOpts = ['me' => $car, 'zoom' => 11];

$ix = 0;

global $labelGuide;
doheader('Charge Car');
?>

  <div class=box>
    <div class='tab-container'>
      <a class='inactive' href='inbooking.php'> &#128663; <?= $car['license'] ?></a>
      <span> &#128268; Chargers </span>
    </div>
  <div class='map'>
    <? getMap($chargerList, $mapOpts); ?>
  </div>
  </div>

  <ul class='car-row'>
<? if(count($chargerList) === 0) { ?>
    <li class='car-row'>
      <h1>No chargers are currently in range.</h1>
    </li>
 
<? } else {
foreach($chargerList as $key => $car) { 
?>
  <li>
    <h3> (<?= $labelGuide[$ix] ?>) <?= ucfirst(strtolower($car['name'])); ?></h3> 
    <div class='car-label'>
    </div> 
    <? if (!empty($car['dist'])) { ?>
      <div class='car-distance'><? printf("%.2f", $car['dist']) ?>mi away</div>
    <? } 
    echo "<div>" . location_link($car) . "</div></li>"; 
    $ix++;
  } 
}
?>
  </ul>
  </div>
  <script src="script.js"></script>
</body>
</html>
