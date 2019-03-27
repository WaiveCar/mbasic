<?php

include('api/common.php');
$what = $_GET['what'];

if ($what === 'zip') {
  prompt('Zip Code Lookup', 'Please enter your zip code', 'zip');
}
