<?php
session_start();
include('db.php');

$labelGuide = '123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
$gList = [
  'AIzaSyBQysUfVLDsR8aYHZBQ9epqpGAQ-LZ1bTw',
  'AIzaSyBibUDNVBjFAKpwyPcZirJW4qHq2W2OO8M',
  'AIzaSyDZkuoCmKxhxy5AH0jMAUcW0JvggQX3WXI',
  'AIzaSyA718cwy2i_uf-GCKrq7cB-1WDvKL5gsh8',
  'AIzaSyC_bFO-1OoYAVg-dTS0MOCbWer6tgEwRhk',

  'AIzaSyCjNzEEetDOi63O7qrD6APLffH0daZIDeQ',
  'AIzaSyA77YUSEIo77Ms26dlAKllaBFYl-XAaELs'
];
$googleKey = $gList[0];

$HOST = false;
function resolve($path) {
  return rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . ltrim($path, '/');
}

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

// a secret version of vardump that hides things.
function svar_dump() {
  echo '<script type=text/plain>';
  call_user_func_array('var_dump', func_get_args());
  echo '</script>';
}

// from https://stackoverflow.com/questions/6225351/how-to-minify-php-page-html-output
function sanitize_output($buffer) {

  $search = array(
    '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
    '/[^\S ]+\</s',     // strip whitespaces before tags, except space
    '/(\s)+/s',         // shorten multiple whitespace sequences
  );

  $replace = array(
    '>',
    '<',
    '\\1',
    ''
  );

  $buffer = preg_replace($search, $replace, $buffer);

  return str_replace('> <','><', $buffer);
} 

function curldo($url, $params = false, $verb = false, $opts = []) {
  if($verb === false) {
    $verb = 'GET';
    // this is a problem
  }
  $verb = strtoupper($verb);
  $HOST = getHost();

  $ch = curl_init();
  $url = '/' . ltrim($url, '/');

  $header = [];
  if(isset($_SESSION['token']) && strlen($_SESSION['token']) > 2) {
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

  if(isset($opts['raw'])) {
    return $res;
  }
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

function post($url, $params = false, $opts = false) {
  return curldo($url, $params, 'POST', $opts);
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

function infobox($title, $content, $klass = '') {
  if(!is_array($content)) {
    $content = [$content];
  }
?>
   <div class='box <?=$klass?>'>
     <div class='title'><?= $title ?></div>

     <div class=content>
       <div class='message'><?
          foreach($content as $p) {
            echo "<p>$p</p>";
          } 
          ?>
       </div>
     </div> 
   </div>
<?
}

function widget($widget, $options) {
  if(!aget($options, 'inline')) {
    load("/$widget.php?" . http_build_query($options));
  } else {
    $_GET = $options;
    include(resolve("$widget.php"));
  }
  exit;
}

function prompt($title, $prompt, $var, $doPage = false) {
  if($doPage) {
    load('prompt.php?' . http_build_query([
      'b' => $doPage,
      'v' => $var,
      't' => $title, 'p' => $prompt, 'o' => $options]));
  } else {
    $_GET['t'] = $title;
    $_GET['p'] = $prompt;
    $_GET['v'] = $var;
    include(resolve('prompt.php'));
  }
  exit;
}

function confirm($title, $prompt, $buttons = [], $options = []) {
  widget('confirm', [
    't' => $title, 
    'p' => $prompt, 
    'b' => $buttons,
    'o' => $options,
    'inline' => aget($options, 'inline')
  ]);
}

function showerror() {
  if(isset($_SESSION['lasterror'])) {
   $err = $_SESSION['lasterror'];
   $verb = empty($err['options']) ? 'error' : 'info';
   ?>

   <div class='<?=$verb ?> box inline'>
     <div class=title><?= $err['title'] ?></div>

     <div class=content>
      <div class=message><div class=copy><?= $err['message'] ?></div>
      <?

      if(isset($err['options'])) { 
        echo '<div class="options">';

        foreach($err['options'] as $option) { 
          if(is_string($option['action']['params'])) {
            $option['action']['params'] = json_decode($option['action']['params'], true);
          }
          $klass = aget($option, 'priority');

          $href = '/api/carcontrol.php?' . http_build_query([
            'action' => 'generic',
            'params' => $option['action']
          ]);
          echo "<a class='btn wid-1 $klass' href='$href'>${option['title']}</a>";
        }
        echo '</div>'; // options
      }
      echo '</div>'; // message
      echo '</div>'; // content

    echo '</div>'; // box

    unset($_SESSION['lasterror']);
  }
}

function me($opts = []) {
  global $whoami;
  $from = aget($opts, 'from', '');
  if(aget($opts, 'yescache') && !empty($_SESSION['me'])) {
    $whoami = $_SESSION['me'];
  }

  if(!isset($whoami) || aget($opts, 'nocache')) {
    $whoami = get('/users/me?' . $from);
    if(!empty($whoami['code']) && (
      $whoami['code'] === 'AUTH_INVALID_TOKEN' ||
      $whoami['code'] === 'INVALID_TOKEN'
    )) {
      $whoami = $_SESSION['me'] = false;
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
    $whoami['car'] = car_info($whoami['booking']['carId']);
  }
  $_SESSION['me'] = $whoami;

  return $whoami;
}



function zip2geo($zip) {
  // we try to check our local cache for this lat/lng (rounded to 3 precision points)
  global $googleKey;
  $location = db_get($zip);
  if(!$location) {
    $res = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=$zip&key=$googleKey");
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
  global $googleKey;
  if(isset($obj['address'])) {
    return $obj['address'];
  }
  $qs = implode(',', [round($obj['latitude'] * 2,3)/2, round($obj['longitude'] * 2,3)/2]);
  // we try to check our local cache for this lat/lng (rounded to 3 precision points)
  $location = db_get($qs);
  if(!$location) {
    // if we failed, then we ask the goog

    $res = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=$qs&key=$googleKey");

    if ($res) {
      $resJSON = json_decode($res, true);
      if(!empty($resJSON['results'])) {
        if(empty($resJSON['results'][0])) {
          var_dump($resJSON);
        }
        $location = $resJSON['results'][0]['formatted_address'];

        $location = preg_replace('/, [A-Z]{2} \d{5}, USA$/', '', $location);

        db_set($qs, $location);
      }
    }
  }
  return $location;
}


function makeError($title, $text, $opts = false) {
  $_SESSION['lasterror'] = [ 
    'title' => $title,
    'message' => $text,
    'options' => $opts,
  ];
}

function throwError($title, $text, $opts = false) {
  makeError($title, $textr, $opts);
  goback();
}

function tis($what) {
  if(is_bool($what) && $what == true) {
    return true;
  }
  if(!$what || is_string($what)) {
    return false;
  }
  if(!array_key_exists('status', $what)) {
    if(!empty($what['message'])) {
      $parts = preg_split('/\t/', $what['message']);

      return makeError(
        aget($what, 'title', 'Notice'),
        $parts[0],
        aget($what, 'options')
      );
    }
  }
  return $what;
} 

function reserve($car) {
  $me = me();
  return tis(post('/bookings', [
    'userId' => $me['id'],
    'carId' => $car,
    'version' => 'mbasic'
  ]));
}

function charge($id, $port) {
  return tis(put("/chargers/start/$id/$port"));
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
  return tis(put("/cars/$car/lock"));
}

function unlock($car) {
  return put("/cars/$car/unlock");
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
function aget($source, $keyList, $default = null) {
  if(!is_array($keyList)) {
    $keyStr = $keyList;
    $keyList = explode('.', $keyStr);

    $orList = explode('|', $keyStr);
    if(count($orList) > 1) {

      $res = null;
      foreach($orList as $key) {
        // this resolves to the FIRST valid value
        if($res === null) {
          $res = aget($source, $key);
        }
      }
      return ($res === null) ? $default : $res;
    }   
  }
  $key = array_shift($keyList);

  if($source && isset($source[$key])) {
    if(count($keyList) > 0) {
      return aget($source[$key], $keyList);
    } 
    return $source[$key];
  }

  return $default;
}

function getstate($nocache = false) {
  $me = me(['nocache' => $nocache, 'from'=>'getstate']);

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
  return $me;

}

function goback($url = false) {
  if(!$url) {
    $url = $_SERVER['HTTP_REFERER'];
  }
  load($url);
  exit;
}

function load($ep) {
  if(strpos($ep, 'http') !== 0) {
    $ep = '/' . ltrim($ep, '/');
  }
  @header("Location: $ep");
  ?>
  <meta http-equiv='refresh' content='0; url=<?= $ep ?>'>
  <script>window.location="<?= $ep ?>";</script>
  <div class='box'>
    <a class="btn wid-1" href="<?= $ep ?>">Continue</a>
  </div>
  <?
}

function getMap($carList, $opts = []) {
  $hide = aget($_SESSION, 'hide');
  $verb = $hide ? 'show' : 'hide';
  ?>
  <div class=container>
    <? if (!$hide) { ?><img class=img width=400 src="<?=getMapUrl($carList, $opts)?>"><? } ?>
    <img class=nop src=img/blank.png>
    <div class=map-controls>
      <a class=btn href=api/control.php?action=<?= $verb ?>><?= ucfirst($verb) ?></a>
      <? /*
       if (!$hide) { ?>
        <a class='zoom' href="">&#xFF0B;</a><a class='zoom' href="">&#x2014;</a> 
      <? }*/ ?>
    </div>
  </div> <?
}

/*
function hasFlag($what) {
  $me = me();
  if(is_string($me['booking']['flags'])) {
    $me['booking']['flags'] = json_decode($me['booking']['flags'], true);
  }
  return in_array( $me['booking']['flags'], $what);
}
 */


function getMapUrl($carList, $opts = []) {
  //$key = 'AIzaSyBibUDNVBjFAKpwyPcZirJW4qHq2W2OO8M';//'AIzaSyD3Bf8BTFI_z00lrxWdReV4MpaqnQ8urzc';

  global $labelGuide;
  global $googleKey;

  $ix = 0;
  $qmap = [];
  $center = '';
  if(!empty($opts['me'])) {
    $qmap[] = "markers=color:0x0000ff%7C" . $opts['me']['latitude'] . "," . $opts['me']['longitude'];
    $center = 'center=' . $opts['me']['latitude'] . "," . $opts['me']['longitude'] . '&';
    $opts['zoom'] = aget($opts, 'zoom', 12);
  } else if(count($carList) === 1) {
    $car = $carList[0];
    $center = 'center=' . $car['latitude'] . "," . $car['longitude'] . '&';
    $opts['zoom'] = aget($opts, 'zoom', 14);
  }

  if(!isset($opts['level'])) {
    foreach($carList as $row) {
      $color = aget($row, 'color', '0x00AA00');
      if(!empty($row['range'])) {
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
      }
      $qmap[] = "markers=color:$color%7Clabel:${labelGuide[$ix]}%7C${row['latitude']},${row['longitude']}";
      $ix++;
    }
  }

  if(!isset($opts['nozone'])) {
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
        if($location['type'] === 'zone' && !isset($opts['nozone'])) {
          $loc = implode('|', array_map(
            function($a) { return "${a[1]},${a[0]}"; },
            $location['shape']
          ));
          $qmap[] = 'path=fillcolor:0x73B06D50|weight:0|' . $loc;
        }
      }
    }
  }

  $params = implode("&", $qmap);
  $zoom = '';
  if(!empty($opts['zoom'])) {
    $zoom = "zoom=${opts['zoom']}&";
  }

  return "//maps.googleapis.com/maps/api/staticmap?${center}size=400x300&${zoom}maptype=roadmap&$params&key=$googleKey";
}

// from https://www.geodatasource.com/developers/php
function distance($lat1, $lon1, $lat2 = false, $lon2 = false) {
  if(!$lat2) {
    if(empty($lon1['longitude']) && empty($lat1['longitude'])) {
      return false;
    }
    $lon2 = $lon1['longitude'];
    $lat2 = $lon1['latitude'];
    $lon1 = $lat1['longitude'];
    $lat1 = $lat1['latitude'];
  }

  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  return $dist * 60 * 1.1515;
}

function location_link($obj) {
  $location = location($obj);
  $name = aget($obj,'license','charger');
  return "<a target=_blank href='//maps.google.com/maps/?q=${obj['latitude']},${obj['longitude']}+($name)'>$location</a>";
}

function dateTz($fmt, $ts = false) {
  if(!$ts) {
    $ts = time();
  }
  $dt = new DateTime();
  $dt->setTimezone(new DateTimeZone('America/Los_Angeles'));
  $dt->setTimestamp($ts);
  return $dt->format($fmt);
}

$hashsecret = '5c5911c4-eb6d-4a9e-865d-80e52db192b';
function hashGenerate($num) {
  global $hashsecret;
  $hash = base64_encode(hex2bin(hash('ripemd160', $hashsecret.$num)));
  $clear = base_convert($num, 10, 36);
  return substr($hash, 0, 8) . $clear;
}

function hashDecode($str) {
  global $hashsecret;
  $num = base_convert(substr($str, 8), 36, 10);
  $hash = base64_encode(hex2bin(hash('ripemd160', $hashsecret.$num)));
  if(substr($hash, 0, 8) === substr($str, 0, 8)) {
    return $num;
  } 
}

function doheader($title, $opts = []) {
  $showaccount = aget($opts, 'showaccount', true);
  $usecss = aget($opts, 'usecss', true);
  $extraHtml = aget($opts, 'extraHtml', '');
  $icon = aget($opts, 'icon', '/img/circle-logo_96.png');

  $me = me();
  $datetime = new DateTime; // current time = server time
  $otherTZ  = new DateTimeZone('America/Los_Angeles');
  $datetime->setTimezone($otherTZ); // calculates with new TZ now
  date_default_timezone_set('America/Los_Angeles');
  if(($url = aget($_SESSION, 'lasterror.options.go')) !== null) {
     unset($_SESSION['lasterror']);
     load($url);
     exit;
   }
?>
<!doctype html>
<html>
  <head>
    <title><?= $title ?></title>
    <meta name=viewport content="width=device-width,initial-scale=1.0">
    <link rel=icon href=<?= $icon ?>>
    <? if ($usecss) { ?> 
      <link rel=stylesheet href=/style.css?3>
    <? } ?>
    <?= $extraHtml; ?>
  </head>
<body>
  <? if ($showaccount) { 
    if($me) {
      echo "<div id=acnt><a href=me.php>Your Account</a></div>";
    } else {
      echo "<div id=acnt><a href=/>Login</a></div>";
    }
  } 
  showerror();
}

function instructions($what) {
  $icon = '/img/' . $what . '.png';
  doheader(ucfirst($what), [
    'icon' => $icon,
    'extraHtml' => "<link rel='shortcut icon' href=$icon><link rel=apple-touch-icon sizes=76x76 href=$icon><link rel=apple-touch-icon sizes=72x72 href=$icon><link rel=apple-touch-icon sizes=60x60 href=$icon>",
    'showaccount' => false]
  );
  infobox("Add a link to $what a WaiveCar", [
    '<b>Android:</b><ol><li>Tap the 3 dots in the upper right to get to the menu. <li>Scroll down and tap on "Add to Home screen"</ol>',
    '<b>iPhone:</b><ol><li>Tap on the share button which looks like a square with an upward arrow on it. <li>Scroll and tap "Add to Home Screen" which is a grey box with a plus sign.</ol>',
    "After you're done, press the back button and add any other functions you'd like."
  ], 'prompt');
}

function actionList($base, $list) {
  if($base[-1] != '=') {
    $base .= '/';
  }
?>
  <div class=action-list><? 
    foreach($list as $row) { 
      $klass = '';
      if(count($row) == 3) {
        $klass = " wid-${row[2]}";
      }
      ?><a class="btn<?= $klass ?>" href="<?= $base ?><?= $row[0] ?>"><?= $row[1] ?></a>
  <? } ?>
  </div>
<?
}

function isTagged($what) {
  $me = me();
  foreach($me['tagList'] as $tag) {
    if($tag['groupRole']['name'] === $what) {
      return true;
    }
  }
}

function isAdmin() {
  return isTagged('Administrator');
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

function showLocation($car, $opts = []) {
  $location = location($car);
  echo "<div class=map>";
  $opts['zoom'] = 13;
  if(!aget($opts, 'nomap')) {
    getMap([$car], $opts); 
    echo "<br/>";
  } else {
    echo "<h4>&#x1F5FA;</h4>";
  }
  echo "<a target=_blank href=\"//maps.google.com/maps/?q=${car['latitude']},${car['longitude']}+(${car['license']})\">$location</a>";
  echo "</div>";
}

function imageList($opts, $list) {
  $required = $opts['required'] ? 'required' : '';
  foreach($list as $row) { ?>
    <div class='image-upload'>
      <input name="<?= $row[1] ?>" type="file" accept="image/*;capture=camera" <?= $required ?> capture="camera" />
    </div>
  <? } 
}
