
function checkPeoples(ele) {

  formPanel = document.querySelector("#form-panel");
  inps = formPanel.querySelectorAll("input");
  more = formPanel.querySelector("textarea");

  allEmpty = true;
  nextUrl = [];
  for (i = 0; i<3; i++) {
    if (inps[i].value.length >= 1) {
      allEmpty = false;
      nextUrl.push(inps[i].name + "=" + inps[i].value);
    }
  }
  if (more.value.length >= 1) {
    allEmpty = false;
    nextUrl.push("more=" + more.value);
  }

  console.log(nextUrl.join("&"));
  if (allEmpty) return; //Si tous casi vide (3 caractères ou moins) on cherche pas

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {

    if (this.readyState == 4 && this.status == 200) {
      console.log(this.responseText + "-");
      addSuggestion(this.responseText);
    }

  };

  xhttp.open("GET", "check.php?" + nextUrl.join("&"), true); //pas obligé de finir pour continuer
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send();

}

function addSuggestion(response) {

  var ids = response.split(',');

  var targeted_ele = document.getElementsByClassName("suggestion");
  var taille = targeted_ele.length;
  for (i = 0; i<taille; i++) { //suppression des ancienne suggestion du même type
    targeted_ele[0].parentElement.removeChild(targeted_ele[0]);
  }

  var bgs = ["#1abc9c", "#16a085", "#2ecc71", "#27ae60", "#3498db", "#2980b9", "#9b59b6", "#8e44ad", "#f1c40f", "#f39c12", "#e74c3c", "#c0392b"];

  if (response.length > 0) {
    for (var i = 0; i<ids.length; i++) {

      if (ids[i].includes("r")) {

        ajax("getInfosReal.php?id=" + ids[i].substr(1), false, function(response) {

          var infos = response.split(",");
          if (infos.length < 3) return;
          if (infos[2] == "deja") return;

          var sugg = document.createElement("div");
          sugg.classList.add("suggestion"); //classe de style
          sugg.id = ids[i]; //id pour vérif que suggestion déjà existante
          sugg.setAttribute("title", "Clique pour ajouter ce membre à ton réseau");
          sugg.setAttribute("onmousedown", "addSuggestionToNetwork(this)");

          var visu = document.createElement("div");
          visu.classList.add("suggestion-visu");
          visu.classList.add("real");
          visu.textContent = infos[1].toUpperCase().substr(0, 1) + infos[0].toUpperCase().substr(0, 1);
          visu.style.backgroundColor = bgs[getRandomInt(bgs.length)];
          sugg.appendChild(visu);

          var title = document.createElement("div");
          title.classList.add("suggestion-title");
          title.textContent = infos[1] + " " + infos[0] + ", personne réelle";
          sugg.appendChild(title);

          document.getElementById("sugg-container").appendChild(sugg);

        });

      } else {

        ajax("getInfos.php?id=" + ids[i], false, function(response) {

          var infos = response.split(",");
          if (infos.length < 4) return;
          if (infos[4] == "deja") return;

          var sugg = document.createElement("div");
          sugg.classList.add("suggestion"); //classe de style
          sugg.id = ids[i]; //id pour vérif que suggestion déjà existante
          sugg.setAttribute("title", "Clique pour ajouter ce membre à ton réseau");
          sugg.setAttribute("onmousedown", "addSuggestionToNetwork(this)");

          var visu = document.createElement("div");
          visu.classList.add("suggestion-visu");
          visu.textContent = infos[1].toUpperCase().substr(0, 1) + infos[0].toUpperCase().substr(0, 1);
          visu.style.backgroundColor = bgs[getRandomInt(bgs.length)];
          sugg.appendChild(visu);

          var title = document.createElement("div");
          title.classList.add("suggestion-title");
          title.textContent = infos[1] + " " + infos[0] + ", " + infos[2] + " - " + infos[3];
          sugg.appendChild(title);

          document.getElementById("sugg-container").appendChild(sugg);

        });

      }

    }
  }


}

function addSuggestionToNetwork(ele) {

  //But: chercher si dans session ['ele.type'] contient bien id. Si oui UPDATE
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {

    if (this.readyState == 4 && this.status == 200) {
      console.log(this.response);
      if (this.responseText == "cool") {
        successMsg();
      } else if (this.responseText.includes("deja")) {
        deja();
      }
    }

  };
  xhttp.open("GET", "addSuggestionToNetwork.php?id=" + ele.id);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send();

}

function updatePreview(ele, type) {

  var target = document.querySelector("#" + type + "-preview");
  if (ele.value.length == 0) {
    target.style.color = "#95a5a6";
    target.innerHTML = type;
  } else {
    target.style.color = "black";
    target.innerHTML = ele.value;
    if (type == "Prénom" || type == "Nom") {
      document.querySelector("#bubble-preview").innerHTML = document.querySelector("#Prénom-preview").innerHTML[0].toUpperCase()
        + document.querySelector("#Nom-preview").innerHTML[0].toUpperCase();
    }
  }

}

function focusForm(ele, where) {

  if (!where && where !== 0) {
    document.querySelector("textarea").innerHTML = ele.innerHTML;
    document.querySelector("textarea").focus();
  } else {
    var inps = document.querySelectorAll("input");
    inps[where].value = ele.innerHTML;
    inps[where].focus();
  }

}

function successMsg() {

  var ele = document.getElementById('success');
  ele.style.opacity = "1";

  setTimeout("document.getElementById('success').style.opacity = '0';", 3000);

}

function deja() {

  var ele = document.getElementById('deja');
  ele.style.opacity = "1";

  setTimeout("document.getElementById('deja').style.opacity = '0';", 3000);

}
