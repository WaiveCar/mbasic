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
  <h1>Account Information</h1>
  <div class='content'>
    <p>Hello <?=$me['firstName']?> <?=$me['lastName']?>! Here's your current account details:</p>
    <ul>
    <? if($car) { ?>
      <li>You're currently in <a href="/inbooking.php"><?= $car['license']; ?></a>.</li>
    <? } else { ?>
      <li>You're currently not in a WaiveCar. </li>
    <? } ?>
      <li>Credit: <?= money($me['credit']) ?></li>
      <li>Email: <?= $me['email']?> </li>
      <li>Phone: <?= $me['phone']?> </li>
      <li>Your account is <?= $me['status'] ?>.
    </ul>

    
    <? if(!$car) { ?>
    <p>
      <a href="/showcars.php">Find Available Go Vehicles</a>
    </p>
    <? } ?>

    <p>
    <b>GoBasic</b>
    This is GoBasic. 
    Add a shortcut on your smartphone home screen for quickly locking and unlocking Reef Go Vehicles. <a href=/fast>Find out more!</a>
    </p>

    <p>
    <b>Gotext</b>
Many things are available over text message. This requires no mobile data whatsoever. <a href="https://medium.com/@Waive/how-to-control-a-waivecar-over-text-7a1cc6176b45">Read our documentation to find out more.</a>
    <p>
<!--
    <b>Smartphones</b>
    We have a full-featured app:
    </p>
    <ul>
    <li><a href="https://play.google.com/store/apps/details?id=com.waivecardrive.app&hl=en_US">Download for Android</a>
    <li><a href="https://itunes.apple.com/us/app/waivecar/id1051144802?mt=8">Download for iPhone</a>
    </ul>
-->
    </p>

    <p>
    You can call or text us at <a href="tel:+18559248355">1 (855) WAIVE-55</a>
    </p>

    <a class='btn' href="api/logout.php">Logout</a>
    </div>
  </div>
</body>
</html>
