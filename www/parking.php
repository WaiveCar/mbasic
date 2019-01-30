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

for($ix = 0; $ix < count($carList); $ix++) {
  $car = $carList[$ix];
  if(!isHome($car) && aget($car, 'tagList.0.groupRoleId') == 6) {
    $bk = aget($car, 'bookings.0.details.0');
    $noPhoto[] = [
      'car' => $car['id'], 
      'latitude' => $bk['latitude'],
      'longitude' => $bk['longitude']
    ];
  }
  $carList[$ix]['parkTime'] = strtotime(aget($car, 'bookings.0.details.0.updated_at'));
  $carList[$ix]['parked'] = round( (time() - $carList[$ix]['parkTime']) / 60);
}

/*
$resList = post('/parkingQuery', ['qstr' => $noPhoto]);

$resMap = [];
foreach($resList as $res) {
  $resMap[$res['car']] = $res;
  unset($resMap[$res['car']]['car']);
}
 */
$resList = [];
$resMap = [];

usort($carList, function($a, $b) {
  return $a['parked'] - $b['parked'];
});
 
doheader("Parking", ['usecss' => false]);
echo '<link rel=stylesheet href=/parking.css?2>';

foreach($carList as $car) {

  $currentDistance = distance($car, aget($car, 'bookings.0.details.0'));
  if(aget($car, 'tagList.0.groupRoleId') !== 6 || isHome($car) || $currentDistance > 0.5) {
    continue;
  }
  $uid =  aget($car, 'bookings.0.id');
  $pd = aget($car, 'bookings.0.parkingDetails');

  $claim = aget($pd, 'streetHours');
  if($claim) {
    $claim .= 'hr';
  } else {
    $claim = aget($pd, 'userInput');
  } 

  if(!$claim && !empty($pd['expireDay'])) {
    $claim = implode(' ', [
      ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'][$pd['expireDay']],
      sprintf("%02d:00", $pd['expireHour'])
    ]);
  }
  $img = aget($car, 'bookings.0.parkingDetails.path');
  $imgClass = false;
  $guess = false;
  list($lat,$lng) = [
    aget($car, 'bookings.0.details.0.latitude'),
    aget($car, 'bookings.0.details.0.longitude')
  ];

  $endTime = $car['parked'];
  $endTime /= 60;
  
  if(floor($endTime) > 0) {
    $endTimeStr = floor($endTime) . 'h'; 
  } else {
    $endTimeStr = '<1h';
  }
?>
  <span class="car-sheet" id="booking-<?=$uid?>" data-car="<?=$car['id']?>">
<span class="img-wrap">
<? if ($img) { ?>
    <a class='img' data-orig="<?=$img?>" target=_blank href=https://s3.amazonaws.com/waivecar-prod/<?=$img ?>><img src=https://s3.amazonaws.com/waivecar-prod/<?=$img ?>></a>
<? } else { ?>
    <img class='img'/> 
<? }  ?>
  
  </span>
  <span class="info">
  <div class='car-name'><a target=_blank href=https://lb.waivecar.com/cars/<?=$car['id']?>><?=$car['license']?> <?=$car['charge']?>%</a></div>
  <div class='park-claim'>Left:  <?= dateTz('H:00 l', $car['parkTime']); ?> (<?=$endTimeStr ?>) <a style=float:right onclick='toggle("<?=$uid ?>");'>&#x1F4CC;</a></div>
    <? if ($claim) { ?>
      <div>Move: <?=$claim ?></div>
    <? } else { ?>
      <div> NO CLAIM </div>
    <? } ?>
    <!--<div class='req'>Min: <?= aget($car, 'zone.parkingTime') ?>hr</div>-->
    <div class='addrtop'><a target=_blank href="https://maps.google.com/?q=<?=$lat?>,<?=$lng?>+(<?=$car['license']?>)"><?=addrClean(aget($car, 'bookings.0.details.0.address')) ?></a></div>
    <div class=guess-wrap></div> 
    <? if (isAdmin()) { ?>
    <div class='action'> 
      <select name="cite-user">
        <option value="null">Choose one</option>
        <optgroup label="Photo">
          <option value="not-a-sign">Not a sign</option>
          <option value="incorrect">Incorrect</option>
          <option value="illegible">Illegible</option>
        </optgroup>

        <optgroup label="Parking" class="parking-options">
          <option value="lawless">Violation</option>
          <option value="bounty">Bounty</option>
        </optgroup>
      </select>
      <button onclick="penalize(this,<?=$uid?>)">Submit</button>
    </div>
    <? } ?>
  </span>
</span>
<? } ?>
<div id='template'>
  <script id='t-archive' type='text/template'>
    <div class='nav'>
      <a class='prev disabled'>Prev</a><a class='next disabled'>Next</a>
    </div>
    <div class='guess'>
      <div>
        <b class='title'></b>
        <em class='dist'></em>
        <span class='addr'></span>
      </div>
    </div>
  </script>
</div>
<script>
  var payload = <?=json_encode($resMap) ?>;
</script>
<script
  src="https://code.jquery.com/jquery-3.3.1.min.js"
  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
  crossorigin="anonymous"></script>
<script src=js/evda.js></script>
<script src=js/underscore-min.js></script>
<script src=js/parking.js?1></script>
</body>
</html>
