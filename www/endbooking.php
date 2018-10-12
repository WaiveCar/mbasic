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
 
<div class='box prompt'>
  <h1>Final Steps</h1>

  <div class='content'>
    <form enctype="multipart/form-data" method="post" action="control/complete">
      <ol>
        <li>Take a photo of the parking sign<br/><small>If there is no parking sign, take a photo of the intersection to show that no sign is posted</small>
          <p class='image-upload'>
            <input name="parking" type="file" accept="image/*;capture=camera" required capture="camera" />
          </p>
        </li>

        <li>How long is the parking valid?<br/><small>Enter "48" if longer than 2 days</small>
          <div>
            <input class="input-inline" maxlength="3" autocomplete="off" type='number' name='hours' size='1' required > hours
          </div>
        </li>

        <li>Are there any new issues?<br><small>Add up to 4 photos</small>

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
</div>

</body>
</html>
