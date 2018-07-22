window.onload = function() {
  var allEl = document.getElementsByClassName('needsjs');
  for(var ix = 0; ix < allEl.length; ix++) {
    allEl[ix].setAttribute('_href', allEl[ix].getAttribute('href'));
    allEl[ix].removeAttribute('href');
  }
}

function getZip() {
  window.location = link.getAttribute('_href');
}

function nearest(el) {
  window.link = el;

  if(!navigator.geolocation) {
    return getZip();
  }

  navigator.geolocation.getCurrentPosition(function(pos) {
    window.location = 'showcars.php?sort=near&lat=' + pos.coords.latitude + '&lng=' + pos.coords.longitude;
  }, getZip, {
    enableHighAccuracy: true,
    timeout: 5000,
    maximumAge: 0
  });
}
