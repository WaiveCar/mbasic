<?
include('api/common.php');
$me = me();
$car = false;
if($me['booking_id']){
  $car = car_info($me['booking']['carId']);
}
doheader('Your Account');
?>

<div class='box prompt account'>
  <h1>Add shortcuts to your homescreen</h1>
  <div class='content'>
    <p>With a few quick taps you can lock and unlock your WaiveCar right from your homescreen on your smartphone!</p>
    <p>Tap on each one and follow the instructions for each below and find out more!</p>
    <p><a class='btn cta' href="Control/unlock">Unlock</a> shortcut</p>
    <p><a class='btn cta' href="Control/lock">Lock</a> shortcut</p>
  </div>
</body>
</html>
