<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= $_GET['prompt'] ?></title>
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/styles.css?<?= rand() ?>">
</head>
<body>
  <div class='box'>
  <h1><?= $_GET['t'] ?></h1>
  <p><?= $_GET['p'] ?></p>
  <p align="center">
    <? 
      foreach($_GET['o'] as $row) { 
        $klass = '';
        if(count($row) > 2) {
          $klass = $row[2];
        }
        echo "<a class='button $klass' href='${row[1]}'>${row[0]}</a>";
      }
    ?> 
  </p>
</body>
</html>