
function traitementTexte(str) {

  var beginWord = 0;
  while (beginWord < str.length && str[beginWord] == " ") {
    beginWord++;
  }

  var endWord = str.length-1;
  for (i = beginWord; i<str.length; i++) {
    if (str[i] == " ") {
      j = i;
      while (j < str.length && str[j] == " ") {
        j++;
      }
      if (j == str.length) {
        endWord = i-1;
        break;
      }
    }

  }

  return str.substr(beginWord, endWord-beginWord+1);

}

function verifName(ele) {

  var login = ele.value;
  var textResult = document.getElementById('VerifNameText');

  //Faire traitement de texte pour prendre tout sans espace de fin (ex: _Pierrot__ doit etre egal à Pierrot où _ = espace)
  login = traitementTexte(login);
  ele.value = login;

  if (ele.value.length == 0 || login == " ") {

    ele.style.backgroundColor = "transparent";
    textResult.innerHTML = "";
    return;

  }

  xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {

    if (this.readyState == 4 && this.status == 200) {

      //RENVOIE NOMBRE DE LIGNE CORRESPONDANTE
      //alert(this.responseText);
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

  }
  xhttp.open("GET", "phps/verifAlreadyName.php?login=" + login, true);
  xhttp.setRequestHeader('Content-type', "application/x-www-form-urlencoded");
  xhttp.send();

}

function mdpOublie() {

  var info = document.getElementById("infoConnexionText");
  if (document.getElementById("loginInput").value.length == 0) {

    info.innerHTML = "Veuillez entrer votre pseudo ou votre adresse mail pour retrouver votre mot de passe.";
    info.style.color = "black";
    return;

  }

  if (confirm("En continuant, votre mot de passe sera modifié. Un mail vous sera alors envoyé pour que vous le recupériez.")) {

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {

      if (this.readyState == 4 && this.status == 200) {

        if (this.responseText == "pas de mail") {
          info.innerHTML = "💥 MINCE ! Nous n'avons pas trouvé de mail renseigné pour votre compte... Dans ce cas, nous vous prions de contacter"
            + " notre équipe à l'adresse suivante : pierre.faber@efrei.net";
          info.style.color = "#e74c3c";
        } else if (this.responseText == "success") {
          info.innerHTML = "✨ Ouf ! Un nouveau mot de passe vous a été envoyé sur votre boite mail (à l'adresse que vous aviez renseignée)";
          info.style.color = "#2ecc71";
        }

      }

    }
    xhttp.open("GET", "phps/mdpOublie.php?login=" + document.getElementById("loginInput").value, true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send();

  }

}

function addAccount(ele) {

  var inputs = ele.getElementsByTagName("input");
  var result;
  var infoIns = document.getElementById('infoInscriptionText');

  if (verifName(inputs[0]) == false) {
    infoIns.innerHTML = "Votre pseudo est déjà prit !"
    return false;
  }

  xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {

    if (this.readyState == 4 && this.status == 200) {

      result = false;
      if (this.responseText == "false lacks") infoIns.innerHTML = "Oups ! Vous avez oublié de tout templir";
      else if (this.responseText == "false Mdp") infoIns.innerHTML = "Mince ! Vos mots de passe sont différents";
      else if (this.responseText == "false failed") infoIns.innerHTML = "Coup dur ! Nos serveur ont quelques soucis...";
      else

      if (this.responseText == "true") {

        result = true;

      }

    }

  }
  xhttp.open("POST", "phps/addAccount.php", false); //On attend réponse
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send("InsLogin=" + inputs[0].value + "&InsMdp=" + inputs[1].value + "&InsConfMdp=" + inputs[2].value);

  return result;

}

var etape = 1;
function next() {

  if (etape == 3) {
    document.querySelector("#InscriptionValidButton").setAttribute("type", "submit");
  }

  if (etape < 3) {
    currentPart = document.querySelector("#part-" + etape);
    for (e of currentPart.querySelectorAll("input")) {
      if (traitementTexte(e.value).length == 0) {
        e.value = "";
        e.style.animation = "";
        e.offsetWidth;
        e.style.animation = "errorInput 1s linear";
        return;
      }
    }
    etape++;
    for (e of currentPart.querySelectorAll("input")) e.style.animation = ""; //retirer l'animation d'erreur si déjà fait une fois
    document.querySelector("#InscriptionPrecButton").disabled = false;
    for (d of document.querySelectorAll(".part")) d.style.display = "none";
    document.querySelector("#part-" + etape).style.display = "flex";
  }

  if (etape == 3) {
    document.querySelector("#InscriptionValidButton").value = "Build!";
    document.querySelector("#InscriptionValidButton").style.backgroundColor = "#f39c12";
  }

}

function prec() {

  if (etape > 1) {
    etape--;
    for (d of document.querySelectorAll(".part")) d.style.display = "none";
    document.querySelector("#part-" + etape).style.display = "flex";
    document.querySelector("#InscriptionValidButton").value = "Suivant";
    document.querySelector("#InscriptionValidButton").style.backgroundColor = "";
  }

  if (etape == 1) {
    document.querySelector("#InscriptionPrecButton").disabled = true;
  }

}

function easter_egg() {

  demo_infos = document.querySelectorAll(".demo-info");
  demo_infos[0].innerHTML = "Jean nemarre de tout... 😭";
  demo_infos[1].innerHTML = "Dokkan Battle meilleur jeu 💪";
  demo_infos[2].innerHTML = "Oui je suis le boss 🎩";
  demo_infos[3].innerHTML = "Paaaaaarle bien ! 💸";
  demo_infos[4].innerHTML = "Bon toutou Matthieu, bon toutou 🐕🐕";
  console.log("hehehe bien trouvé ! Tu es probablement un grand hacker ;)")

}
