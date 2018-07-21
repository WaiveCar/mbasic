<?
include('api/common.php');
$me = me();
if($me['booking_id']){
  $car = car_info($me['booking']['carId']);
}
doheader('Start Booking');
?>
  <div class='box center'>
    <h1>
    Welcome to <?= $car['license'] ?>!
    </h1>
    <form enctype="multipart/form-data" method="post" action="api/carcontrol.php?action=start">
      <p>Is there any damage or issues such as trash? (upload up to 4 photos)</p>

      <div class='image-upload'>
        <input name="image0" type="file" accept="image/*;capture=camera" capture="camera" />
        <input name="image1" type="file" accept="image/*;capture=camera" capture="camera" />
        <input name="image2" type="file" accept="image/*;capture=camera" capture="camera" />
        <input name="image3" type="file" accept="image/*;capture=camera" capture="camera" />
      </div>

      <input class='btn wid-1 submit' type="submit" value="Start Ride">
    </form>

    <p align="center">
      <small><a href="api/carcontrol.php?action=end">Cancel Booking</a></small>
    </p>

  </div>

</body>
</html>
