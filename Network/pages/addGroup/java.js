
function createBubble(bubbleID) {

  var xhttp = new XMLHttpRequest();

  xhttp.onreadystatechange = function() {

    if (this.readyState == 4 && this.status == 200) {

      var infos = this.responseText.split(",");
      if (infos.length < 4) return;
      var ele = document.createElement("div");
      ele.classList.add("bubble");
      ele.innerHTML = "<b>" + infos[1] + " " + infos[0] + "</b> <i>" + infos[2] + "</i></br>" + infos[3];
      ele.setAttribute("nom", infos[0]);
      ele.setAttribute("prenom", infos[1]);
      ele.setAttribute("job", infos[2]);
      ele.setAttribute("more", infos[3]);
      ele.setAttribute("onclick", "addToGroup(this)");
      ele.id = bubbleID;

      document.getElementById("container").appendChild(ele);

    }

  };
  xhttp.open("GET", "getInfos.php?id=" + bubbleID, false);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send();

}

function initListe(net) {

  var ids = net.split(",");
  for (var id of ids) {

    if (!id.includes(":") && id != "-1") createBubble(id);

  }

}

function recherche() {

  var ele = document.getElementById("recherche");
  var bubbles = document.getElementsByClassName("bubble");
  var tofind = ele.value.toUpperCase();

  for (var b of bubbles) {

    if (b.classList.contains("find")) b.classList.remove("find");

    if (ele.value != "") {
      if (b.getAttribute("nom").toUpperCase().includes(tofind)
        || b.getAttribute("prenom").toUpperCase().includes(tofind)
        || b.getAttribute("job").toUpperCase().includes(tofind)
        || b.getAttribute("more").toUpperCase().includes(tofind)) {

          b.classList.add("find");

        }
    }


  }

}

function changeColor(ele) {

  var test = document.getElementById("test");
  test.style.backgroundColor = ele.style.backgroundColor;
  document.getElementById("input-color").value = ele.style.backgroundColor;

}

function changeTitle(ele) {

  if (ele.value != "") document.getElementById("test").innerHTML = ele.value;
  else document.getElementById("test").innerHTML = "Membre du Groupe";

}

function addToGroup(ele) {

  var inputG = document.getElementById("input-ingroup");

  if (ele.classList.contains("toGroup")) { //Retirer du groupe

    var groupid = inputG.value.split(":");
    if (groupid.includes(ele.id)) groupid.splice(groupid.indexOf(ele.id), 1);
    inputG.value = groupid.join(":");

    ele.classList.remove("toGroup");
    document.getElementById("container").appendChild(ele);

  } else { //Ajouter dans le groupe

    if (inputG.value == "") inputG.value = ele.id;
    else inputG.value += ":" + ele.id;

    ele.classList.add("toGroup");
    document.getElementById("container2").appendChild(ele);

  }

}

function msg(message) {

  var text = document.getElementById("msg")
  text.style.opacity = "1";
  text.innerHTML = message;

  setTimeout(function() {
    text.style.opacity = "0";
  }, 3000);

}
