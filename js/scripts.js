window.onload = function() {
  var allEl = document.getElementsByClassName('needsjs');
  for(var ix = 0; ix < allEl.length; ix++) {
    allEl[ix].className = allEl[ix].className.replace(/needsjs/, '');
  }
}
function nearest() {
  navigator.geolocation.getCurrentPosition(function(pos) {
    
    window.location = 'showcars.php?sort=near&lat=' + pos.coords.latitude + '&lng=' + pos.coords.longitude;
  }, function(err) {
    var zip = prompt("Hrmm, we can't seem to get the location from your device. If you tell us your zipcode, we can use that instead.", "ex: 90210");
    if(zip) {
      window.location = 'showcars.php?sort=near&zip=' + zip;
    }
  }, {
    enableHighAccuracy: true,
    timeout: 5000,
    maximumAge: 0
  });
}
