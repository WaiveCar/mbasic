<?php
session_start();
include('db.php');

$labelGuide = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

$HOST = false;
function getHost() {
  global $HOST;
  if(!$HOST) {
    if($_SERVER["HTTP_HOST"] === 'mbasic.waivecar.com' || substr($_SERVER["HTTP_HOST"], -1) === '0') {
      $HOST = 'http://api-local.waivecar.com:3080';
    } else {
      $HOST = 'https://api.waivecar.com';
    }
  }
  return $HOST;
}
 

function curldo($url, $params = false, $verb = false, $opts = []) {
  if($verb === false) {
    // this is a problem
  }
  $verb = strtoupper($verb);
  $HOST = getHost();

  $ch = curl_init();
  $url = '/' . ltrim($url, '/');

  $header = [];
  if(isset($_SESSION['token'])) {
    $header[] = "Authorization: ${_SESSION['token']}";
  }
    
  if($verb !== 'GET') {
    if(!isset($opts['isFile'])) {
      if(!$params) {
        $params = [];
      }
      $params = json_encode($params);
      $header[] = 'Content-Type: application/json';
    } else {
      $header[] = 'Content-Type: multipart/form-data';
    }
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);  
    // $header[] = 'Content-Length: ' . strlen($data_string);
  }

  if($verb === 'POST') {
    curl_setopt($ch, CURLOPT_POST,1);
  }

  curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
  curl_setopt($ch, CURLOPT_URL, $HOST . $url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $verb);  
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  $res = curl_exec($ch);
  
  /*
  $tolog = json_encode([
      'verb' => $verb,
      'header' => $header,
      'url' => $url,
      'params' => $params,
      'res' => $res
  ]);
  var_dump(['>>>', curl_getinfo ($ch), json_decode($tolog, true)]);
  

  file_put_contents('/tmp/log.txt', $tolog, FILE_APPEND);
   */

  $resJSON = @json_decode($res, true);
  if($resJSON) {
    return $resJSON;
  }
  return $res;
}

$_isLevel = null;
function isLevel() {
  global $_isLevel;
  if(!is_bool($_isLevel)) {
    $region = getTag('region', 'id');
    $_isLevel = $region === 7;
  }
  return $_isLevel;
}

function get($url, $params = false) {
  return curldo($url, $params, 'GET');
}

function post($url, $params = false) {
  return curldo($url, $params, 'POST');
}
function postFile($url, $params = false) {
  return curldo($url, $params, 'POST', ['isFile' => true]);
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
   <p><?= $_SESSION['lasterror'] ?></p>
   <div>
    <?

    if(isset($_SESSION['options'])) { 
      foreach($_SESSION['options'] as $option) { 
        if(is_string($option['action']['params'])) {
          $option['action']['params'] = json_decode($option['action']['params'], true);
        }
        $qstr = http_build_query([
          'action' => 'generic',
          'params' => $option['action']
        ]);
        echo "<a class='button' href='/api/carcontrol.php?$qstr'>${option['title']}</a>";
       }
    }
    echo "</div>";
    echo '</div>';

    unset($_SESSION['lasterror']);
    unset($_SESSION['options']);
  }
}

$whoami = false;
function me($opts = []) {
  if(!$whoami || aget($opts, 'nocache')) {
    $whoami = get('/users/me');
    if(!empty($whoami['code']) && (
      $whoami['code'] === 'AUTH_INVALID_TOKEN' ||
      $whoami['code'] === 'INVALID_TOKEN'
    )) {
      return false;
    }
    $whoami['booking_id'] = false;
    if(array_key_exists('booking', $whoami)) {
      $whoami['booking_id'] = $whoami['booking']['id'];
    }
  } 

  if( aget($opts, 'withcar') && 
      !aget($whoami, 'car') && 
      aget($whoami, 'booking_id')
  ) {
    $whoami['car'] = $car = car_info($whoami['booking']['carId']);
  }

  return $whoami;
}


function confirm($title, $prompt, $options) {
  load('confirm.php?' . http_build_query(['t' => $title, 'p' => $prompt, 'o' => $options]));
  exit;
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
      $parts = preg_split('/\t/', $what['message']);
      $_SESSION['lasterror'] = $parts[0];
      if(!empty($what['options'])) {
        $_SESSION['options'] = $what['options'];
      }
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

function extend($booking, $howmuch) {
  return tis(put("/bookings/$booking/extend?howmuch=$howmuch"));
}

function start($booking) {
  return tis(put("/bookings/$booking/ready"));
}

function finish($booking) {
  return tis(put("/bookings/$booking/end"));
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
  return get("/cars/$car/lock");
}

function unlock($car) {
  return get("/cars/$car/unlock");
}

function money($amount) {
  printf("$%.2f", round($amount / 100, 2));
}

function uploadFiles($list = false) {
  $res = [];
  $host = getHost();

  foreach($_FILES as $key => $value) {
    if($list && array_search($key, $list) === false) {
      continue;
    }
    if($value['size']) {
      $str = "/usr/bin/curl -s -X POST $host/files -H 'Authorization: ${_SESSION['token']}' -F file=@${value['tmp_name']}";
      $resTxt = shell_exec($str);
      $resJson = json_decode($resTxt, true);
      $res[] = $resJson[0];
    }
    unset($_FILES[$key]);
  }

  return $res;
}

function createReport($fileList) {
  $me = me();
  $res = post('/reports', [
    'bookingId' => $me['booking']['id'],
    'files' => $fileList
  ]);
}


function hasFlag($what) {
  $me = me();
  if(!$me['booking'] || !$me['booking']['flags']) {
    return false;
  }

  return strpos($me['booking']['flags'], $what) !== false;
}

// dot notation array get 
function aget($source, $keyList) {
  if(!is_array($keyList)) {
    $keyList = explode('.', $keyList);
  }
  $key = array_shift($keyList);

  if($source && isset($source[$key])) {
    if(count($keyList) > 0) {
      return aget($source, $keyList);
    } 
    return $source[$key];
  }
}

function getstate($nocache = false) {
  $me = me(['nocache' => $nocache]);

  $from = $_SERVER['SCRIPT_NAME'];
  $to = false;
  if(!$me || aget($me, 'code') === "INVALID_TOKEN") {
    $to = '/index.php';
  } else if($me['booking']) {
    if($me['booking']['status'] === 'reserved') {
      $to = '/gettocar.php';
    } else if($me['booking']['status'] === 'started') {
      if(hasFlag('inspected')) {
        $to = '/inbooking.php';
      } else {
        $to = '/inbooking.php';
        //$to = '/startbooking.php';
      }
    } else if($me['booking']['status'] === 'ended') {
      if(!isLevel()) {
        $to = '/endbooking.php';
      } else {
        $to = '/receipt.php';
      }
    } 
  } else {
    $to = '/showcars.php';
  }

  if($to && $from !== $to) {
    load($to);
    ob_end_flush();
    flush();
    exit;
  }

}

function load($ep) {
  $ep = '/' . ltrim($ep, '/');
  @header("Location: $ep");
  ?>
  <meta http-equiv='refresh' content='0; url=<?= $ep ?>'>
  <script>window.location="<?= $ep ?>";</script>
  <div class='box'>
    <a class="button wid-1" href="<?= $ep ?>">Continue</a>
  </div>
  <?
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

  if(!isset($opts['level'])) {
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
  }

  $locationList = get('/locations');
  foreach($locationList as $location) {
    // brooklyn level parking is 1246 ... I know it's not a great method
    if(isset($opts['level'])) {
      if($location['id'] == 1246) {
        $qmap[] = "markers=color:green%7C${location['latitude']},${location['longitude']}";
        if(!$center) {
          $center = 'center=' . $location['latitude'] . "," . $location['longitude'] . '&';
        }
      }
    } else {
      if($location['type'] === 'zone') {
        $loc = implode('|', array_map(
          function($a) { return "${a[1]},${a[0]}"; },
          $location['shape']
        ));
        $qmap[] = 'path=fillcolor:0x00AA0050|weight:0|' . $loc;
      }
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
  <? if ($showaccount) { 
    echo "<a href='/account.php' id='account-link'>Your Account</a>";
   } 
  showerror();
}

function actionList($base, $list) {
?>
  <div class='action-list'>
  <? 
    foreach($list as $row) { 
      $klass = '';
      if(count($row) == 3) {
        $klass = " wid-${row[2]}";
      }
      ?>
      <a class="button<?= $klass ?>" href="<?= $base ?>?action=<?= $row[0] ?>"><?= $row[1] ?></a>
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
    </div>
  <? } 
}
