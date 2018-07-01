window.onload = function() {
  var allEl = document.getElementsByClassName('needsjs');
  for(var ix = 0; ix < allEl.length; ix++) {
    allEl[ix].className = allEl[ix].className.replace(/needsjs/, '');
  }
}
function nearest() {
  navigator.geolocation.getCurrentPosition(function(pos) {
    window.location = 'showcars.php?sort=near&lat=' + pos.latitude + '&lat=' + pos.longitude;
  }, function(err) {
    alert("Unable to get your location. Please check your security settings!\n Code: " + err.code + "\n Message: " + err.message);
  }, {
    enableHighAccuracy: true,
    timeout: 5000,
    maximumAge: 0
  });
}
