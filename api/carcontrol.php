<?
include('common.php');

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
  if($action === 'extend') {
    extend($me['booking_id']);
  }

  if($action === 'cancel') {
    cancel($me['booking_id']);
  }

  if($action === 'start') {
    start($me['booking_id']);
  }

  if($action === 'finish') {
    finish($me['booking_id']);
  }
  
  if($action === 'complete') {
    if(complete($me['booking_id'])) {
      // This is a special use-case
      // for getting to the receipt

      header("Location: /receipt.php");
      echo "<meta http-equiv='refresh' content='0; url=/receipt.php'>";
      exit;
    }
  }
}
getstate("nocache");
