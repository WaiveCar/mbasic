<?
include('api/common.php');

function addrClean($str) {
  return preg_replace('/, (USA|CA)/','',$str);
}
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
<!doctype html><html><head><title>Parking</title><meta name=viewport content="width=device-width,initial-scale=1.0">
<style>
<? for($ix = 1; $ix < 9; $ix++) { ?>
.lvl-<?=$ix?> {
  background: rgba(255,150,160,0.<?=$ix?>);
  font-weight: <?=$ix?>00;
}
<? } ?>
</style>
<link rel=stylesheet href=/parking.css?2>
<?
foreach($carList as $car) {
  $currentDistance = distance($car, aget($car, 'bookings.0.details.0'));
  if(aget($car, 'tagList.0.groupRoleId') !== 6 || isHome($car) || $currentDistance > 0.5) {
    continue;
  }
  $level = 0;
  $uid =  aget($car, 'bookings.0.id');
  $claim = aget($car, 'bookings.0.parkingDetails.streetHours');
  $img = aget($car, 'bookings.0.parkingDetails.path');
  $imgClass = false;
  $guess = false;
  list($lat,$lng) = [
    aget($car, 'bookings.0.details.0.latitude'),
    aget($car, 'bookings.0.details.0.longitude')
  ];
  if(!$img) {
    $path = aget($resMap, $car['id'] . ".results.0.path");
    if($path) {
      $guess = $resMap[$car['id']];
      $parts = explode('T', aget($guess, 'results.0.created_at') );
      $guess['date'] = $parts[0];
      $guess['place'] = '<em>~' . round(aget($guess, 'results.0.dist') * 100, 3) . ' miles away</em>';
      $guess['place'] .= addrClean(aget($guess, 'results.0.address'));
      $img = $path;
    }
  }

  $endTime = round( ( time() - strtotime(aget($car, 'bookings.0.details.0.updated_at')) ) / 60);
  $endTimeStr = ($endTime % 60) . 'm';
  $endTime /= 60;
  if($endTime > 4) {
    $level ++;
    if($endTime > 7) {
      $level ++;
    }
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
  <span class="car-sheet lvl-<?=$level?>" id="booking-<?=$uid?>">
<span class="img">
<? if ($img) { ?>
    <a target=_blank href=https://s3.amazonaws.com/waivecar-prod/<?=$img ?>><img src=https://s3.amazonaws.com/waivecar-prod/<?=$img ?>></a>
<? } else { ?>
    <img/> 
<? }  ?>
  
  </span>
  <span class="info">
  <div class=car-name><a target=_blank href=https://lb.waivecar.com/cars/<?=$car['id']?>><?=$car['license']?> <?=$car['charge']?>%</a></div>
  <div class='park-claim'>Parked: <?=$endTimeStr ?> <a style=float:right onclick='toggle("<?=$uid ?>");'>&#x1F4CC;</a></div>
    <? if ($claim) { ?>
      <div> Good for: <?=$claim ?>hr</div>
    <? } else { ?>
      <div> NO CLAIM </div>
    <? } ?>
    <div class='addr'><a target=_blank href="https://maps.google.com/?q=<?=$lat?>,<?=$lng?>+(<?=$car['license']?>)"><?=addrClean(aget($car, 'bookings.0.details.0.address')) ?></a></div>
    <? if ($guess) { ?>
    <div class=guess>
    <h4>Archival <?= $guess['date'] ?></h4>
      <div>
      <?= $guess['place'] ?>
      </div>
    </div> 
    <? } ?>
  </span>
</span>
<? } ?>
</body>
<script src=parking.js?1></script>
</html>
