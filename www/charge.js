window.onload = function () {
  let geo = !window.location.search;
  if(!geo) {
    let res = window.location.search.match(/at=(\d*)$/);
    geo = new Date() - +( res ? res[1] : 0 ) > 600000;
  }
  if(geo) {
    loc(function(pos){
      window.location = window.location + "?lat=" + pos.coords.latitude + '&lng=' + pos.coords.longitude + "&at=" + +new Date();
    }, function () {
      console.log("nope");
    })
  }
}
