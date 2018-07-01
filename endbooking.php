<?
include('api/common.php');
$me = me();
if($me['booking_id']){
  $car = car_info($me['booking']['carId']);
}
doheader('End Booking');
?>
  <div>
    Thanks for using <?= $car['license'] ?>!
    Please upload the following photographs before continuing
    <form enctype="multipart/mixed" method="post" action="api/booking.php?action=end">
      <? imageList([
        [ 'Driver Side', 'driver' ],
        [ 'Front', 'front' ],
        [ 'Passenger Side', 'passenger' ],
        [ 'Back', 'back' ],
        [ 'Parking', 'parking' ],
        [ 'Anything else', 'other' ],
      ]); ?>

      <input type="submit" value="end ride">
    </form>

  </div>

  <script src="js/scripts.js"></script>
</body>
</html>
