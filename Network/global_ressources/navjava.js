
function navQuit() {

  var container = document.getElementById("nav-container")
  container.style.display = "none";


}

function navOpen() {

  var container = document.getElementById("nav-container")
  container.style.display = "block";

}

function disconnect() {

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {

    if (this.readyState == 4 && this.status == 200) {

      window.location = "../../";
    }

  }
  xhttp.open("GET", "../../phps/deconnect.php", true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send();

}
