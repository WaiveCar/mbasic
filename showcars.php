<?
include('api/common.php');
getstate();
$me = me();
$region = getTag('region', 'id');
$carList = get('/cars');
$carList = array_filter($carList, function($m) use ($region) { 
  return $m['groupCar'][0]['groupRoleId'] === $region;
});
$arrow = ['near' => '', 'range'=> '', 'name' => ''];
if(!empty($_GET['sort'])) {
  if($_GET['sort'] === 'range') {
    uasort($carList, function($a, $b) {
      return $b['range'] - $a['range'];
    });
    $arrow['range'] = '&#9660;';
  } else if($_GET['sort'] === 'name') {
    foreach($carList as &$car) {
      $car['number'] = intval(preg_replace('/[a-z]*/i','', $car['license']));
    }
    uasort($carList, function($a, $b) {
      return $a['number'] - $b['number'];
    });
    $arrow['name'] = '&#9660;';
  }
}

$user_lat = false;
$user_lng = false;
extract($_GET, EXTR_PREFIX_ALL | EXTR_OVERWRITE, 'user_');
$ix = 0;

global $labelGuide;
doheader('Find Cars');
?>

  <div class='box map'>
    <img src="<?=getMap($carList)?>">
    <div  id='sorter'>
    Sort By <a class="needsjs" onclick=nearest();><?= $arrow['near'] ?>Nearest</a> <a href="?sort=range"><?= $arrow['range'] ?>Range</a> <a href="?sort=name"><?= $arrow['name'] ?>Name</a>
    </div>
  </div>

<? if(count($carList) === 0) { ?>
  <div class='box'>
    <h1>Nothing is currently available.</h1>
  </div>
 
<? } else {
  foreach($carList as $car) { ?>
  <div class='car-row'>
    <a class='button' href="api/carcontrol.php?action=reserve&car=<?= $car['id']; ?>">Reserve</a> 
    <h2><?= $car['license']; ?> <small>(<?= $labelGuide[$ix] ?>)</small> </h2>
    <h3>Range: <?= $car['range']; ?> miles</h3>
    <?= location_link($car) ?>
  </div>
<? 
$ix++;
  } 
}
?>
  </div>
  <script src="js/scripts.js?<?= rand() ?>"></script>
</body>
</html>
