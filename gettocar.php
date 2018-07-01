<?
include('api/common.php');
getstate();

$me = me();
if($me['booking_id']){
  $car = car_info($me['booking']['carId']);
}

doheader('Get to Your WaiveCar');
?>
  <div class='box'>
    <img class='map' src="<?=getMap([$car])?>">
    <?= location_link($car) ?> 
    You have XXX more minutes to get to <?= $car['license']; ?>.

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
