
function checkAlreadyLogin(ele) {

  if (ele.value == "") {

    document.getElementById("info-login").innerHTML = "";
    document.getElementById("input-login").style.backgroundColor = "white";

    return;
  }

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {

    if (this.readyState == 4 && this.status == 200) {

      textResult = document.querySelector("#info-login");
      if (this.responseText == "0") {
        textResult.style.color = "#2ecc71";
        textResult.innerHTML = "✨ Ce pseudo est PARFAIT !";
        ele.style.backgroundColor = "#2ecc71";
      } else if (this.responseText == 'Could not connect') {
        textResult.style.color = "#e74c3c";
        textResult.innerHTML = "💥 OUPS, pas de connexion";
        ele.style.backgroundColor = "#e74c3c";
      } else if (this.responseText == "Symbole") {
        textResult.style.color = "#e74c3c";
        textResult.innerHTML = "💥 OUPS, ce pseudo contient des symboles interdits (# ou ,)";
        ele.style.backgroundColor = "#e74c3c";
      } else {
        textResult.style.color = "#e74c3c";
        textResult.innerHTML = "💥 OUPS, ce pseudo est déjà prit";
        ele.style.backgroundColor = "#e74c3c";
      }

    }

  };
  xhttp.open("GET", "../../phps/verifAlreadyName.php?login=" + ele.value, true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send();

}

function successMsg(message) {

  var success = document.getElementById("success-msg");
  success.innerHTML = message;
  success.style.opacity = "1";

  setTimeout(function() {
    success.style.opacity = "0";
  }, 3000);

}

function getUserId() {

  var id;

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {

    if (this.readyState == 4 && this.status == 200) {
      id = this.responseText;
    }

  }
  xhttp.open("GET", "getId.php", false) //On attend
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send();

  return id;
}

function downloadPref() {

  var ele = document.createElement("a");
  var finaltxt = "";
  var userId = getUserId();

  for (var i = 0; i<window.localStorage.length; i++) {
    var parts = window.localStorage.key(i).split("-");
    if (parts.length > 1 && parts[0] == userId) {
        finaltxt += window.localStorage.key(i) + "=" + window.localStorage.getItem(window.localStorage.key(i)) + ";\n";
    }
  }

  //création de l'élément
  ele.setAttribute("href", 'data:text/plain;charset=utf-8,' + encodeURIComponent(finaltxt));
  ele.setAttribute("download", "Vos Préférences");
  ele.innerHTML = "download";

  //magouille
  document.body.appendChild(ele);
  ele.click();
  document.body.removeChild(ele);

  successMsg('🎉 Génération du fichier "sauveur" réussit !');

}

function reinit() {

  window.localStorage.clear();
  successMsg("🎉 Vous avez bien réinitialiser toutes vos Préférences");
}

function readPref(ele) {

  var file = ele.target.files[0];
  if (file == null) {
    return;
  }

  var reader = new FileReader();
  reader.onload = function(event) {

    var text = event.target.result;
    var cookies = text.split(";\n");
    for (var i = 0; i<cookies.length; i++) {
      var cook = cookies[i];
      var parts = cook.split("=");
      window.localStorage.setItem(parts[0], parts[1]);
    }
    successMsg("🎉 Vous avez bien chargé vos Préférences !")

  }
  reader.readAsText(file);

}

function showSection(ids) {

  for (r of document.querySelectorAll(".toRemove")) {
    r.style.display = "none";
  }
  for (e of document.querySelectorAll(".section")) {
    e.style.display = "none";
  }
  for (id of ids) {
    document.querySelector("#" + id).style.display = "flex";
  }

}
