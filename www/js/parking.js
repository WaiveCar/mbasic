var Template = {},
    IMGBASE = 'https://s3.amazonaws.com/waivecar-prod/',
    ev = EvDa();

function penalize(el, booking){
  var ddList = el.parentNode.getElementsByTagName('select');
  var selected = ddList[0].value;
  
  if(selected != 'null') {
    $.post('/cite-user.php', {booking: booking, type: selected}, function(res) {
      alert("Cited user for " + ddList[0].innerHTML);
    });
  } else {
    alert("Nothing selected");
  }

}

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

function showTag(car, dir) {
  var row = payload[car],
      data = row._,
      el;

  dir = dir || 0;

  if(dir && row.results[data.ix + dir]) {
    data.ix += dir;
  }

  if(row.results[data.ix]) {
    el = row.results[data.ix];
    if(data.ix === 0) {
      data.prev.classList.add('disabled');
    } else {
      data.prev.classList.remove('disabled');
    }
    if(row.results[data.ix + 1]) {
      data.next.classList.remove('disabled');
    } else {
      data.next.classList.add('disabled');
    }
    if(el.hide) {
      data.guess.style.display = 'none';
    } else {
      data.guess.style.display = 'block';

      data.title.innerHTML = el.created_at.split('T')[0];
      data.addr.innerHTML = el.addr;
      data.dist.innerHTML = distance(row.latitude, row.longitude, el.lat, el.lng).toFixed(4) + "mi."
    }
    data.img.href = data.img.firstChild.src = IMGBASE + el.path;
  }
}

function generateCars() {
  if(!payload) {
    payload = {};
  }
  document.querySelectorAll(".car-sheet").forEach(function(item){
    var car = item.dataset.car;
    var node = item.querySelector('.guess-wrap');
    if(!(car in payload)) {
      payload[car] = {results: []};
    }
    payload[car]._ = {ix: 0};
    node.innerHTML = Template.archive();

    ['title','dist','addr','prev','next','guess'].forEach(function(row) {
      payload[car]._[row] = node.querySelector('.' + row);
    });
    payload[car]._.next.onclick = function() {
      showTag(car, 1);
    }
    payload[car]._.prev.onclick = function() {
      showTag(car, -1);
    }
    payload[car]._.img = item.querySelector('.img');
    if(payload[car]._.img.dataset.orig ) {
      payload[car].results.unshift({
        hide: true,
        path: payload[car]._.img.dataset.orig
      })
      
      showTag(car);
    }
  });
}
          
ev('load', function(){
  //loadTemplates();
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
