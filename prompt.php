<?
list($path, $query) = explode('?', $_SERVER['HTTP_REFERER']);
$keys = parse_str($query);
?>
<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= $_GET['prompt'] ?></title>
  <link rel="stylesheet" href="css/styles.css?<?= rand() ?>">
</head>
<body>
  <div class='box'>
  <h1><?= $_GET['prompt'] ?></h1>
  <form method="get" action="<?= $path ?>" class='prompt'>
    <?  foreach($query as $key => $value) { ?>
      <input name="<?=$key?>" type="hidden" value="<?= $value ?>">
    <? } ?>
    <center>
      <input class='input' type="text" name="<?= $_GET['var'] ?>" autofocus>
    </center>
    <p align="center">
      <input type="submit" class='button wid-2 danger' value="Cancel">
      <input type="submit" class='button wid-2' value="Done">
    </p>
  </form>
</body>
</html>