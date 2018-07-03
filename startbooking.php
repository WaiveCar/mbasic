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
    <!--
    <p>
    Please upload the following photographs before continuing
    </p>
    -->
    <form enctype="multipart/mixed" method="post" action="api/carcontrol.php?action=start">
      <!--
      <? imageList(['required' => false], [
        [ 'Driver Side', 'driver' ],
        [ 'Front', 'front' ],
        [ 'Passenger Side', 'passenger' ],
        [ 'Back', 'back' ],
      ]); ?>
      --->

      <p>Is there any damage or issues such as trash?</p>
      <? imageList(['required' => false], [[ 'Anything else', 'other' ]]) ?>

      <input class='button wid-1' type="submit" value="Start Ride">
    </form>

    <p align=center>
      <a href="api/carcontrol.php?action=finish">Cancel Booking</a>
    </p>

  </div>

</body>
</html>
