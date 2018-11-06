<?
function instructions($what) {
  doheader(ucfirst($what), [
    'icon' => '/img/' . $what . '.png',
    'showaccount' => false]
  );
  infobox("Add a link to $what a WaiveCar", [
    '<b>Android:</b><ol><li>Tap the 3 dots in the upper left to get to the menu. <li>Scroll down and tap on "Add to Home screen"</ol>',
    '<b>iPhone:</b><ol><li>Tap on the share button which looks like a square with an upward arrow on it. <li>Scroll and tap "Add to Home Screen" which is a grey box with a plus sign.</ol>',
    "After you're done, press the back button and add any other functions you'd like."
  ], 'prompt');
}

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

if($action === 'lock' || $action === 'unlock') {
  $title = ucfirst(action) . "ing. Please wait";
} else {
  $title = "Please Wait";
}
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
    confirm("Extend Your Reservation", "You can buy either an extra 10 or 20 minutes to get to your WaiveCar.<p><em>Reminder:</em> If you don't make it in time, you'll have to wait 30 minutes to rebook the same WaiveCar.</p>", [
      [ "$4.20 for 20 extra minutes", "/api/carcontrol.php?action=extend4realz&howmuch=20", 'wid-1 primary preferred'],
      [ "$1.00 for 10 extra minutes", "/api/carcontrol.php?action=extend4realz&howmuch=10", 'wid-1 '],
      [ "No thanks! I'll make it in time", "control/nop", 'wid-1 ignored' ]
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
