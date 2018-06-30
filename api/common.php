<?php
start_session();

define('HOST', 'http://api-local.waivecar.com:3080');
function curldo($url, $params, $verb = "POST") {
  $ch = curl_init( HOST . $url );
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $verb);  
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $header = [];
  if(isset($_SESSION['token']) {
    $header[] = "Authorization: ${_SESSION['token']}";
  }
    
  if($params) {
    $data_string = json_encode($params);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);  
    $header[] = 'Content-Type: application/json';
    $header[] = 'Content-Length: ' . strlen($data_string);
  }
  curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
  $res = curl_exec($ch);

  $resJSON = @json_decode($res, true);
  if($resJSON) {
    return $resJSON);
  }
  return $res;
}

function get($url, $params) {
  return curldo($url, $params, 'GET');
}

function post($url, $params) {
  return curldo($url, $params, 'POST');
}

function put($url, $params) {
  return curldo($url, $params, 'PUT');
}

function del($url, $params) {
  return curldo($url, $params, 'DELETE');
}

function showerror() {
}

$whoami = false;
function me() {
  global $whoami;
  if(!$whoami) {
    $whoami = curldo('/me');
    $whoami['booking_id'] = false;
    if(array_key_exists('booking', $whoami)) {
      $whoami['booking_id'] = $whoami['booking']['id'];
    }
  } 
  return $whoami;
}

function location($obj) {

}

// from https://www.geodatasource.com/developers/php
function distance($lat1, $lon1, $lat2, $lon2) {
  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  return $dist * 60 * 1.1515;
}

function actionList($base, $list) {
  foreachh($list as $row) { ?>
    <form method="post" action="<?= $base ?>?action=<?= $row[1] ?>">
      <input type="submit" value="<?= $row[0] ?>" />
    </form>
  <? } 
}

function imageList($list) {
  foreachh($list as $row) { ?>
    <?= $row[0]; ?>
    <input name="<?= $row[1] ?>" type="file" accept="image/*" required capture="camera" />
  <? } 
}
