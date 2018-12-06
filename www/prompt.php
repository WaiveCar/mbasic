<?
list($path, $query) = explode('?', $_SERVER['HTTP_REFERER']);
$back = $path;
if(isset($_GET['b'])) {
  $back = $_GET['b'];
}
if(isset($_GET['f'])) {
  $path = $_GET['f'];
}
ob_start("sanitize_output");
?>
<!doctype html>
<html>
<head>
  <title><?= $_GET['t'] ?></title>
  <meta name=viewport content="width=device-width, initial-scale=1.0" />
  <link rel=stylesheet href=style.css>
</head>
<body>
  <div class='box prompt'>
    <h1><?= $_GET['t'] ?></h1>
    <div class=content>
      <div class=copy><?= $_GET['p'] ?></div>
      <form method=get action="<?= $path ?>" class=prompt>
        <? foreach($query as $key => $value) { ?>
          <input name="<?=$key?>" type=hidden value="<?= $value ?>">
        <? } ?>
        <input class=input type=text name="<?= $_GET['v'] ?>" autofocus required autocomplete="false">
        <div class=action-list>
          <a class='btn wid-2 ignore' href="<?= $back ?>">Cancel</a>
          <button class='btn wid-2'>Done</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
