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

window.onload = function() {
  var list = getPinList();
  console.log(list);
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

  

