<?
include('common.php');
doheader('Waiting', false);
?>
<div class='box'>
  <center>
  <h1>Please wait</h1>
  <p>This can take up to 30 seconds.</p>
  <img src="/img/ajax-loader.gif">
  </center>
</div>
<?
ob_end_flush();
flush();

$me = me();
$action = $_GET['action'];
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

  if($action === 'cancel') {
    cancel($me['booking_id']);
  }

  if($action === 'start') {
    $fileList = uploadFiles();
    if(count($fileList) > 0)  {
      createReport($fileList);
    }
    start($me['booking_id']);
  }

  if($action === 'finish') {
    $parking = uploadFiles(['parking']);
    $me = me();
    $id = $me['booking_id'];
    $payload = [
      'data' => [
        'streetHours' => $_POST['hours'],
        'bookingId' => $id,
        'streetSignImage' => $parking[0]
      ]
    ];

    tis(put("/bookings/$booking/complete", $payload));

    $fileList = uploadFiles();

    if(count($fileList) > 0) {
      createReport($fileList);
    }
  }
  
  if($action === 'complete') {
    if(complete($me['booking_id'])) {
      // This is a special use-case
      // for getting to the receipt
      load('/receipt.php');
      exit;
    }
  }
}
getstate("nocache");
