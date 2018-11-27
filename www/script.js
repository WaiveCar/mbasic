window.onload = function() {
  var allEl = document.getElementsByClassName('geo');
  for(var ix = 0; ix < allEl.length; ix++) {
    allEl[ix].href_ = allEl[ix].href;
    allEl[ix].onclick = function() {
      var mthis = this;
      loc(function(pos) { 
        window.location = mthis.href_ + ( mthis.href_.indexOf('?') === false ? '?' : '&' ) + 'latitude=' + pos.coords.latitude + '&longitude=' + pos.coords.longitude;
      }, function() {
        window.location = mthis.href_; 
      });
    }
    allEl[ix].removeAttribute('href');
  }

  var allEl = document.getElementsByClassName('needsjs');
  for(var ix = 0; ix < allEl.length; ix++) {
    allEl[ix].removeAttribute('href');
  }
}

function loc(cb, err) {
  err = err || function(){};

  return navigator.geolocation ? navigator.geolocation.getCurrentPosition(cb, err, {
      enableHighAccuracy: true,
      timeout: 5000,
      maximumAge: 0
    }) : err();
}

function nearest() {
  loc(function(pos){
    window.location = 'showcars.php?sort=near&lat=' + pos.coords.latitude + '&lng=' + pos.coords.longitude;
  }, function () {
    var zip = prompt("Hrmm, we can't seem to get the location from your device. If you tell us your zipcode, we can use that instead.");
    if(zip) {
      window.location = 'showcars.php?sort=near&zip=' + zip;
    }
  }); 
}
