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
<div class='box'>
  <center>
    <h1>Please wait</h1>
    <p>This can take up to 30 seconds.</p>
    <p>
      <img src="/img/ajax-loader.gif">
    </p>
  </center>
</div>
<?
ob_end_flush();
flush();

$me = me();
if($action === 'reload') {
  header('Location: gettocar.php');
  exit;
}
if($action === 'reserve') {
  $res = reserve($_GET['car']);
}


if($me['booking_id']) {
  $booking = $me['booking_id'];
  if($action === 'extend') {
    extend($booking);
  }

  if($action === 'cancel4realz') {
    cancel($booking);
  }

  if($action === 'cancel') {
    confirm("Cancel your booking?", "This will cancel your booking and you'll need to wait 30 minutes to rebook this car.", [
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
    confirm("End your booking?", "Are you sure you're done with $car?", [
      [ "Yes, I'm done with $car.", "/api/carcontrol.php?action=end4realz", 'wid-1'],
      [ "I'm not done. I want to keep going!", "/api/carcontrol.php?action=nop", 'wid-1 primary' ]
    ]);
  }
  if($action === 'end4realz') {
    tis(put("/bookings/$booking/canend"));
    load('/endbooking.php');
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
