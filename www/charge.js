window.onload = function (){
  if(!window.location.search) {
    loc(function(pos){
      window.location = window.location + "?lat=" + pos.coords.latitude + '&lng=' + pos.coords.longitude;
    }, function () {
      console.log("nope");
    })
  }
}
