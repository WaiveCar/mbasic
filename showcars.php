<?
include('api/common.php');
getstate();
$carList = get('/cars');

$user_lat = false;
$user_lng = false;
extract($_GET, EXTR_PREFIX_ALL | EXTR_OVERWRITE, 'user_');

?>
<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>Find Cars</title>
  <link rel="stylesheet" href="css/styles.css">

</head>

<body>
  <? showerror(); ?>
  <div>
<? foreach($carList as  $car) { ?>
  <div class='car-row'>
    <h2><?= $car['license']; ?></h2>
    <h3><?= $car['range']; ?> miles</h3>
    <a href="https://www.google.com/maps/search/?api=1&query=<?= $car['latitude'] ?>,<?= $car['longitude'] ?>"><?= location($car) ?></a>
    <a href="api/carcontrol.php?action=reserve&car=<?= $car['id']; ?>">Reserve it</a>
  </div>
<? } ?>
  </div>
  <script src="js/scripts.js"></script>
</body>
</html>
