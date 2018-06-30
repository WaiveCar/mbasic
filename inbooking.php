<?
include('api/common.php');
$me = me();
if($me['booking_id']){
  $car = car_info($me['booking']['carId']);
}
?>
<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>Current Booking</title>
  <link rel="stylesheet" href="css/styles.css">

</head>

<body>
  <? showerror(); ?>
  <div>
    You have had <?= $car['license'] ?> for YYY minutes
    <? actionList('carcontrol.php', [
      ['unlock', 'Unlock'],
      ['lock', 'Lock'],
      ['end', 'End Ride']
    ]); ?>
  </div>

  <script src="js/scripts.js"></script>
</body>
</html>
