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
  confirm("No Active Booking", "Cannot $action because there's no active booking.",[
    [ "Find Availale WaiveCars", "/showcars.php", "wid-1" ]
  ], ['inline' => true, 'klass' => "error"]);
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
    $goad = '';
    if($me['credit'] > 500) {
      $goad = sprintf("<p>(You have $%.2f in credit!)</p>", $me['credit'] / 100);
    }
    
    confirm("Extend Your Reservation", "<b>Just $1.00</b> for ten minutes and $0.30 each additional minute until you start your ride.$goad", [
      [ "Save the WaiveCar for me!", "/api/carcontrol.php?action=extend4realz&howmuch=-1", 'wid-1 primary'],
      [ "Nah! I'll make it.", "control/nop", 'wid-1 ignore' ]
    ]);
  }

  if($action === 'cancel4realz') {
    cancel($booking);
  }

  if($action === 'cancel') {
    confirm("Cancel Your Booking", "Are you sure you want to cancel your booking?<p><em>Reminder:</em> If you cancel, you need to wait 30 minutes to rebook the same WaiveCar.</p>", [
      [ "Yes, cancel my booking", "control/cancel4realz", 'wid-1 danger'],
      [ "No, do not cancel my booking", "control/nop", 'wid-1 primary' ]
    ]);
  }

  if($action === 'start') {
    $me = me(['withcar' => true]);
    $distance = distance($_REQUEST, $me['car']);
    if(is_numeric($distance)) {
      $distance *= 1609;
    }

    if($distance === false || $distance > 100) {
      $car = $me['car'];
      $plate = aget($me, 'car.plateNumberWork');
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
    $nosign = !empty($_POST['nosign']);
    $nophoto = !empty($_POST['nophoto']);

    if(!aget($_FILES, 'parking.size') && !$nophoto) {
      $err[] = 'Upload an image or if you cannot get a photo, choose that option.';
    }
    if (empty($_POST['hours']) && !$nosign) {
      $err[] = 'Specify the time the WaiveCar need to move, or if there is no sign, choose that option.';
    }
    // The user didn't specify hours or say there was no sign.
    if(count($err) > 0) {
      $err = 'Please correct the following:<ul><li>' . implode('</li><li>', $err) . '</li></ul>';

      makeError("Please complete this page", $err);
      goback();
      exit;
    } else {
      var_dump("BlAH");
      exit;

      $offset = 0;
      $append = $_POST['append'];

      if($append == 'pm') {
        $offset += 12;
      } else if ($append == 'hours') {
        $offset = dateTz('G');
      }

      $parts = explode(':', $_POST['hours']);
      $hour = intval($parts[0]);
      $day = intval($_POST['day']);

      if($hour + $offset > 24) {
        $day = ($day + 1) % 7;
      }
      $hour = ($hour + $offset) % 24;
      $user_input = $POST['day'];

      $parking = uploadFiles(['parking']);
      $payload = [
        'data' => [
          'nosign' => $nosign,
          'nophoto' => $nophoto,
          'expireHour' => $hour,
          'expireDay' => $day,
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
}
getstate("nocache");
