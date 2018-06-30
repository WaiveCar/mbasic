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

  <title>Get to Your WaiveCar</title>
  <link rel="stylesheet" href="css/styles.css">

</head>

<body>
  <? showerror(); ?>
  <div>
    You have XXX more minutes to get to <?= $car['license'] ?>.
    It's at <a href="https://www.google.com/maps/search/?api=1&query=<?= $car['latitude'] ?>,<?= $car['longitude'] ?>"><?= location($car) ?></a> (click to open in maps)

    <? actionList('api/carcontrol.php', [
      ['reload', 'Update'],
      ['extend', 'Extend Reservation 10 additional minutes for $1.00'],
      ['cancel', 'Cancel Booking'],
      ['start', 'Unlock Car and Start Ride']
    ]); ?>
  </div>

  <script src="js/scripts.js"></script>
</body>
</html>
