<?php
date_default_timezone_set('UTC');
$dbPath = "${_SERVER['DOCUMENT_ROOT']}/../db/main.db";
if(!file_exists($dbPath)) {
  touch($dbPath);
}
$db = new SQLite3($dbPath);
$db->exec('CREATE TABLE IF NOT EXISTS location_cache (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  latlng string,
  name string,
  created int
);');
$db->exec('CREATE TABLE IF NOT EXISTS users (
  user_id integer primary key, 
  car integer default 0, 
  book integer default 0, 
  dash integer default 0, 
  end integer default 0, 
  last datetime default current_timestamp, 
  first datetime default current_timestamp
);');

function db_incrstats($what) {
  global $db;
  $me = me();
  $id = $me['id'];

  @$db->exec("insert into users(user_id) values($id)");
  $db->exec("update users set $what = $what + 1, last = current_timestamp where user_id = $id");
}

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
