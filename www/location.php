<?
include('api/common.php');
if(isset($_GET['latitutde'])) {
  echo location($_GET);
  // this format is multi=[[lat,lng],[lat,lng]...]
} else if(isset($_GET['multi'])) {
  $list = json_decode($_GET['multi'], true);
  $res = [];
  foreach($list as $row) {
    $res[] = location([ 'latitude' => $row[0], 'longitude' => $row[1] ]);
  }
  echo json_encode($res);
}


