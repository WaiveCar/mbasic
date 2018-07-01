<?php
date_default_timezone_set('UTC');
$db = new SQLite3("${_SERVER['DOCUMENT_ROOT']}/db/main.db");
$db->query('CREATE TABLE IF NOT EXISTS location_cache (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  latlng string,
  name string,
  created int
);');

function db_get($key) {
  global $db;
  $key = $db->escapeString($key);
  return $db->querySingle("select name from location_cache where latlng='$key'");
}

function db_set($key, $val) {
  global $db;
  $key = $db->escapeString($key);
  $val = $db->escapeString($val);

  $db->exec("insert into location_cache(latlng, name, created) values('$key', '$val', date('now'))");
}
