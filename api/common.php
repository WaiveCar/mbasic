<?php
session_start();
include('db.php');

$labelGuide = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

function curldo($url, $params = false, $verb = false) {
  if($verb === false) {
    // this is a problem
  }
  if($_SERVER["HTTP_HOST"] === 'mbasic.waivecar.com') {
    $HOST = 'http://api-local.waivecar.com:3080';
  } else {
    $HOST = 'https://api.waivecar.com';
  }

  $ch = curl_init();

  $header = [];
  if(isset($_SESSION['token'])) {
    $header[] = "Authorization: ${_SESSION['token']}";
  }
    
  if($verb !== 'GET') {
    if(!$params) {
      $params = [];
    }
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
  if(isset($_SESSION['lasterror'])) {
    ?>
    <div class='error box'>
      <?= $_SESSION['lasterror'] ?>
    </div>
    <? 
    unset($_SESSION['lasterror']);
  }
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


function zip2geo($zip) {
  // we try to check our local cache for this lat/lng (rounded to 3 precision points)
  $location = db_get($zip);
  if(!$location) {
    $res = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?address=$zip");
    if ($res) {
      $resJSON = json_decode($res, true);
      if(!empty($resJSON['results'])) {
        if(!empty($resJSON['results'][0])) {
          $location = json_encode($resJSON['results'][0]['geometry']['location']);
          db_set($zip, $location);
        }
      }
    }
  }
  if($location) {
    return json_decode($location, true);
  }
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


function tis($what) {
  if(is_bool($what) && $what == true) {
    return true;
  }
  if(!$what || is_string($what)) {
    return false;
  }
  if(array_key_exists('status', $what)) {
    return $what['status'] === 'success';
  } else {
    if(!empty($what['message'])) {
      $_SESSION['lasterror'] = $what['message'];
    }
  }
} 

function reserve($car) {
  $me = me();
  return tis(post('/bookings', [
    'userId' => $me['id'],
    'carId' => $car,
    'version' => 'mbasic'
  ]));
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

function hasFlag($what) {
  $me = me();
  if(!$me['booking'] || !$me['booking']['flags']) {
    return false;
  }
  var_dump($me['booking']['flags']);
  exit;
}

function getstate($nocache = false) {
  $me = me($nocache);

  $from = $_SERVER['SCRIPT_NAME'];
  $to = false;
  if(!$me || (isset($me['code']) && $me['code'] === "INVALID_TOKEN")) {
    $to = '/index.php';
  } else if($me['booking']) {
    if($me['booking']['status'] === 'reserved') {
      $to = '/gettocar.php';
    } else if($me['booking']['status'] === 'started') {
      if(hasFlag('inspected')) {
        $to = '/inbooking.php';
      } else {
        $to = '/startbooking.php';
      }
    } else if($me['booking']['status'] === 'ended') {
      $to = '/endbooking.php';
    } 
  } else {
    $to = '/showcars.php';
  }

  if($to && $from !== $to) {
    header("Location: $to");
    echo "<meta http-equiv='refresh' content='0; url=$to'>";
    ob_end_flush();
    flush();
    exit;
  }

}

function getMap($carList, $opts = []) {
  $key = 'AIzaSyBibUDNVBjFAKpwyPcZirJW4qHq2W2OO8M';//'AIzaSyD3Bf8BTFI_z00lrxWdReV4MpaqnQ8urzc';

  global $labelGuide;
  $ix = 0;
  $qmap = [];
  $center = '';
  if(!empty($opts['me'])) {
    $qmap[] = "markers=color:0x0000ff%7C" . $opts['me']['latitude'] . "," . $opts['me']['longitude'];
    $center = 'center=' . $opts['me']['latitude'] . "," . $opts['me']['longitude'] . '&';
    $opts['zoom'] = 12;
  } else if(count($carList) === 1) {
    $car = $carList[0];
    $center = 'center=' . $car['latitude'] . "," . $car['longitude'] . '&';
    $opts['zoom'] = 14;
  }

  foreach($carList as $row) {
    if($row['range'] < 40) {
      $color = 'red';
    } else if($row['range'] < 60) {
      $color = 'orange';
    } else if($row['range'] < 80) {
      $color = 'yellow';
    } else if($row['range'] < 110) {
      $color = '0x779900';
    } else {
      $color = '0x00AA00';
    }
    $qmap[] = "markers=color:$color%7Clabel:${labelGuide[$ix]}%7C${row['latitude']},${row['longitude']}";
    $ix++;
  }

  $locationList = get('/locations');
  foreach($locationList as $location) {
    if($location['type'] === 'zone') {
      $loc = implode('|', array_map(
        function($a) { return "${a[1]},${a[0]}"; },
        $location['shape']
      ));
      $qmap[] = 'path=fillcolor:0x00AA0050|weight:0|' . $loc;
    }
  }
  $params = implode("&", $qmap);
  $zoom = '';
  if(!empty($opts['zoom'])) {
    $zoom = "zoom=${opts['zoom']}&";
  }

  return "https://maps.googleapis.com/maps/api/staticmap?${center}size=400x300&${zoom}maptype=roadmap&$params&key=$key";
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

function doheader($title, $showaccount=true) {
  $me = me();
?>
<!doctype html>

<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?= $title ?></title>
<link rel="stylesheet" href="/css/styles.css">
</head>

  <body>
    <? if ($showaccount) { ?>
      <a href='/account.php' id='account-link'>Your Account</a>
    <? } ?>
<?
  showerror();
}

function actionList($base, $list) {
?>
  <div class='action-list'>
  <? foreach($list as $row) { 
    $klass = '';
    if(count($row) == 3) {
      $klass = " wid-${row[2]}";
    }
  ?>
    <a class="button<?=$klass?>" href="<?= $base ?>?action=<?= $row[0] ?>"><?= $row[1] ?></a>
  <? } ?>
  </div>
<?
}

function getTag($what, $field = false) {
  $me = me();
  foreach($me['tagList'] as $tag) {
    if($tag['group']['name'] === $what) {
      if($field) {
        return $tag['groupRole'][$field];
      }
      return $tag['groupRole'];
    }
  }
}

function showLocation($car) {
  $location = location($car);
  echo "<a target='_blank' class='map' href='https://maps.google.com/maps/?q=${car['latitude']},${car['longitude']}+(${car['license']})'>";
  ?>
      <span> <?= $location ?> </span>
      <img src="<?=getMap([$car], ['zoom' => 13])?>">
    </a>
  <?
}

function imageList($opts, $list) {
  $required = $opts['required'] ? 'required' : '';
  foreach($list as $row) { ?>
    <div class='image-upload'>
      <input name="<?= $row[1] ?>" type="file" accept="image/*;capture=camera" <?= $required ?> capture="camera" />
      <img src='img/camera.gif'>
      <div> <?= $row[0]; ?></div>
    </div>
  <? } 
}
