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
  if($action === 'extend') {
    extend($me['booking_id']);
  }

  if($action === 'cancel4realz') {
    cancel($me['booking_id']);
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
    confirm("End your booking?", "Are you sure you want to end your booking?", [
      [ "Yes, I'm done with the WaiveCar.", "/api/carcontrol.php?action=end4realz", 'wid-1'],
      [ "I'm not done. I want to keep going!", "/api/carcontrol.php?action=nop", 'wid-1 primary' ]
    ]);
  }
  if($action === 'end4realz') {
    load('/endbooking.php');
  }

  if($action === 'finish') {
    finish($me['booking_id']);
  }
  
  if($action === 'complete') {
    //var_dump($_FILES);
    $parking = uploadFiles(['parking']);
    $me = me();
    $id = $me['booking_id'];
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

    $data = put("/bookings/$id/end", $payload); 
    if($data) {
      // This is a special use-case
      // for getting to the receipt
      if(put("/bookings/$id/complete")) {
        load('/receipt.php');
        exit;
      }
    }
  }
}
getstate("nocache");
