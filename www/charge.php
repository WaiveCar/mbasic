<?
include('api/common.php');
$me = me();
?>
<!doctype html>
<html>
<head>
  <title><?= $name ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="/style.css" />
</head>
<body>
<?
$id = $_GET['charger'];
$portSearch = $_GET['port'];

$locationList = get('/locations');
// yeah all CS history is telling me this is bad ... oh well.
foreach($locationList as $m) {
  if($m['type'] === 'chargingStation') {
    if($m['id'] === $id) { 
      $charger = $m;
      break;
    }
    if($portSearch) {
      foreach($m['portList'] as $attempt) {
        if($attempt['name'] === $portSearch) {
          infoBox("Starting " . $attempt['name'], "The charge should begin shortly. Feel free to use the back button to navigate away from this message.");
          charge($m['id'], $attempt['id']);
          exit;
        }
      }
    }
  }
}

if(!$charger) {
  echo "Woops, something went wrong.";
  exit;
}
$chargerSet = ['fast' => [], 'slow' => []];
foreach($charger['portList'] as $port) {
  $chargerSet[$port['type']][] = $port;
}
$name = $charger['name'];
?>
  <div class='box prompt'>
    <h1><?= $name ?></h1>
    <div class='content'>
      <div class='copy'>
      1. After parking and plugging in the charge cable to the WaiveCar, find the machine's ID number (usually on a sticker below or above the screen).
      <p>2. Tap on the icon with the right code below and your charge should begin shortly!</p></div>

    <?
     foreach(['fast', 'slow'] as $type) {
       $list = $chargerSet[$type];
       echo "<div class='port-list'>";
       echo "<span>".ucfirst($type)."</span>";

       if(count($list) > 0) {
         foreach($chargerSet[$type] as $port) {
           echo "<a href=/charge.php?port=${port['name']} class=geo><img src=/img/charger-{$port['type']}.png>${port['name']}</a>";
         }
       } else {
         echo "<span><em>None</em></span>";
       }
       echo '</div>';
     }
     ?> 
    </div>
  </div>
</body>
  <script src=/script.js></script>
</html>
