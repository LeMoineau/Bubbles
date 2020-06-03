
var acts = ['donner un CV', 'donner une lettre de motivation'];

function updateAction(ele) {

  ele.style.width = ((ele.value.length + 1) * 7) + "px";

}

function initListes(net) {

  //INITIALISATION DES PERSONNES / BULLES
  var persons = document.getElementById("personsList");
  persons.innerHTML = "";

  for (var b of net.split(",")) { //On regarde toutes les bulles

    if (b != "-1") {
      for (var bubble of b.split(":")) { //Et dans les groupes s'il y en a

        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {

          if (this.readyState == 4 && this.status == 200) {

            persons.innerHTML += "<option value='" + this.responseText + "'></option>";

          }

        }
        xhttp.open("GET", "phps/getName.php?bubble=" + bubble, true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send();

      }
    }

  }

  //INITIALISATION DES TACHES
  var actions = document.getElementById("actionsList");
  actions.innerHTML = "";

  for (var a of acts) {

    actions.innerHTML += "<option value='" + a + "'></option>";

  }

}
