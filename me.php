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
      <li>Email: <?= $me['email']?> </li>
      <li>Phone: <?= $me['phone']?> </li>
      <li>Credit: <?= money($me['credit']) ?></li>
      <li>Your account is <?= $me['status'] ?>.
    </ul>

    
    <? if(!$car) { ?>
    <p>
      <a href="/showcars.php">Find Available WaiveCars</a>
    </p>
    <? } ?>

    <p>
    This is WaiveCar Basic. 
    </p>

    <p>
    We have a full-featured app:
    <ul>
    <li><a href="https://play.google.com/store/apps/details?id=com.waivecardrive.app&hl=en_US">Download for Android</a>
    <li><a href="https://itunes.apple.com/us/app/waivecar/id1051144802?mt=8">Download for iPhone</a>
    </ul>
    </p>

    <p>
    You can call or text us at <a href="tel:+18559248355">1 (855) WAIVE-55</a>
    </p>

    <a class='btn' href="api/logout.php">Logout</a>
    </div>
  </div>
</body>
</html>
