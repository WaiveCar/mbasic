var Template = {},
    ev = EvDa();

function distance(lat1, lon1, lat2, lon2) {
 if ((lat1 == lat2) && (lon1 == lon2)) {
  return 0;
 }
 else {
  var radlat1 = Math.PI * lat1/180;
  var radlat2 = Math.PI * lat2/180;
  var theta = lon1-lon2;
  var radtheta = Math.PI * theta/180;
  var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
  if (dist > 1) {
   dist = 1;
  }
  dist = Math.acos(dist);
  dist = dist * 180/Math.PI;
  dist = dist * 60 * 1.1515 * 1.609344;
  return dist;
 }
}

function getPinList(){
  try{
    return JSON.parse(localStorage['pin']);
  } catch(ex) {
  }
  return [];
}
function setPinList(list) {
  localStorage['pin'] = JSON.stringify(list);
}


function toggle(id) {
  var list = getPinList();
  var container = document.getElementById('booking-' + id);
  if(list.indexOf(id) == -1) {
    list.push(id);
    setPinList(list);
    container.classList.add("pinned");
  } else {
    list = list.filter(function(m) { 
      return m != id;
    });
    setPinList(list);
    container.classList.remove('pinned');
  }
}

  
function loadTemplates() {
  document.querySelectorAll("#template > *").forEach(function(item){
    var id = item.id.slice(2); 
    Template[id] = _.template(item.innerHTML);
    console.log(">> template " + id);
  });
}

function generateCars() {
  document.querySelectorAll(".car-sheet").forEach(function(item){
    var car = item.dataset.car;
    var node = item.querySelector('.guess-wrap');
    node.innerHTML = Template.archive({date: 'a', place: 'b'});
  });
}
          
ev('load', function(){
  loadTemplates();
  generateCars();
  var list = getPinList();
  var filtered = [];
  if(list) {
    list.forEach(function(row) {
      var container = document.getElementById('booking-' + row);
      if(container) {
        filtered.push(row);
        console.log("adding a classname to " + row);
        container.classList.add("pinned");
      }
        console.log("failed adding a classname to " + row);
    });
    setPinList(filtered);
  }
});

window.onload = function() {
  ev.fire('load');
}
