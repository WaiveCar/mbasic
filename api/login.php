<?php

include ('common.php');

$resJSON = post('/auth/login', $_POST);
if(!array_key_exists('token', $resJSON)) {
  header("Location: index.php?badlogin=true");
}
$_SESSION['token'] = $resJSON['token'];
header("Location: /showcars.php");
