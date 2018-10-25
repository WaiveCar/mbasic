<?
include('api/common.php');
$time = intval(aget($_GET, 'time', 1));
$revenueList = curldo('/revenue/' . $time);
$revenueList = array_filter($revenueList, function($row) { 
  return $row['ttl'] > 0;
});
$ttl = array_sum(array_map(function($row) { return $row['ttl']; }, $revenueList));

$map = [
  'name' => function($row) { return "<a href=//lb.waivecar.com/users/${row['id']}>{$row['first_name']} {$row['last_name']}</a>"; },
  'status' => function($row) { return $row['status']; },
  'revenue' => function($row) { return sprintf("%.2f", $row['ttl']/100); },
  'tx count' => function($row) { return $row['charges']; },
  'credit' => function($row) { return sprintf("%.2f", $row['credit']/100); },
  'percent' => function($row) { global $ttl;return sprintf("%.2f",  100 *$row['ttl']/$ttl); }

];

?>
Last <?= $time ?>mo.
<table>
  <thead>
    <? foreach(array_keys($map) as $column) { 
      echo "<th>$column</th>";
    } ?>
  </thead>
  <tbody>
  <? foreach($revenueList as $row) {
     echo "<tr>";
     foreach($map as $key => $value) {
       echo "<td>" . $value($row) . "</td>";
     }
     echo "</tr>";
  }
  ?>
  </tbody>
</table> 

