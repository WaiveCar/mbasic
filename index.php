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

</head>

<body>
  <? showerror(); ?>
  Login
  <form action="api/login.php" method="post">
    <input name="identifier" type="text" placeholder="Email" />
    <input name="password" type="password" placeholder="password" />
    <input type="submit">
  </form>

  <script src="js/scripts.js"></script>
</body>
</html>
