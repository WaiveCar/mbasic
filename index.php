<?
include('api/common.php');
getstate();
?>
<!doctype html>
<html>
<head>
  <title>WaiveCar Basic Login</title>
  <link rel="stylesheet" href="css/styles.css">
  <link rel="shortcut icon" href="img/circle-logo_32.gif">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="The free electric car sharing program. Join the Waive!" />
</head>

<body>
  <? showerror(); ?>
  <div class='box login'>
    <h1><img src='img/circle-logo_32.gif'>Waive<span class='waive-green'>Car</span> <small>basic</small></h1>
    <form action="api/login.php" method="post">
      <input name="identifier" type="text" placeholder="Email" />
      <input name="password" type="password" placeholder="Password" />
      <button class='btn wid-1'>Login</button>
    </form>
  </div>

</body>
</html>
