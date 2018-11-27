<?
ob_start();
include('common.php');
$action = $_GET['action'];
if($action === 'nop') {
  getstate("nocache");
  exit;
}
if(strpos($_SERVER['HTTP_REFERER'], 'shortcut') !== false) {
  instructions($action);
  exit;
}

doheader(ucfirst($action), ['showaccount' => false]);

$me = me();
if(($action === 'lock' || $action === 'unlock') && !$me['booking_id']) {
  infobox("No Active Booking", "Cannot $action because there's no active booking. <div class=btn-group><a href='/showcars.php' class='btn cta'>Find WaiveCars</a></div>", 'error');
  exit;
}

$title = '';
if($action === 'lock' || $action === 'unlock') {
  $title = ucfirst($action) . "ing. ";
}
$title .= "Please wait...";
infobox($title, [
  "This can take up to 30 seconds.",
  '<img id=ajax src=/img/ajax-loader.gif>'
], 'prompt center');
ob_end_flush();
flush();
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
    $remain = aget($_GET, 'remain');
    if($remin == '0') {
      $remain = 'a few seconds!';
    } else {
      $remain = "$remain minutes";
    }
    
    confirm("Extend Your Reservation", "<b>Take as long as you want!</b> $1.00 for the first 10 minutes and $0.30/min after that until you get to your WaiveCar and start the ride.", [
      [ "Save the WaiveCar for me!", "/api/carcontrol.php?action=extend4realz&howmuch=-1", 'wid-1 primary'],
      [ "Nah! I'll make it in $remain", "control/nop", 'wid-1 ignore' ]
    ]);
  }

  if($action === 'cancel4realz') {
    cancel($booking);
  }

  if($action === 'cancel') {
    confirm("Cancel Your Booking", "Are you sure you want to cancel your booking?<p><em>Reminder:</em> If you cancel, you need to wait 30 minutes to rebook the same WaiveCar.</p>", [
      [ "Yes, cancel my booking.", "control/cancel4realz", 'wid-1 danger'],
      [ "No, do not cancel my booking", "control/nop", 'wid-1 primary' ]
    ]);
  }

  if($action === 'start') {
    $me = me(['withcar' => true]);
    $distance = distance($_REQUEST, $me['car']);

    if($distance === false || $distance > 100) {
      $car = $me['car'];
      $plate = aget($me, 'car.plateNumber');
      $success = false;
      $append = '';
      if($plate && !empty($_REQUEST['plate'])) {
        $guess = $_REQUEST['plate'];
        $append = "<p>Hrmm, '$guess' is not correct. Did you make typo?</p>";
        if(strpos(strtolower($plate), strtolower($_REQUEST['plate'])) != false) {
          $success = true;
        }
      }
      if($plate && !$success) {
        prompt(
          "Looking for ${car['license']}", 
          "${append}We can't determine your device's location. Please enter in the last 3 digits of ${car['license']}'s license plate for confirmation.","plate",
          "/gettocar.php"
        );
      }
    } 
    // We permit starting under two conditions, the first is if the person is within 100M from the car and
    // the second is if they know the last 3 digits of the license plate
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
      [ "Yes, I'm done with $car.", "control/end4realz", 'wid-1'],
      [ "I'm not done. I want to keep going!", "control/nop", 'wid-1 primary' ]
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
        $res = finish($me['booking_id']);
        if(tis(complete($me['booking_id']))) {
          load('/receipt.php');
        } else {
          goback();
        }
      } 
      exit;
    }
  }

  if($action === 'finish') {
    finish($me['booking_id']);
  }
  
  if($action === 'complete') {
    $parking = uploadFiles(['parking']);
    $payload = [
      'data' => [
        'type' => 'street',
        'streetHours' => $_POST['hours'],
        'streetMinutes' => null,
        'streetOvernightRest' => false,
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
