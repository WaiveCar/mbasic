<?
include('api/common.php');

$me = me();
if($me['booking_id']){
  complete($me['booking_id']);
}

doheader('Complete Booking');
?>
  <div class='box'>
    <h1>Thanks for using WaiveCar</h1>

    <p>
    You'll be getting a receipt by email for your booking today.
    </p>

    <a class='button wid-1' href="showcars.php">Find Available WaiveCars</a>
  </div>

</body>
</html>
