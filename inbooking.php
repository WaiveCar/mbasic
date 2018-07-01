<?
include('api/common.php');
$me = me();
if($me['booking_id']){
  $car = car_info($me['booking']['carId']);
}
doheader('Current Booking');
?>
  <div>
    You have had <?= $car['license'] ?> for YYY minutes
    <? actionList('carcontrol.php', [
      ['unlock', 'Unlock'],
      ['lock', 'Lock'],
      ['end', 'End Ride']
    ]); ?>
  </div>

  <script src="js/scripts.js"></script>
</body>
</html>
