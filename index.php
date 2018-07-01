<?
include('api/common.php');
getstate();
?>
<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>WaiveCar Login</title>
  <link rel="stylesheet" href="css/styles.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

</head>

<body>
  <? showerror(); ?>
  <div class='box login'>
    <H1 class='logo'><img src='img/circle-logo_32.gif'>Waive<span class='waive-green'>Car</span></H1>
    Login
    <form action="api/login.php" method="post">
      <input name="identifier" type="text" placeholder="Email" />
      <input name="password" type="password" placeholder="Password" />
      <input class='button wid-1' type="submit" value="Login">
    </form>

  </div>
</body>
</html>
