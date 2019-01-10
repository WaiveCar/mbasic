<?
include('api/common.php');
$me = getstate();
//var_dump($me);
db_incrstats('car');

$showdibs = false;
$carList = get('/cars');

$arrow = ['near' => '', 'range'=> '', 'name' => '', 'show' => ''];

$mapOpts = [];
if(!empty($_GET['zip'])) {
  $_GET['sort'] = 'near';
}
if(empty($_GET['sort'])) {
  $_GET['sort'] = aget($_SESSION, 'sort', 'range');
} else {
  $_SESSION['sort'] = $_GET['sort'];
}

if(isLevel()) {
  $mapOpts['level'] = true;
}
if($_GET['sort'] === 'range') {
  uasort($carList, function($a, $b) {
    return $b['range'] > $a['range'] ? 1 : -1;
  });
  $arrow['range'] = '&#9660;';
} else if($_GET['sort'] === 'name') {
  foreach($carList as $key => $car) {
    preg_match('/\d*$/', $car['license'],$match);
    $carList[$key]['number'] = intval($match[0]);
  }
  uasort($carList, function($a, $b) {
    return $a['number'] >  $b['number'] ? 1 : -1;
  });
  $arrow['name'] = '&#9660;';
} else if ($_GET['sort'] === 'near' || !empty($_GET['zip'])) {

  if(!empty($_GET['zip'])) {
    $loc = zip2geo($_GET['zip']);

    $lat = $loc['lat'];
    $lng = $loc['lng'];
  } else if (!empty($_GET['lat'])) {
    $lat = $_GET['lat'];
    $lng = $_GET['lng'];
  } else {
    $lat = $_SESSION['lat'];
    $lng = $_SESSION['lng'];
  }

  $_SESSION['lat'] = $lat;
  $_SESSION['lng'] = $lng;
  $showdibs = true;

  foreach($carList as $key => $car) {
    $carList[$key]['dist'] = distance($lat, $lng, $car['latitude'], $car['longitude']);
    if($carList[$key]['dist'] < 0.5) {
      $showdibs = false;
    }
  }
  usort($carList, function($a, $b){
    return $a['dist'] > $b['dist'] ? 1 : -1;
  });
  $mapOpts['me'] = ['latitude' => $lat, 'longitude' => $lng];
  $arrow['near'] = '&#9660;';
}

if(!empty($_GET['show'])) {
  $carList = array_slice($carList, 0, intval($_GET['show'])); 
  unset($arrow['range']);
  $arrow['show'] = '&#9660;';
}
$ix = 0;

global $labelGuide;
ob_start("sanitize_output");
doheader('Find Cars');
?>

  <div class=map>
    <? getMap($carList, $mapOpts); ?>
    <div id=sorter>
    <a class=needsjs href=getzip.php onclick=nearest()><?= $arrow['near'] ?>Nearest</a> <a href=?sort=range><?= $arrow['range'] ?>Range</a> <a href=?sort=name><?= $arrow['name'] ?>Name</a> <a href=?sort=range&show=6><?= $arrow['show'] ?>Best 5</a>
    </div>
  </div>
  <ul class=car-row>
<? if(count($carList) === 0) { ?>
    <li class=car-row>
      <h1>No WaiveCars are currently available.</h1>
    </li>
 
<? } else {

foreach($carList as $key => $car) { 
?>
  <li>
    <h3><?= ucfirst(strtolower($car['license'])); ?></h3> 
    <? if ($car['isReallyAvailable']) { ?>
      <a class=btn href=book/<?= $car['id']; ?>>Reserve</a> 
    <? } ?>
    <div class=car-label>
      (<?= $labelGuide[$ix] ?>) <?= round($car['range']); ?>mi left
      <p><b style=width:<?=round($car['range'] * 100 / 140, 2)?>%></b></p>
    </div> 
    <? if (!empty($car['dist'])) { ?>
      <div class=car-distance><? printf("%.2f", $car['dist']) ?>mi away</div>
    <? } 
    echo "<div>" . location_link($car) . "</div></li>"; 
    $ix++;
  } 
}
?>
  </ul>
  <script language=javascript type=text/javascript src=script.js></script>
</body>
</html>
