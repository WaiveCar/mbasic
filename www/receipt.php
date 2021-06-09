<?
include('api/common.php');
$me = me();
db_incrstats('end');

if($me['booking_id']){
  complete($me['booking_id']);
}

doheader('Complete Booking');
?>
  <div class='box prompt'>
    <h1>Thanks for driving with us!</h1>
    <div class='content'>

      <p>
      You'll be getting a receipt by email for your booking today.
      </p>

      <a class='btn wid-1' href="showcars.php">Find Available Vehicles</a>
    </div>
  </div>
</body>
</html>
