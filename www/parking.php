<?
include('api/common.php');

function isHome($car) {
  list($lat,$lng) = [
    aget($car, 'bookings.0.details.0.latitude'),
    aget($car, 'bookings.0.details.0.longitude')
  ];
  return (abs($lat - 34.0199623) + abs($lng - -118.4682465) < 0.001);
}

$carList = get('cars?type=parking');
$noPhoto = [];

foreach($carList as $car) {
  if(!aget($car, 'bookings.0.parkingDetails.path') && !isHome($car)) {
    $bk = aget($car, 'bookings.0.details.0');
    $noPhoto[] = [
      'car' => $car['id'], 
      'latitude' => $bk['latitude'],
      'longitude' => $bk['longitude']
    ];
  }
}

$resList = post('/parkingQuery', ['qstr' => $noPhoto]);
$resMap = [];
foreach($resList as $res) {
  $resMap[$res['car']] = $res;
}

?>
<style>
span {
  display: inline-block;
}
.car-name {
  font-size: 18px;
}
.car-sheet {
  width: 380px;
  padding: 10px;
  margin: 5px;
  box-shadow: 0 0 2px grey;
  height: 240px;
  vertical-align: top;
}
.info {
  font-size: 12px;
  width: 50%;
}
.img {
  width: 48%;
  overflow: hidden;
  height: 100%;
  vertical-align: top;
}
.info {
  padding-left: 2%;
}
img {
  width: 100%;
  vertical-align: top;
}
body {
  font-size: 0;
  max-width: auto
}
.park-claim {
  font-size: 15px;
}
.addr {
  margin-top: .25rem;
}
.guess {
  margin-top: 2rem;
  background: powderblue;
}
.guess div {
  padding: 0.25rem 0.5rem;
}
.guess h4 {
  text-indent: 0.5rem;
  margin: -.25rem 0 .15rem;
  background: #3EAFBE;
  color: white;
  padding: 0.25rem 0;
  font-family: sans-serif;
  font-weight: 400;
}
<? for($ix = 1; $ix < 9; $ix++) { ?>
.lvl-<?=$ix?> {
  background: rgba(255,150,160,0.<?=$ix?>);
  font-weight: <?=$ix?>00;
}
<? } ?>
</style>
<?
foreach($carList as $car) {
  if(aget($car, 'tagList.0.groupRoleId') !== 6 || isHome($car)) {
    continue;
  }
  $level = 0;
  $claim = aget($car, 'bookings.0.parkingDetails.streetHours');
  $img = aget($car, 'bookings.0.parkingDetails.path');
  $imgClass = false;
  $guess = false;
  if(!$img) {
    $path = aget($resMap, $car['id'] . ".results.0.path");
    if($path) {
      $guess = $resMap[$car['id']];
      $parts = explode('T', aget($guess, 'results.0.created_at') );
      $guess['date'] = $parts[0];
      $img = $path;
    }
  }

  $endTime = round( ( time() - strtotime(aget($car, 'bookings.0.details.0.updated_at')) ) / 60);
  $endTimeStr = ($endTime % 60) . 'm';
  $endTime /= 60;
  if($endTime > 3) {
    $level ++;
    if($endTime > 12) {
      $level ++;
    }
    if($claim && $endTime > $claim) {
      $level += 5;
    }
  }
  
  if(floor($endTime) > 0) {
    $endTimeStr = floor($endTime) . 'h ' . $endTimeStr;
  }
?>
<span class="car-sheet lvl-<?=$level?>">
<span class="img">
<? if ($img) { ?>
    <img src=https://s3.amazonaws.com/waivecar-prod/<?=$img ?>>
<? } ?>
  </span>
  <span class=info>
  <div class=car-name><?=$car['license']?> (<?=$car['charge']?>%)</div>
    <div class='park-claim'> Parked for: <?=$endTimeStr ?></div>
    <? if ($claim) { ?>
      <div> Good for: <?=$claim ?>hr</div>
    <? } else { ?>
      <div> NO CLAIM </div>
    <? } ?>
    <div class='addr'> <?=aget($car, 'bookings.0.details.0.address') ?></div>
    <? if ($guess) { ?>
    <div class=guess>
      <h4>Archival Photo</h4>
      <div>
      <?= $guess['date'] ?>
      </div>
    </div> 
    <? } ?>
  </span>
</span>
<? } ?>
  <script src=script.js></script>
</body>
</html>
