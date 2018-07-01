<?
include('api/common.php');
$me = me();
if($me['booking_id']){
  $car = car_info($me['booking']['carId']);
}
doheader('Start Booking');
?>
  <div class='box'>
    <h1>
    Welcome to <?= $car['license'] ?>!
    </h1>
    <p>
    Please upload the following photographs before continuing
    </p>
    <form enctype="multipart/mixed" method="post" action="api/carcontrol.php?action=start">
      <? imageList(['required' => false], [
        [ 'Driver Side', 'driver' ],
        [ 'Front', 'front' ],
        [ 'Passenger Side', 'passenger' ],
        [ 'Back', 'back' ],
      ]); ?>

      <p>Is there anything else, such as trash?</p>
      <? imageList(['required' => false], [[ 'Anything else', 'other' ]]) ?>

      <input class='button wid-1' type="submit" value="Start Ride">
    </form>

  </div>

</body>
</html>
