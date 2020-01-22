window.onload = function (){
  if(!window.location.search) {
    loc(function(pos){
      window.location = window.location + "?lat=" + pos.coords.latitude + '&lng=' + pos.coords.longitude + "&at=" + (new Date());
    }, function () {
      console.log("nope");
    })
  }
}
