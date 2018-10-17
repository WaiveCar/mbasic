<?
include('api/common.php');
getstate();
$me = me();
$region = getTag('region', 'id');

// If a user doesn't have a region defined we can safely assume
// that they are in los angeles and then filter based on that.
if(!$region) {
  $region = 6;
}
$locationList = get('/locations');
$carList = array_filter($carList, function($m) use ($region) { 
  return $m['groupCar'][0]['groupRoleId'] === $region;
});
$arrow = ['near' => '', 'range'=> '', 'name' => '', 'show' => ''];

$mapOpts = [];
if(!empty($_GET['zip'])) {
  $_GET['sort'] = 'near';
}
if(empty($_GET['sort'])) {
  $_GET['sort'] = aget($_SESSION, 'sort', 'none');
} else {
  $_SESSION['sort'] = $_GET['sort'];
}

if(isLevel()) {
  $mapOpts['level'] = true;
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

  <div class='map'>
    <? getMap($carList, $mapOpts); ?>
    <div id='sorter'>
    <a class="needsjs" href="prompt.php?prompt=Please enter your zip code&var=zip" onclick="nearest();"><?= $arrow['near'] ?>Nearest</a> <a href="?sort=range"><?= $arrow['range'] ?>Range</a> <a href="?sort=name"><?= $arrow['name'] ?>Name</a> <a href="?sort=range&show=6"><?= $arrow['show'] ?>Best 5</a>
    </div>
  </div>

  <ul class='car-row'>
<? if(count($carList) === 0) { ?>
    <li class='car-row'>
      <h1>No WaiveCars are currently available.</h1>
    </li>
 
<? } else {
foreach($carList as $key => $car) { 
?>
  <li>
    <h3><?= ucfirst(strtolower($car['license'])); ?></h3> 
    <? if ($car['isReallyAvailable']) { ?>
    <a class='btn' href="book/<?= $car['id']; ?>">Reserve</a> 
    <? } ?>
    <div class='car-label'>
      (<?= $labelGuide[$ix] ?>) <?= round($car['range']); ?>mi charge
      <div class='fuel'><div style='width:<?=round($car['range'] * 100 / 140, 2)?>%'></div></div>
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