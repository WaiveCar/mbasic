<?
include('api/common.php');
getstate();
$carList = get('/cars');

$user_lat = false;
$user_lng = false;
extract($_GET, EXTR_PREFIX_ALL | EXTR_OVERWRITE, 'user_');

doheader('Find Cars');
//  <img src="<?=getMap($carList)
?>

  <div>
  <div id='sorter'>
    Sort By <span>Nearest</span> <span>Range</span> <span>Name</span>
  </div>

<? foreach($carList as  $car) { ?>
  <div class='car-row'>
    <a class='button' href="api/carcontrol.php?action=reserve&car=<?= $car['id']; ?>">Reserve</a> 
    <h2><?= $car['license']; ?></h2>
    <h3>Range: <?= $car['range']; ?> miles</h3>
    <?= location_link($car) ?>
  </div>
<? } ?>
  </div>
  <script src="js/scripts.js"></script>
</body>
</html>
