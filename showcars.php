<?
include('api/common.php');
getstate();
$me = me();
$region = getTag('region', 'id');
$carList = get('/cars');
$carList = array_filter($carList, function($m) use ($region) { 
  return $m['groupCar'][0]['groupRoleId'] === $region;
});
$arrow = ['near' => '', 'range'=> '', 'name' => '', 'show' => ''];
$mapOpts = [];
if(!empty($_GET['sort'])) {
  if($_GET['sort'] === 'range') {
    uasort($carList, function($a, $b) {
      return $b['range'] > $a['range'] ? 1 : -1;
    });
    $arrow['range'] = '&#9660;';
  } else if($_GET['sort'] === 'name') {
    foreach($carList as &$car) {
      $car['number'] = intval(preg_replace('/[a-z]*/i','', $car['license']));
    }
    uasort($carList, function($a, $b) {
      return $a['number'] >  $b['number'] ? 1 : -1;
    });
    $arrow['name'] = '&#9660;';
  } else if ($_GET['sort'] === 'near') {
    if(empty($_GET['lat'])) {
      $loc = zip2geo($_GET['zip']);
      $lat = $loc['lat'];
      $lng = $loc['lng'];
    } else {
      $lat = $_GET['lat'];
      $lng = $_GET['lng'];
    }
    foreach($carList as $key => $car) {
      $carList[$key]['dist'] = distance($lat, $lng, $car['latitude'], $car['longitude']);
    }
    usort($carList, function($a, $b){
      return $a['dist'] > $b['dist'] ? 1 : -1;
    });
    $mapOpts['me'] = ['latitude' => $lat, 'longitude' => $lng];
    $arrow['near'] = '&#9660;';
  }
}

if(!empty($_GET['show'])) {
  $carList = array_slice($carList, 0, intval($_GET['show'])); 
  unset($arrow['range']);
  $arrow['show'] = '&#9660;';
}

$ix = 0;

global $labelGuide;
doheader('Find Cars');
?>

  <div class='box map'>
    <img src="<?=getMap($carList, $mapOpts)?>">
    <div  id='sorter'>
    <a class="needsjs" onclick=nearest();><?= $arrow['near'] ?>Nearest</a> <a href="?sort=range"><?= $arrow['range'] ?>Range</a> <a href="?sort=name"><?= $arrow['name'] ?>Name</a> <a href="?sort=range&show=5"><?= $arrow['show'] ?>Best 5</a>
    </div>
  </div>

<? if(count($carList) === 0) { ?>
  <div class='box'>
    <h1>Nothing is currently available.</h1>
  </div>
 
<? } else {
foreach($carList as $key => $car) { 
?>
    <div class='car-row' text=<?=$ix?>>
    <a class='button' href="api/carcontrol.php?action=reserve&car=<?= $car['id']; ?>">Reserve</a> 
    <h2><?= $car['license']; ?> <small>(<?= $labelGuide[$ix] ?><? if (!empty($car['dist'])) { 
        echo ': ' . round($car['dist'], 2) . ' miles away';
    } ?>)</small> </h2>
    <h3>Range: <?= $car['range']; ?> miles</h3>
    <?= location_link($car) ?>
  </div>
<? 
$ix++;
  } 
}
?>
  </div>
  <script src="js/scripts.js"></script>
</body>
</html>
