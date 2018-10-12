<?
include('api/common.php');

$me = me();
if($me['booking_id']){
  complete($me['booking_id']);
}

doheader('Complete Booking');
?>
  <div class='box prompt'>
    <h1>Thanks for Waiving!</h1>
    <div class='content'>

      <p>
      You'll be getting a receipt by email for your booking today.
      </p>

      <a class='btn wid-1' href="showcars.php">Find Available WaiveCars</a>
    </div>
  </div>
</body>
</html>
