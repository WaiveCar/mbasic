<?php

include ('common.php');

$resJSON = post('/auth/login', $_POST);
if(!array_key_exists('token', $resJSON)) {
  if(!empty($resJSON['message'])) {
    $_SESSION['lasterror'] = $what['message'];
  }
  header("Location: /index.php");
}
$_SESSION['token'] = $resJSON['token'];
load('/showcars.php');
