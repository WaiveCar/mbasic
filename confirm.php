<!doctype html>
<html>
<head>
  <title><?= $_GET['t'] ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/styles.css" />
</head>
<body>
  <div class='box prompt'>
    <h1><?= $_GET['t'] ?></h1>
    <div class='content'>
      <div class='copy'><?= $_GET['p'] ?></div>
      <div class='actions'>
        <? 
          foreach($_GET['o'] as $row) { 
            $klass = '';
            if(count($row) > 2) {
              $klass = $row[2];
            }
            echo "<a class='btn $klass' href='${row[1]}'>${row[0]}</a>";
          }
        ?> 
      </p>
    </div>
  </div>
</body>
</html>
