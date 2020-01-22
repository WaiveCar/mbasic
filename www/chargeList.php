<?
include('api/common.php');
$me = me();


if($me['booking_id']){
  $car = car_info($me['booking']['carId']);
}
$reference = false;
if(isset($_GET['latitude'])) {
  $reference = $_GET;
} else {
  $reference = $car;
}

$locationList = get('/locations');
$chargerList = [];
foreach($locationList as $m) {
  if ($m['type'] === 'chargingStation') {
    $dist = distance($reference, $m);
    if($dist < 7) {

      $m['dist'] = $dist;
      $m['fast'] = 0;
      $m['slow'] = 0;

      foreach($m['portList'] as $port) {
        $m[$port['type']]++;
      }
      if($m['fast'] > 1) {
        $m['color'] = 'green';
      } else if($m['fast'] > 0) {
        $m['color'] = 'brown';
      } else { 
        $m['color'] = 'gray';
      }
      $chargerList[] = $m;
    }
  }
};

usort($chargerList, function($a, $b){
  return $a['dist'] - ($a['fast'] * 2) > $b['dist'] - ($b['fast'] * 2) ? 1 : -1;
});

$mapOpts = ['me' => $reference, 'zoom' => 12];

$ix = 0;

global $labelGuide;
ob_start("sanitize_output");
doheader('Charge Car');
?>

<div class=box>
  <div class=tab-container>
    <a class=inactive href=inbooking.php> &#128663; <?= $car['license'] ?></a><span> &#128268; Chargers </span>
  </div>
  <div class=map>
    <? getMap($chargerList, $mapOpts); ?>
  </div>
</div>
<?
  echo "<ul class='car-row fuel-list'>";
if(count($chargerList) === 0) { 
  echo '<li><h1>No chargers are currently in range.</h1></li>';
} else {
  foreach($chargerList as $key => $charger) { 
?>
  <li>
    <h3><?= $charger['name'] ?></h3> 
    <a class=btn href=s/<?= $charger['id'] ?>>Charge</a> 
      <div>(<?= $labelGuide[$ix] ?>) <?= location_link($charger) ?><span class=fuel-dist><? printf("%.2f", $charger['dist']) ?>mi away</span></div>
    <?
    foreach(['fast', 'slow'] as $type) {
      $len = $charger[$type];
      if($len) {
        $klass = "'$type zero'";
      }
      echo "<span class=$klass>$len $type</span>";
    }
    echo '</li>';
    $ix++;
  } 
}
?>
  </ul>
</div>
<script src=script.js></script>
<script src=charge.js></script>
</body>
</html>
