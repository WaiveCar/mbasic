<?
ob_start();
include('common.php');
$action = $_GET['action'];
if(isset($_GET['action'])) {
  if($_GET['action'] == 'hide') {
    $_SESSION['hide'] = true;
    goback();
  }
  if($_GET['action'] == 'show') {
    unset($_SESSION['hide']);
    goback();
  }
}

