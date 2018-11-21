<?
include('api/common.php');
$me = me();

if($me['booking_id']){
  $car = car_info($me['booking']['carId']);
}

$locationList = get('/locations');
$chargerList = [];
foreach($locationList as $m) {
  if ($m['type'] === 'chargingStation') {
    $dist = distance($car, $m);
    if($dist < 7) {
      $m['dist'] = $dist;
      $chargerList[] = $m;
    }
  }
};
usort($chargerList, function($a, $b){
  return $a['dist'] > $b['dist'] ? 1 : -1;
});

$mapOpts = ['me' => $car, 'zoom' => 11];

$ix = 0;

global $labelGuide;
doheader('Charge Car');
?>

  <div class=box>
    <div class='tab-container'>
      <a class='inactive' href='inbooking.php'> &#128663; <?= $car['license'] ?></a><span> &#128268; Chargers </span>
    </div>
    <div class='map'>
      <? getMap($chargerList, $mapOpts); ?>
    </div>
  </div>
<?
infobox("Under Development","Vehicle charging on WaiveBasic currently <u>Does Not Work</u> (Nov 21, 2018).  This is a sneak preview of the interface in development. Thanks for your interest. Please check back in the next few days. <i>Note: This message will be removed when it is functional.</i>", 'error');
?>
  <ul class='car-row charge-list'>
<? if(count($chargerList) === 0) { ?>
    <li class='car-row'>
      <h1>No chargers are currently in range.</h1>
    </li>
 
<? } else {
foreach($chargerList as $key => $charger) { 
?>
  <li>
    <h3> (<?= $labelGuide[$ix] ?>) <?= $charger['name'] ?></h3> 
    <div><?= location_link($charger) ?><span class='charger-distance'>(<? printf("%.2f", $charger['dist']) ?>mi away)</span></div>
    <?
    $chargerSet = ['fast' => [], 'slow' => []];
    foreach($charger['portList'] as $port) {
      $chargerSet[$port['type']][] = $port;
    }
    foreach(['fast', 'slow'] as $type) {
      $list = $chargerSet[$type];
      echo "<div class='port-list'>";
      echo "<span>".ucfirst($type)."</span>";
      if(count($list) > 0) {
        foreach($chargerSet[$type] as $port) {
          echo "<a><img src=charger-{$port['type']}.png>${port['name']}</a>";
        }
      } else {
        echo "<span><em>None</em></span>";
      }
      echo '</div>';
    }
    echo '</li>';
    $ix++;
  } 
}
?>
  </ul>
  </div>
  <script src="script.js"></script>
</body>
</html>
