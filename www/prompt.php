<?
list($path, $query) = explode('?', $_SERVER['HTTP_REFERER']);
$keys = parse_str($query);
?>
<!doctype html>
<html>
<head>
  <title><?= $_GET['prompt'] ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="style.css?<?= rand() ?>">
</head>
<body>
  <div class='box login'>
    <h1><?= $_GET['prompt'] ?></h1>
    <form method="get" action="<?= $path ?>" class='prompt'>
      <? foreach($query as $key => $value) { ?>
        <input name="<?=$key?>" type="hidden" value="<?= $value ?>">
      <? } ?>
      <input class='input' type="text" name="<?= $_GET['var'] ?>" autofocus>
      <input class='btn wid-1' type="submit" value="Done">
    </form>
  </div>
</body>
</html>
