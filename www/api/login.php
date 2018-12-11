<?php

include ('common.php');

$resJSON = post('/auth/login', $_POST);
if($resJSON === 'Not Found') {
  $_SESSION['lasterror'] = [
    'title' => 'Unable to Contact Server',
    'message' => 'The server appears to be down. Please try again shortly'
  ];
} else if(!array_key_exists('token', $resJSON)) {
  if(!empty($resJSON['message'])) {
    $_SESSION['lasterror'] = [
      'title' => 'Unable to Log in',
      'message' => $resJSON['message']
    ];
  }
} else {
  $_SESSION['token'] = $resJSON['token'];
  load('/showcars.php');
  exit;
}
header("Location: /index.php");
exit;
