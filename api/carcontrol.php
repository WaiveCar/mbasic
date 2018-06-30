<?

include('common.php');

function reserve($car) {
  return post('/bookings', [
    'carId' => $car,
    'version' => 'mbasic'
  ]);
}

function tis($what) {
  if(array_key_exists('status', $what)) {
    return $what['status'] === 'success';
  }
} 

function cancel($booking) {
  return tis(del("/bookings/$booking"));
}

function extend($booking) {
  return tis(put("/bookings/$booking/extend"));
}

function start($booking) {
  return tis(put("/bookings/$booking/ready"));
}

function end($booking) {
  return put("/bookings/$booking/end");
}

function complete($booking) {
  return put("/bookings/$booking/complete");
}

function booking_info($booking) {
  return get("/bookings/$booking");
}

function car_info($car) {
  return get("/cars/$car");
}

function lock($car) {
  return put("/cars/$car/lock");
}

function unlock($car) {
  return put("/cars/$car/unlock");
}

$action = $_GET['action'];
if($action === 'reload') {
  header('Location: gettocar.php');
  exit;
}
if($action === 'reserve') {
  return reserve($_POST['car']);
}

$me = me();

if($me['booking_id']) {
  if($action === 'extend') {
    if(extend($me['booking_id'])) {
      header('Location: gettocar.php');
    }
  }

  if($action === 'cancel') {
    if(cancel($me['booking_id'])) {
      header('Location: showcars.php');
    }
  }

  if($action === 'start') {
    if(start($me['booking_id'])) {
      header('Location: startbooking.php');
    }
  }
}
