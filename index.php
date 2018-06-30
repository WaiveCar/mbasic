<?
include('common.php');
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
  <form action="login.php" method="post">
    <input id="identifier" type="text" placeholder="Email" />
    <input id="password" type="password" placeholder="password" />
    <input type="submit">
  </form>

  <script src="js/scripts.js"></script>
</body>
</html>
