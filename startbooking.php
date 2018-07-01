<?
include('api/common.php');
$me = me();
if($me['booking_id']){
  $car = car_info($me['booking']['carId']);
}
doheader('Start Booking');
?>
  <div>
    Welcome to <?= $car['license'] ?>!
    Please upload the following photographs before continuing
    <form enctype="multipart/mixed" method="post" action="api/booking.php?action=start">
      <? imageList([
        [ 'Driver Side', 'driver' ],
        [ 'Front', 'front' ],
        [ 'Passenger Side', 'passenger' ],
        [ 'Back', 'back' ],
        [ 'Anything else', 'other' ],
      ]); ?>

      <input type="submit" value="start ride">
    </form>

  </div>

  <script src="js/scripts.js"></script>
</body>
</html>
