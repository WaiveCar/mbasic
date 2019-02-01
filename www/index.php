<?
include('api/common.php');
getstate();
ob_start("sanitize_output");
?>
<!doctype html>
<html>
<head>
  <title>WaiveBasic Login</title>
  <link rel=stylesheet href=style.css>
  <link rel="shortcut icon" href=img/cl32.gif>
  <meta name=mobile-web-app-capable content=yes>
  <meta name=viewport content="width=device-width, initial-scale=1.0">
</head>
<body>
  <? showerror(); ?>
  <div class='box login'>
    <h1><img src=img/cl32.gif><span class=waive-green>Waive</span><small>basic</small></h1>
    <form action=api/login.php method=post>
      <input type=hidden name=referer value="<?=$_SERVER['HTTP_REFERER']?>">
      <input name=identifier placeholder=Email autofocus>
      <input type=password name=password placeholder=Password>
      <input type=submit value=Login class='btn wid-1'>
    </form>
  </div>

  <p>Normally log in through facebook? To use WaiveBasic, you'll need to <a href=//lb.waivecar.com/reset-password>give yourself a password</a>.</p>

</body>
</html>
