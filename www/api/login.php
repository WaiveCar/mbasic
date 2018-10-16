<?php

include ('common.php');

$resJSON = post('/auth/login', $_POST);
if(!array_key_exists('token', $resJSON)) {
  if(!empty($resJSON['message'])) {
    $_SESSION['lasterror'] = [
      'title' => 'Unable to Log in',
      'message' => $resJSON['message']
    ];
  }
  header("Location: /index.php");
  exit;
}
$_SESSION['token'] = $resJSON['token'];
load('/showcars.php');
