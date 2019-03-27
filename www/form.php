<?
include('api/common.php');
list($path, $query) = explode('?', $_SERVER['HTTP_REFERER']);
$title = aget($_GET, 't', 'Form');
$help = aget($_GET, 'p', 'Please fill out the form');
$back = $path;
/*
if(isset($_GET['b'])) {
  $back = $_GET['b'];
}
if(isset($_GET['f'])) {
  $path = $_GET['f'];
}
 */
ob_start("sanitize_output");
$title = "Enter Home Address";
$help = "In an effort to improve service, please tell us your home address before continuing.";
$options = [
  ['name' => 'address1', 'label' => 'Address'],
  ['name' => 'address2', 'label' => ''],
  ['name' => 'city', 'size' => 50],
  ['name' => 'state', 'size' => 20],
  ['name' => 'zip', 'size' => '20']
];

?>
<!doctype html>
<html>
<head>
  <title><?= $title ?></title>
  <meta name=viewport content="width=device-width, initial-scale=1.0" />
  <link rel=stylesheet href=style.css>
</head>
<body>
  <div class='box prompt'>
    <h1><?= $title ?></h1>
    <div class=content>
      <div class=copy><?= $help ?></div>
      <form method=get action="<?= $path ?>" class=prompt>
        <? foreach($query as $key => $value) { ?>
          <input name="<?=$key?>" type=hidden value="<?= $value ?>">
        <? } ?>
        <? 
        $ix = 0;
        foreach($options as $row) { 
          $name = $row['name'];
          $label = aget($row, 'label', ucfirst($name));
          $size = aget($row, 'size', 100);
          $style = "style=width:$size%";
          if($size >= 100) {
            $ix = 0;
          } else {
            if($ix > 0) {
              $margin = 5;
              $style .= ";margin-left:$margin%";
              $ix += $margin;
            }

            $ix += $size;
          }

        ?>
          <div class='input-container' <?= $style ?>>
            <? if (strlen($label) > 0) { ?>
              <label for="<?= $name ?>"><?= $label ?></label>
            <? } ?>
            <input class=input type=text name="<?= $name ?>" autofocus required autocomplete="off">
          </div>
        <? } ?>
        <div class=action-list>
          <a class='btn wid-2 ignore' href="<?= $back ?>">Cancel</a>
          <input type='submit' value='Done' class='btn wid-2'>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
