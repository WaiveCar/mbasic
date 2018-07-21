<?
include('api/common.php');
$me = me();
if($me['booking_id']){
  $car = car_info($me['booking']['carId']);
} else {
  load('/showcars.php');
}
doheader('End Booking');
?>
 
<div class='box'>
  <h1> Thanks for using <?= $car['license'] ?> </h1>

  <form enctype="multipart/form-data" method="post" action="api/carcontrol.php?action=complete">
    <ol>
      <li>Please upload a photo of the parking sign
        <p class='image-upload'>
          <input name="parking" type="file" accept="image/*;capture=camera" required capture="camera" />
        </p>
      </li>

      <li>How long is the parking valid? <small>(Enter 48 if longer than 2 days)</small>
        <p>
          <input type='text' name='hours' size='1' required > hours
        </p>
      </li>

      <li>Are there any new issues? <small>(add up to 4 photos)</small>

        <div class='image-upload'>
          <input name="image0" type="file" accept="image/*;capture=camera" capture="camera" />
          <input name="image1" type="file" accept="image/*;capture=camera" capture="camera" />
          <input name="image2" type="file" accept="image/*;capture=camera" capture="camera" />
          <input name="image3" type="file" accept="image/*;capture=camera" capture="camera" />
        </div>
      </li>

    </ol>

    <p>When finished, remove your belongings, close the doors, and tap "End Ride"</p>

    <input class='btn wid-1' type="submit" value="End Ride">
  </form>

</div>

</body>
</html>
