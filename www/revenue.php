<?
include('api/common.php');
$start = intval(aget($_GET, 'start', 1));
$end = intval(aget($_GET, 'end', 0));
$revenueList = curldo('/revenue/' . $start . '/' . $end);
$revenueList = array_filter($revenueList, function($row) { 
  return $row['ttl'] > 0;
});
$ttl = array_sum(array_map(function($row) { return $row['ttl']; }, $revenueList));

$map = [
  'name' => function($row) { return "<a href=//lb.waivecar.com/users/${row['id']}>{$row['first_name']} {$row['last_name']}</a>"; },
  'status' => function($row) { return $row['status']; },
  'revenue' => function($row) { return sprintf("%.2f", $row['ttl']/100); },
  'tx count' => function($row) { return $row['charges']; },
  '$/ride' => function($row) { return sprintf("%.2f", $row['ttl'] * .01/$row['charges']); },
  'credit' => function($row) { return sprintf("%.2f", $row['credit']/100); },
  'monthly' => function($row) { global $end,$start;return sprintf("%.2f", $row['ttl']/(100 * ($start - $end))); },
  'percent' => function($row) { global $ttl;return sprintf("%.2f",  100 *$row['ttl']/$ttl); }

];

$list = [];
foreach($revenueList as $raw) {
  $row = [];
  foreach($map as $key => $value) {
     $row[$key] = $value($raw);
  }
  $list[] = $row;
}
$sort = false;
if(isset($_GET['sort'])) {
  $sort = $_GET['sort'];
  uasort($list, function($a,$b) use ($sort) {
    return $a[$sort] < $b[$sort] ? 1 : -1;
  });
}


$order = array_keys($map);

?>
  <?= $start ?> - <?= $end ?>mo (total: <?= count($revenueList) ?>)
<table>
  <thead>
    <? foreach($order as $column) { 
      echo "<th><a href='?sort=$column'>" . (($sort === $column) ? '*' : ' ' ) . "$column</a></th>";
    } ?>
  </thead>
  <tbody>
  <? foreach($list as $row) {
     if($row['tx count'] < 30) {
       continue;
     }
     echo "<tr>";
     foreach($order as $key) {
       echo "<td>${row[$key]}</td>";
     }
     echo "</tr>";
  }
  ?>
  </tbody>
</table> 

