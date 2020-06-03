
document.getElementById('Network').addEventListener("mousedown", function() {

    var net = document.getElementById('Network');

    if (net.classList.contains("liste")) return;

    var X_start = event.clientX - net.getAttribute("left");
    var Y_start = event.clientY - net.getAttribute("top");

    function move() {
      net.setAttribute("left", event.clientX - X_start);
      net.setAttribute("top", event.clientY - Y_start);
      net.style.left = net.getAttribute('left') + "px";
      net.style.top = net.getAttribute('top') + "px";
    }

    net.addEventListener("mousemove", move, true);

    net.addEventListener("mouseup", function() {

      net.removeEventListener("mousemove", move, true);

    }, true);

    net.addEventListener("mouseleave", function() {
      net.removeEventListener("mousemove", move, true);
    })

}, true);
