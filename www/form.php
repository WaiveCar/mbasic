<?
include_once('api/common.php');

$title = aget($_GET, 't|title', 'Form');
$help = aget($_GET, 'h|help', 'Please fill out the form');
$options = aget($_GET, 'o|options');
$verb = aget($_GET, 'verb', 'get');
$path = aget($_GET, 'a|action', $_SERVER['REQUEST_URI']);
$back = aget($_GET, 'b|back');

ob_start("sanitize_output");

?>
<!doctype html>
<html>
<head>
  <title><?= $title ?></title>
  <meta name=viewport content="width=device-width,initial-scale=1.0" />
  <link rel=stylesheet href=/style.css>
</head>
<body>
  <div class='box prompt'>
    <h1><?= $title ?></h1>
    <div class=content>
      <div class=copy><?= $help ?></div>
      <form method=<?= $verb ?> action="<?= $path ?>" class=prompt>
        <? 
        $ix = 0;
        foreach($options as $row) { 
          if(aget($row, 'type') === 'hidden') {
            echo "<input type=hidden name='${row['name']}' value='${row['value']}' />";
            continue;
          }
          $name = $row['name'];
          $label = aget($row, 'label', ucfirst($name));
          $size = aget($row, 'size', 100);
          $style = "style=width:$size%";
          $required = aget($row, 'required') ? 'required' : '';
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
          <div class=input-container <?= $style ?>>
            <? if (strlen($label) > 0) { ?>
              <label for=<?= $name ?>><?= $label ?></label>
            <? } ?>
            <input name=<?= $name ?> <?= $required ?>>
          </div>
        <? } ?>
        <div class=action-list>
          <? 
            $klass = '';
            if (!empty($back)) { 
              $klass = ' wid-2';
              echo "<a class='btn wid-2 ignore' href='$back'>Cancel</a>";
            } 
          ?>
          <input type=submit value=Done class='btn<?= $klass?>'>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
