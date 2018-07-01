<?php
session_start();
include('db.php');

function curldo($url, $params = false, $verb = false) {
  if($verb === false) {
    // this is a problem
  }
  $HOST = 'http://api-local.waivecar.com:3080';

  $ch = curl_init();

  $header = [];
  if(isset($_SESSION['token'])) {
    $header[] = "Authorization: ${_SESSION['token']}";
  }
    
  if($params) {
    $data_string = json_encode($params);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);  
    $header[] = 'Content-Type: application/json';
    $header[] = 'Content-Length: ' . strlen($data_string);
  }

  curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
  curl_setopt($ch, CURLOPT_URL, $HOST . $url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $verb);  
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  $res = curl_exec($ch);
  /*
  var_dump(curl_getinfo ($ch));
  
  $tolog = json_encode([
      'verb' => $verb,
      'header' => $header,
      'url' => $url,
      'params' => $params,
      'res' => $res
  ]);

  file_put_contents('/tmp/log.txt', $tolog, FILE_APPEND);
   */

  $resJSON = @json_decode($res, true);
  if($resJSON) {
    return $resJSON;
  }
  return $res;
}

function get($url, $params = false) {
  return curldo($url, $params, 'GET');
}

function post($url, $params = false) {
  return curldo($url, $params, 'POST');
}

function put($url, $params = false) {
  return curldo($url, $params, 'PUT');
}

function del($url, $params = false) {
  return curldo($url, $params, 'DELETE');
}

function showerror() {
}

$whoami = false;
function me($nocache = false) {
  global $whoami;
  if(!$whoami || $nocache) {
    $whoami = get('/users/me');
    if(!empty($whoami['code']) && $whoami['code'] === 'AUTH_INVALID_TOKEN') {
      return false;
    }
    $whoami['booking_id'] = false;
    if(array_key_exists('booking', $whoami)) {
      $whoami['booking_id'] = $whoami['booking']['id'];
    }
  } 
  return $whoami;
}


function location($obj) {
  global $db; 
  $qs = implode(',', [round($obj['latitude'],3), round($obj['longitude'],3)]);
  // we try to check our local cache for this lat/lng (rounded to 3 precision points)
  $location = db_get($qs);
  if(!$location) {
    // if we failed, then we ask the goog

    $res = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?latlng=$qs");

    if ($res) {
      $resJSON = json_decode($res, true);
      if(!empty($resJSON['results'])) {
        if(empty($resJSON['results'][0])) {
          var_dump($resJSON);
        }
        $location = $resJSON['results'][0]['formatted_address'];

        // this just looks stupid, we know the country
        $location = str_replace(", USA", "", $location);

        // and the state.
        $location = str_replace(", CA ", " ", $location);

        db_set($qs, $location);
      }
    }
  }
  return $location;
}


function reserve($car) {
  $me = me();
  return post('/bookings', [
    'userId' => $me['id'],
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

function finish($booking) {
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

function getstate($nocache = false) {
  $me = me($nocache);

  $from = $_SERVER['REQUEST_URI'];
  $to = false;
  if(!$me || (isset($me['code']) && $me['code'] === "INVALID_TOKEN")) {
    $to = '/index.php';
  } else if($me['booking']) {
    if($me['booking']['status'] === 'reserved') {
      $to = '/gettocar.php';
    } 
  } else {
    $to = '/showcars.php';
  }

  if($to && $from !== $to) {
    header("Location: $to");
    exit;
  }

}

function getMap($carList) {
  $key = 'AIzaSyBibUDNVBjFAKpwyPcZirJW4qHq2W2OO8M';//'AIzaSyD3Bf8BTFI_z00lrxWdReV4MpaqnQ8urzc';

  $params = implode('&', array_map(function($row) {
    if($row['range'] < 40) {
      $color = 'red';
    } else if($row['range'] < 60) {
      $color = 'orange';
    } else if($row['range'] < 80) {
      $color = 'yellow';
    } else {
      $color = 'green';
    }
    return "markers=color:$color%7Clabel:${row['license']}%7C${row['latitude']},${row['longitude']}";
  }, $carList));
  return "https://maps.googleapis.com/maps/api/staticmap?zoom=13&size=600x300&maptype=roadmap&$params&key=$key";
}

// from https://www.geodatasource.com/developers/php
function distance($lat1, $lon1, $lat2, $lon2) {
  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  return $dist * 60 * 1.1515;
}

function location_link($obj) {
  $location = location($obj);
  return "<a target='_blank' class='location-link' href='https://maps.google.com/maps/?q=${obj['latitude']},${obj['longitude']}+(${obj['license']})'>$location</a>";
}

function doheader($title) {
  $me = me();
?>
<!doctype html>

<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?= $title ?></title>
<link rel="stylesheet" href="css/styles.css">
</head>

  <body>
    <a href='/account.php' id='account-link'><?= $me['firstName'] ?> <?= $me['lastName'] ?> </a>
<?
  showerror();
}

function actionList($base, $list) {
?>
  <div class='action-list'>
  <? foreach($list as $row) { ?>
    <a href="<?= $base ?>?action=<?= $row[0] ?>"><?= $row[1] ?></a>
  <? } ?>
  </div>
<?
}

function imageList($list) {
  foreach($list as $row) { ?>
    <?= $row[0]; ?>
    <input name="<?= $row[1] ?>" type="file" accept="image/*" required capture="camera" />
  <? } 
}
