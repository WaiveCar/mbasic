<?
include('api/common.php');
$carList = get('cars?type=parking');
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
<? for($ix = 1; $ix < 9; $ix++) { ?>
.lvl-<?=$ix?> {
  background: rgba(255,0,0,0.<?=$ix?>)
}
<? } ?>
</style>
<?
foreach($carList as $car) {
  if(aget($car, 'tagList.0.groupRoleId') !== 6) {
    continue;
  }
  list($lat,$lng) = [
    aget($car, 'bookings.0.details.0.latitude'),
    aget($car, 'bookings.0.details.0.longitude')
  ];
  if(abs($lat - 34.0199623) + abs($lng - -118.4682465) < 0.001) {
    continue;
  }
  $level = 0;
  $claim = aget($car, 'bookings.0.parkingDetails.streetHours');
  $img = aget($car, 'bookings.0.parkingDetails.path');
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
  <span class=img>
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
  </span>
</span>
<? } ?>
  <script src=script.js></script>
</body>
</html>
