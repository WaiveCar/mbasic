<?
include_once('api/common.php');
ob_start("sanitize_output");
$klass = aget($_GET, 'o.klass');
$title = $_GET['t'];
doheader($title, ['showaccount' => false]);
?>
  <div class='box prompt <?= $klass ?>'>
    <h1 class='title'><?= $title ?></h1>
    <div class='content'>
      <div class=copy><?= $_GET['p'] ?></div>
      <div class=action>
        <? 
          foreach($_GET['b'] as $row) { 
            $klass = '';
            if(count($row) > 2) {
              $klass = $row[2];
            }
            echo "<a class='btn $klass' href='${row[1]}'>${row[0]}</a>";
          }
        ?> 
      </div>
    </div>
  </div>
</body>
</html>
