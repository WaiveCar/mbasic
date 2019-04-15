<?php

include('api/common.php');
$what = $_GET['what'];
$verb = $_SERVER['REQUEST_METHOD'];

if ($what === 'zip') {
  prompt('Zip Code Lookup', 'Please enter your zip code', 'zip');
}
if ($what === 'address') {
  if($verb !== 'GET') {
    $me = me();
    $source = $_POST['source'];
    unset($_POST['source']);
    $res = put('licenses', $_POST);
    load($source);
  } else {

    widget('form', [
      'inline' => true,
      'verb' => 'post',
      'title' => 'Enter Home Address',
      'help' => 'In an effort to improve service, please tell us your home address before continuing.',
      'options' => [
        ['type' => 'hidden', 'name' => 'source', 'value' => "/", 'required' => true],
        ['name' => 'street1', 'label' => 'Address', 'required' => true],
        ['name' => 'street2', 'label' => ''],
        ['name' => 'city', 'size' => 50, 'required' => true],
        ['name' => 'state', 'size' => 20, 'required' => true],
        ['name' => 'zip', 'size' => '20', 'required' => true]
      ]
    ]);
  }
}
