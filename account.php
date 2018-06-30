<?
include('api/common.php');
$me = me();
$car = false;
if($me['booking_id']){
  $car = car_info($me['booking']['carId']);
}
?>

Hello <?= $me['firstName'] ?> <?= $me['lastName'] ?>!
<? if($car) { ?>
  You're currently in <?= $car['license']; ?>.
<? } else { ?>
  You're currently not in any WaiveCar. 
  <a href="showcars.php">Find Available Cars</a>
</a>
<? } ?>

<p>
You are using WaiveCar Basic. 
</p>

<p>
We have an app available at the app store.
Just search for WaiveCar.
</p>

<p>
You can call or text us at <a href="tel:+18559248355">1 (855) WAIVE-55</a>
</p>

<a href="api/logout.php">Logout</a>
