<?
include('api/common.php');
$time = intval(aget($_GET, 'time', 1));
$revenueList = curldo('/revenue/' . $time);
$map = [
  'name' => function($row) { return "<a href=//lb.waivecar.com/users/${row['id']}>{$row['first_name']} {$row['last_name']}</a>"; },
  'status' => function($row) { return $row['status']; },
  'revenue' => function($row) { return sprintf("%.2f", $row['ttl']/100); },
  'tx count' => function($row) { return $row['charges']; },
  'credit' => function($row) { return sprintf("%.2f", $row['credit']/100); }
];

?>
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

