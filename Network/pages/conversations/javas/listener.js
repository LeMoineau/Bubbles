
document.getElementById("conv-nav").onmousewheel = function(event) {

  var delta = event.wheelDelta;
  var ele = document.getElementById("conv-nav");

  if (delta < 0) {
    ele.scrollTop += 40;
  } else {
    ele.scrollTop -= 40;
  }

}

var toClosed = setInterval(function() {

  var container = document.getElementById("conv-msgs-container");
  var id_selected = container.getAttribute("conv-selected");

  if (id_selected != null) {
    openChat(document.getElementById(id_selected), true);
    //console.log("update " + id_selected);
  }

}, 1000);
