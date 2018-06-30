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
  var_dump($res);
}


if($me['booking_id']) {
  if($action === 'extend') {
    extend($me['booking_id']);
  }

  if($action === 'cancel') {
    cancel($me['booking_id']);
  }

  if($action === 'start') {
    start($me['booking_id']);
  }
}
getstate("nocache");
