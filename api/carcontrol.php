<?
ob_start();
include('common.php');
$action = $_GET['action'];
if($action === 'nop') {
  getstate("nocache");
  exit;
}
doheader('Waiting', false);
?>
  <div class='box center'>
    <h1>Please wait</h1>
    <p>This can take up to 30 seconds.</p>
    <p>
      <img src="/img/ajax-loader.gif">
    </p>
  </div>
<?
ob_end_flush();
flush();
$me = me();
if($action === 'generic') {
  $res = $_GET['params'];
  tis(curldo($res['url'], $res['params'], $res['verb']));
}

if($action === 'reload') {
  header('Location: gettocar.php');
  exit;
}
if($action === 'reserve') {
  $res = reserve($_GET['car']);
}


if($me['booking_id']) {

  $booking = $me['booking_id'];
  $carId = $me['booking']['carId'];

  if($action === 'lock' || $action === 'unlock') {
    $action($carId);
    getstate();
    exit;
  }

  if($action === 'extend4realz') {
    extend($booking, $_GET['howmuch']);
  }
  if($action === 'extend') {
    confirm("Extend Your Reservation", "You can buy either an extra 10 or 20 minutes to get to your WaiveCar.<p><em>Reminder:</em> If you don't make it in time, you'll have to wait 30 minutes to rebook the same WaiveCar.</p>", [
      [ "$4.20 for 20 extra minutes", "/api/carcontrol.php?action=extend4realz&howmuch=20", 'wid-1 primary preferred'],
      [ "$1.00 for 10 extra minutes", "/api/carcontrol.php?action=extend4realz&howmuch=10", 'wid-1 '],
      [ "No thanks! I'll make it in time", "/api/carcontrol.php?action=nop", 'wid-1 ignored' ]
    ]);
  }

  if($action === 'cancel4realz') {
    cancel($booking);
  }

  if($action === 'cancel') {
    confirm("Cancel Your Booking", "Are you sure you want to cancel your booking?<p><em>Reminder:</em> If you cancel, you need to wait 30 minutes to rebook the same WaiveCar.</p>", [
      [ "Yes, cancel my booking.", "/api/carcontrol.php?action=cancel4realz", 'wid-1 danger'],
      [ "No, do not cancel my booking", "/api/carcontrol.php?action=nop", 'wid-1 primary' ]
    ]);
  }

  if($action === 'start') {
    $fileList = uploadFiles();
    if(count($fileList) > 0)  {
      createReport($fileList);
    }
    start($me['booking_id']);
  }

  if($action === 'end') {
    $me = me(['withcar' => true]);
    $car = $me['car']['license'];
    confirm("End Your Booking", "Are you sure you're done with $car?", [
      [ "Yes, I'm done with $car.", "/api/carcontrol.php?action=end4realz", 'wid-1'],
      [ "I'm not done. I want to keep going!", "/api/carcontrol.php?action=nop", 'wid-1 primary' ]
    ]);
  }
  if($action === 'end4realz') {
    $res = tis(put("/bookings/$booking/canend"));

    // this is not false if the user can end here.
    if($res) {

      // if we aren't a level car or if we are ending 
      // in a zone then just load the end booking
      if(!isLevel() && aget($res,'type') === 'zone') {
        load('/endbooking.php');
      } else {
        // otherwise we either are a level car or we're
        // ending in a valid place which is not a zone 
        // (thus a homebase hub or charging station)
        // and we can skip things.
        finish($me['booking_id']);
        complete($me['booking_id']);
        load('/receipt.php');
      } 
      exit;
    }
  }

  if($action === 'finish') {
    finish($me['booking_id']);
  }
  
  if($action === 'complete') {
    //var_dump($_FILES);
    $parking = uploadFiles(['parking']);
    $payload = [
      'data' => [
        // TODO: figure out why the type is needed
        'type' => true,
        'streetHours' => $_POST['hours'],
        'id' => $id,
        'streetSignImage' => $parking[0]
      ]
    ];

    $fileList = uploadFiles();

    if(count($fileList) > 0) {
      createReport($fileList);
    }

    $data = put("/bookings/$booking/end", $payload); 
    if($data) {
      // This is a special use-case
      // for getting to the receipt
      if(put("/bookings/$booking/complete")) {
        load('/receipt.php');
        exit;
      }
    }
  }
}
getstate("nocache");
