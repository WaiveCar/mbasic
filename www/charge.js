window.onload = function () {
  let geo = !window.location.search;
  let gran = 600000;
  if(!geo) {
    let res = window.location.search.match(/at=(\d*)$/);
    geo = Math.floor(new Date()/gran) - +( res ? res[1] : 0 )
  }
  if(geo) {
    loc(function(pos){
      window.location = window.location.toString().split('?')[0] + "?" + [ 
        'latitude=' + pos.coords.latitude.toFixed(5), 
        'longitude=' + pos.coords.longitude.toFixed(5), 
        'at=' + Math.floor(new Date()/gran)
      ].join('&');
    }, function () {
      console.log("nope");
    })
  }
}
