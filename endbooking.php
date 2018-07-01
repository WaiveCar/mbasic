<?
include('api/common.php');
$me = me();
if($me['booking_id']){
  $car = car_info($me['booking']['carId']);
}
doheader('End Booking');
?>
  <div class='box'>
    <h1>
    Thanks for using <?= $car['license'] ?>!
    </h1>
    <p>
    Please upload the following photographs before continuing
    </p>
    <form enctype="multipart/mixed" method="post" action="api/carcontrol.php?action=complete">
      <? imageList(['required' => false], [
        [ 'Driver Side', 'driver' ],
        [ 'Front', 'front' ],
        [ 'Passenger Side', 'passenger' ],
        [ 'Back', 'back' ],
        [ 'Parking', 'parking' ],
      ]); ?>

      <p>Any other photos, such as trash?</p>
      <? imageList(['required' => false], [[ 'Anything else', 'other' ]]) ?>

      <p>When you're finished, remove your belongings, close the doors, and tap "End Ride" below.</p>
      <input class='button wid-1' type="submit" value="End Ride">
    </form>

  </div>

  <script src="js/scripts.js"></script>
</body>
</html>
