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
  <h1>Add Shortcuts to your Homescreen</h1>
  <div class='content'>
    <p>With a few quick taps you can lock and unlock your WaiveCar right from your homescreen on your smartphone!</p>
    <center>
    <img style='border:1px solid black;padding:4px' src=/img/buttons.png>
    </center>
    
    <p>Tap on each button below and follow the instructions to add it to your homescreen!</p>
    <p><a class='btn cta' href="Control/unlock">Unlock</a> shortcut</p>
    <p><a class='btn cta' href="Control/lock">Lock</a> shortcut</p>
  </div>
</body>
</html>
