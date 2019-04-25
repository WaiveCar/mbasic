<?php

include ('common.php');

$referer = aget($_POST, 'referer');
unset($_POST['referer']);

$resJSON = post('/auth/login', $_POST);
if($resJSON === 'Not Found') {
  $_SESSION['lasterror'] = [
    'title' => 'Unable to Contact Server',
    'message' => 'The server appears to be down. Please try again shortly'
  ];
} else if(!array_key_exists('token', $resJSON)) {
  if(!empty($resJSON['message'])) {
    if(strpos($resJSON['message'], 'authentication token provided')) !== false) {
      session_destroy();
      $resJSON['message'] = 'Please try again';
    }

    $_SESSION['lasterror'] = [
      'title' => 'Unable to Log in',
      'message' => $resJSON['message']
    ];
  }
} else if(array_key_exists('code', $resJSON)) {
  session_destroy();
} else {
  $_SESSION['token'] = $resJSON['token'];
  if($referer) {
    load($referer);
  } else {
    load('/showcars.php');
  }
  exit;
}
header("Location: /index.php");
exit;
