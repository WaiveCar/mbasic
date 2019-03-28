<?php

include('api/common.php');
$what = $_GET['what'];
$verb = $_SERVER['REQUEST_METHOD'];

if ($what === 'zip') {
  prompt('Zip Code Lookup', 'Please enter your zip code', 'zip');
}
if ($what === 'address') {
  if($verb === 'post') {
    $source = $_POST['name'];
    unset($_POST['name']);
    $res = put('licenses/unspecificUpdate', $_POST);
    var_dump($res);
  } else {

    widget('form', [
      'inline' => true,
      'verb' => 'post',
      'title' => 'Enter Home Address',
      'help' => 'In an effort to improve service, please tell us your home address before continuing.',
      'options' => [
        ['type' => 'hidden', 'name' => 'source', 'value' => aget($_SERVER, 'HTTP_REFERER')],
        ['name' => 'address1', 'label' => 'Address'],
        ['name' => 'address2', 'label' => ''],
        ['name' => 'city', 'size' => 50],
        ['name' => 'state', 'size' => 20],
        ['name' => 'zip', 'size' => '20']
      ]
    ]);
  }
}
