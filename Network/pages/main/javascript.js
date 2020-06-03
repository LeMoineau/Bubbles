
var formes = ['circle', 'square', 'losange', 'ovale'];


function initBubbles() {

  //Cherche network puis explode.
  //mettre network dans session
  //Pour chaque element prendre les premiers lettres et id
  //Si id dans session alors donner les infos

  var limit = document.getElementsByClassName("bubble").length; //destruction de toutes les bulles
  for (var i = 0; i<limit; i++) {

    var target = document.getElementsByClassName("bubble")[0];
    if (target.id.includes(":")) { //Si c un groupe on ajoute tout ces enfant à son parent (pour pas de bug)

      var limit2 = target.children.length;
      for (var j = 0; j<limit2; j++) {

        var b = target.lastChild;
        if (b.classList.contains("bubble")) {
          target.parentElement.appendChild(b);
        }
      }

    }
    target.parentElement.removeChild(target);

  }

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() { //recherche de la chaine de caractère 'network'

    if (this.readyState == 4 && this.status == 200) {
      if (this.responseText == "oups") { //pas trouvé
          //alert("erreur lors du chargement de votre réseau. Déso :/");
          return;
      }

      var bubbles = this.responseText.split(","); //trouvé
      var ingameID = 0;
      for (var bubbleID of bubbles) {

        if (bubbleID != -1) {

          if (bubbleID.split(":").length > 1) { //Bulle de groupes

            var group = createGroup(bubbleID, ingameID);
            document.getElementById("Network").appendChild(group);

          } else { //Bulle de personnes réel

            if (bubbleID.includes("r")) {

              ajax("phps/getInfosReal.php?id=" + bubbleID.substr(1), false, function(response) {

                var infos = response.split(","); //nom,prenom,id
                console.log(infos);
                if (infos.length < 3) {
                  //alert("Un problème est survenue pendant le chargement d'une bulle");
                  return;
                }

                var ele = createRealBubble(response, ingameID); //Création de la bulle
                document.getElementById("Network").appendChild(ele);

              });

            } else { //Bulle normale

              ajax("phps/getInfos.php?id=" + bubbleID, false, function(response) {

                var infos = response.split(","); //nom,prenom,job,more,id
                if (infos.length < 4) {
                  //alert("Un problème est survenue pendant le chargement d'une bulle");
                  return;
                }

                var ele = createBubble(response, ingameID); //Création de la bulle
                document.getElementById("Network").appendChild(ele);

              });

            }

          }
          ingameID++;
        }

      }
    }

  };
  xhttp.open("GET", "phps/initBubbles.php", false);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send();

  posBubbles(); //Affichage des bulles

}

function createBubble(allInfos, ingameID) {

  var infos = allInfos.split(",")

  var ele = document.createElement("div"); //création HTML de la bulle
  var id = getUserID(); //id de l'utilisateur
  var buID = infos[4];

  ele.classList.add("bubble"); //Nom et Prénom
  ele.classList.add(ingameID);
  ele.setAttribute("nom", infos[0]);
  ele.setAttribute("prenom", infos[1]);

  ele.setAttribute("job", window.localStorage.getItem(id + "-job-" + buID) || infos[2]); //Job et More
  ele.setAttribute("more", window.localStorage.getItem(id + "-more-" + buID) || infos[3]);

  ele.id = buID; //ID et innerHTML
  if (window.localStorage.getItem(id + "-etiquette-" + buID) != null) ele.innerHTML = window.localStorage.getItem(id + "-etiquette-" + buID);
  else ele.innerHTML = infos[1].substring(0, 1).toUpperCase() + infos[0].substring(0, 1).toUpperCase();
  ele.setAttribute("onclick", "showInfo(this)");

  //Préférences
  var colorCookie = window.localStorage.getItem(id + "-color-" + buID); //couleur
  if (colorCookie != null) ele.style.backgroundColor = colorCookie;

  var formeCookie = window.localStorage.getItem(id + "-forme-" + buID); //forme
  if (formeCookie != null) ele.classList.add(formeCookie);

  return ele;
}

function createRealBubble(allInfos, ingameID) {

  var infos = allInfos.split(",") //de la forme: "nom,prénom,id"

  var ele = document.createElement("div"); //création HTML de la bulle
  var id = getUserID(); //id de l'utilisateur
  var buID = infos[2];

  ele.classList.add("bubble"); //Nom et Prénom
  ele.classList.add(ingameID);
  ele.classList.add("real");
  ele.setAttribute("nom", infos[0]);
  ele.setAttribute("prenom", infos[1]);

  ele.setAttribute("job", window.localStorage.getItem(id + "-job-" + buID) || "Personne réelle"); //Job et More
  ele.setAttribute("more", window.localStorage.getItem(id + "-more-" + buID) || "");

  ele.id = buID; //ID et innerHTML
  if (window.localStorage.getItem(id + "-etiquette-" + buID) != null) ele.innerHTML = window.localStorage.getItem(id + "-etiquette-" + buID);
  else ele.innerHTML = infos[1].substring(0, 1).toUpperCase() + infos[0].substring(0, 1).toUpperCase();
  ele.setAttribute("onclick", "showInfo(this)");

  //Préférences
  var colorCookie = window.localStorage.getItem(id + "-color-" + buID); //couleur
  if (colorCookie != null) ele.style.backgroundColor = colorCookie;

  var formeCookie = window.localStorage.getItem(id + "-forme-" + buID); //forme
  if (formeCookie != null) ele.classList.add(formeCookie);

  return ele;

}

function createGroup(allIds, ingameID) {

  var group = document.createElement("div");
  group.setAttribute("class", "bubble group " + ingameID);
  group.setAttribute("onclick", "openGroup(this); showInfoGroup(this);");
  group.id = allIds;

  //Ajout des préférences
  if (window.localStorage.getItem(getUserID() + "-etiquette-" + allIds) != null) {
    group.innerHTML = "<div style='width:100%; display:flex; flex-direction: row; justify-content: center;'><h4 class='group-title'>"
      + window.localStorage.getItem(getUserID() + "-etiquette-" + allIds) + "</h4></div>";
    group.setAttribute("etiquette", window.localStorage.getItem(getUserID() + "-etiquette-" + allIds));
  } else {
    group.innerHTML = "<div style='width:100%; display:flex; flex-direction: row; justify-content: center;'><h4 class='group-title'>"
      + "Groupe</h4></div>";
    group.setAttribute("etiquette", "Groupe");
    localStorage.setItem(getUserID() + "-etiquette-" + allIds, "Groupe");
  }
  if (window.localStorage.getItem(getUserID() + "-color-" + allIds) != null) {
    group.style.backgroundColor = window.localStorage.getItem(getUserID() + "-color-" + allIds);
  }



  for (var ingroup of allIds.split(":")) { //Création des Bulles INGROUP

    var xhttp2 = new XMLHttpRequest(); //Obtention de toutes les INFOS de la Bulle
    xhttp2.onreadystatechange = function() {

      if (this.readyState == 4 && this.status == 200) { //création de la bulle

        var infos = this.responseText.split(","); //nom,prenom,job,more,id
        if (infos.length < 4) {
          //alert("Un problème est survenue pendant le chargement d'une bulle");
        }

        var ele = createBubble(this.responseText, -1); //Création de la bulle
        ele.classList.add("ingroup");
        ele.style.display = "none";
        ele.setAttribute("onclick", "showIngroupInfo(this)");
        group.appendChild(ele);
      }

    }
    xhttp2.open("GET", "phps/getInfos.php?id=" + ingroup, false);
    xhttp2.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp2.send();
  }

  return group;

}

function posBubbles() {

  var network = document.getElementById("Network");
  var allBubbles = document.getElementsByClassName('bubble');

  var bubbles = [];
  for (var b of allBubbles) {
    if (!b.classList.contains("ingroup")) bubbles.push(b);
  }
  for (var i = 0; i<bubbles.length; i++) {
    network.appendChild(document.getElementsByClassName(i)[0]);
  }

  var oldNetworkLines = document.getElementsByClassName("network-line");
  while(oldNetworkLines[0]) {
    oldNetworkLines[0].parentNode.removeChild(oldNetworkLines[0]);
  }

  //Check des préférences d'affichage de l'USER
  if (window.localStorage.getItem(getUserID() + "-affich") != null) {
    network.classList.add(window.localStorage.getItem(getUserID() + "-affich"));
    document.getElementById("button-affich").value = window.localStorage.getItem(getUserID() + "-affich");
  } else {
    network.classList.add("circle");
    document.getElementById("button-affich").value = "circle";
  }

  if (network.classList.contains("circle")) { //AFFICHAGE CERCLE
    //Calcul du nombre de lignes
    var nbr_lignes = 3;
    var nbr_bulle_accepted = 7;

    while(nbr_bulle_accepted < bubbles.length) {

      nbr_bulle_accepted += 7;
      nbr_lignes += 2;

    }

    //Création des lignes
    var lines = [];
    for (var i = 0; i<nbr_lignes; i++) {

      var line = document.createElement("div");
      line.classList.add("network-line");
      lines.push(line);

    }

    //Remplissage des lignes
    if (bubbles.length > 0) { //Ajoute 1 au centre pour début
      lines[(nbr_lignes-1)/2].appendChild(bubbles[0]);
      bubbles.splice(0, 1);
    }

    var middle = (nbr_lignes-1)/2;
    var cursor = middle;
    while (bubbles.length > 0) {

      lines[cursor].appendChild(bubbles[0]);
      bubbles.splice(0, 1);
      //("ajout en " + cursor + " qui var contenir " + (lines[cursor].children.length) + " bulles");
      if (cursor == middle) cursor -= 1;
      else if (cursor > middle && lines[cursor].children.length >= 3) {

        if (middle - cursor + middle - 1 < 0) cursor = 0;
        else cursor = middle - cursor + middle - 1;

      } else if (cursor > middle) cursor = middle;
      else if (middle + middle - cursor >= lines.length) cursor = lines.length -1;
      else if (lines[middle + middle - cursor].children.length < lines[cursor].children.length) cursor = middle + middle - cursor;


    }

    for (var line of lines) { //Ajout au Réseau sur page
      network.appendChild(line);
    }

  } else { //AFFICHAGE DES AUTRES
    network.style.top = "0";
    network.style.left = "0";
    network.setAttribute("top", "0");
    network.setAttribute("left", "0");

    network.style.height = document.documentElement.clientHeight-60-20 + "px";
  }


}

function showInfo(ele) { //Affiche les infos présent dans les Attributes

  //retirer infoGroup + fermer Groupe
  closeInfoGroup(ele);

  var info = document.getElementById("info");
  infoNav = document.querySelector("#info-nav");

  if (info.getAttribute("bubbleID") == ele.id) { //Ouverture / Fermeture de bar d'info

    if (info.style.right == "0px") { //si déjà ouvert
      info.style.right = "-400px";
      info.setAttribute("bubbleID", "-1");
      return;
    } else {
      info.style.right = "0px";
    }

  } else {

    info.setAttribute("bubbleID", ele.id);
    info.style.right = "0px";

  }

  document.getElementById("info-etiquette").value = "";
  document.getElementById("info-nom").innerHTML = ele.getAttribute("nom");
  document.getElementById("info-prenom").innerHTML = ele.getAttribute("prenom");
  document.getElementById("info-job").value = window.localStorage.getItem(getUserID() + "-job-" + ele.id) || ele.getAttribute("job");
  document.getElementById("info-more").value = window.localStorage.getItem(getUserID() + "-more-" + ele.id) || ele.getAttribute("more");
  showTasks(ele.id);

}

function showInfoGroup(ele) {

  if (ele.getAttribute("wantShow") !== "" && ele.getAttribute("wantShow") !== null) {
    var ids = ele.getAttribute("wantShow");
    ele.setAttribute("wantShow", "");
    showInfo(document.getElementById(ids));
    return;
  }

  //Fermer info de bulles si ouvert
  closeInfo();

  //FERMER LES AUTRES BULLES OUVERTES
  var info = document.getElementById("info-groupe");
  if (info.getAttribute("bubbleID") == ele.id) { //Ouverture et Fermeture de bar d'info

    if (info.style.right == "0px") { //si déjà ouvert
      info.style.right = "-400px";
      info.setAttribute("bubbleID", "-1");
      return;
    } else {
      info.style.right = "0px";
    }

  } else {

    info.setAttribute("bubbleID", ele.id);
    info.style.right = "0px";

  }

  document.getElementById("group-etiquette").innerHTML = ele.getAttribute("etiquette");


}

function showIngroupInfo(ele) {

  ele.parentElement.setAttribute("wantShow", ele.id);

}

function showSection(ele) {

  if (ele.style.height == ele.getAttribute("fit-size") + "px") {
    ele.style.height = "35px";
  } else {
    ele.style.height = ele.getAttribute("fit-size") + "px";
  }

}

function openGroup (ele) {

  if (window.localStorage.getItem(getUserID() + "-affich") !== "liste") { //Ouverture des groupes dans tous les affichages sauf Liste

    ele.style.minHeight = "";

    var line = 1;
    while (line*line < ele.children.length-1) { //on retire le titre
      line++;
    }
    var taille = 94 * line + 10;

    if (ele.style.width != taille + "px") {
      ele.style.width = taille + "px";
      ele.style.height = taille + "px";
      ele.style.zIndex = 2;

      for (var eles of ele.getElementsByClassName("ingroup")) {
        eles.style.display = "flex";
      }

    } else {
      for (e of document.querySelectorAll(".group")) {
        e.style.width = "";
        e.style.height = "";
        e.style.zIndex = 0;

        for (var es of e.getElementsByClassName("ingroup")) {
          es.style.display = "none";
        }
      }
    }

  } else { //Ouverture des groupes dans l'affichage Liste

    ele.style.width = "";
    ele.style.height = "";

    if (ele.style.minHeight != ele.children.length*100-100+10 + "px") {
      ele.style.minHeight = ele.children.length*100-100+10 + "px";
      ele.style.zIndex = 2;

      for (var eles of ele.getElementsByClassName("ingroup")) {
        eles.style.display = "flex";
      }

    } else {
      ele.style.minHeight = "80px";
      ele.style.zIndex = 0;

      for (var eles of ele.getElementsByClassName("ingroup")) {
        eles.style.display = "none";
      }

    }

  }


}

function changeColor(ele) {

  var info = document.getElementById("info");
  var bubbleID = info.getAttribute("bubbleID");
  var target = document.getElementById(bubbleID);

  target.style.backgroundColor = ele.style.backgroundColor;

}

function changeColorGroup(ele) {

  var info = document.getElementById("info-groupe");
  var bubbleID = info.getAttribute("bubbleID");
  var target = document.getElementById(bubbleID);

  target.style.backgroundColor = ele.style.backgroundColor;

}

function changeForme(ele) {

  var info = document.getElementById("info");
  var bubbleID = info.getAttribute("bubbleID");
  var target = document.getElementById(bubbleID);
  var currentForme = intersection(formes, target.getAttribute("class").split(" "));

  if (currentForme != null) {
    target.classList.replace(currentForme, ele.classList[1]);
  } else {
    target.classList.add(ele.classList[1]);
  }

}

function changeAffichage(ele) {

  var net = document.getElementById("Network");
  net.setAttribute("class", ele.value);
  window.localStorage.setItem(getUserID() + "-affich", ele.value);
  net.style.top = "0";
  net.style.left = "0";
  net.setAttribute("top", "0");
  net.setAttribute("left", "0");
  posBubbles();

}

function openAddToGroupe(ele) {

  var select = document.getElementById("allGroups-select");
  var info= document.getElementById("info");
  var currentBubble = document.getElementById(info.getAttribute("bubbleID"));

  if (currentBubble.classList.contains("ingroup")) { //Si déjà dans groupe dois d'abord retirer de son groupe

    ele.parentElement.getElementsByClassName("info-label")[0].innerHTML = "<b>Attention</b>, Une personne ne peut pas avoir deux groupes. Veuillez "
      + " d'abord la retirer de son Groupe.";
    select.style.display = "none";
    select.innerHTML = "Impossible :/";

  } else { //Sinon normal

    ele.parentElement.getElementsByClassName("info-label")[0].innerHTML = "Ajouter <b>" + document.getElementById("info-prenom").innerHTML + " "
      + document.getElementById("info-nom").innerHTML + "</b> au Groupe :";
    select.innerHTML = "";
    select.style.display = "initial";
    for (var b of document.getElementsByClassName("bubble")){

      if (b.classList.contains("group") && b.id.includes(":")) {
        select.innerHTML += "<option value=" + b.id + ">" + (b.getAttribute("etiquette") || "Groupe #" + b.id) + "</option>";
      }

    }

  }



}

function openRemoveFromGroupe(ele) {

  var select = document.getElementById("allInGroups-select");
  var infoGroupe = document.getElementById("info-groupe");
  var currentGroup = document.getElementById(infoGroupe.getAttribute("bubbleID"));

  ele.parentElement.getElementsByClassName("info-label")[0].innerHTML = "Retirer de <b>" + localStorage.getItem(getUserID() + "-etiquette-" + currentGroup.id)
    + "</b> :";
  select.innerHTML = "";
  var ids = currentGroup.id.split(":");
  for (var id of ids) {
    var currentBulle = document.getElementById(id);
    select.innerHTML += "<option value=" + id + ">" + (currentBulle.getAttribute("prenom") || "#" + id) + " " + (currentBulle.getAttribute("nom") || "")
      + "</option>";
  }


}

function addToGroupe() {

  var info = document.getElementById("info");
  var currentBubble = document.getElementById(info.getAttribute("bubbleID"));
  var select = document.getElementById("allGroups-select");

  if (currentBubble.classList.contains("ingroup")) return;

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {

    if (this.readyState == 4 && this.status == 200) {

      if (this.responseText == "ok") { //Changer dans la base, mtn faire dans localStorage

        var newGroupID = select.value + ":" + currentBubble.id;

        if (localStorage.getItem(getUserID() + "-color-" + select.value) != null) {
          localStorage.setItem(getUserID() + "-color-" + newGroupID, localStorage.getItem(getUserID() + "-color-" + select.value) );
          localStorage.removeItem(getUserID() + "-color-" + select.value);
        }
        if (localStorage.getItem(getUserID() + "-etiquette-" + select.value)) {
          localStorage.setItem(getUserID() + "-etiquette-" + newGroupID, localStorage.getItem(getUserID() + "-etiquette-" + select.value) );
          localStorage.removeItem(getUserID() + "-etiquette-" + select.value);
        }

        window.location = "../main/main.php"; //On reload
      }
    }

  }
  xhttp.open("GET", "phps/addToGroup.php?groupid=" + select.value + "&bubbleid=" + currentBubble.id, true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send();

}

function removeFromGroupe() {

  var infoGroup = document.getElementById("info-groupe");
  var currentGroup = document.getElementById(infoGroup.getAttribute("bubbleID"));
  var select = document.getElementById("allInGroups-select");

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {

    if (this.readyState == 4 && this.status == 200) {

      if (this.responseText.includes("-")) {

        newgroupid = this.responseText.split("-")[1];
        //On refait les espace mémoire
        if (localStorage.getItem(getUserID() + "-etiquette-" + currentGroup.id) != null) {
          localStorage.setItem(getUserID() + "-etiquette-" + newgroupid, localStorage.getItem(getUserID() + "-etiquette-" + currentGroup.id));
          localStorage.removeItem(getUserID() + "-etiquette-" + currentGroup.id);
        }

        if (localStorage.getItem(getUserID() + "-color-" + currentGroup.id) != null) {
          localStorage.setItem(getUserID() + "-color-" + newgroupid, localStorage.getItem(getUserID() + "-color-" + currentGroup.id));
          localStorage.removeItem(getUserID() + "-color-" + currentGroup.id);
        }

        closeInfoGroup();
        initBubbles(); //On reload le réseau
        sendSuccessMsg("🎉 Vous avez bien retirer " + document.getElementById(select.value).getAttribute("prenom") + " du Groupe "
         + currentGroup.getAttribute("etiquette"));
      } else {
        sendSuccessMsg("❌ Nous n'avons pas réussit à satisfaire votre demande :/");
      }

    }

  }
  xhttp.open("GET", "phps/removeFromGroup.php?groupid=" + currentGroup.id + "&bubbleid=" + select.value, true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send();

}

function destroyGroupe() {

  if (!confirm("Vous allez détruire ce groupe pour l'éternité... Etes-sûr de votre choix ? (Vous pourrez bien sûr le recréer depuis la fonctionnalité 'Créer"
    + " un Groupe')")) return;

  var infoGroup = document.getElementById("info-groupe");
  var currentGroup = document.getElementById(infoGroup.getAttribute("bubbleID"));

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {

    if (this.readyState == 4 || this.status == 200) {

      if (this.responseText == "ok") {

        //Supprimer les donner du groupe en mémoire
        localStorage.removeItem(getUserID() + "-etiquette-" + currentGroup.id);
        localStorage.removeItem(getUserID() + "-color-" + currentGroup.id);

        closeInfoGroup();

        initBubbles(); //On reload le réseau
        sendSuccessMsg("🎉 Vous avez bien dissoud le Groupe " + currentGroup.getAttribute("etiquette"));

      }

    }

  }
  xhttp.open("GET", "phps/destroyGroup.php?groupid=" + currentGroup.id, true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send();

}

function sendSuccessMsg(message) {

  var successMsg = document.getElementById("info-success-msg");
  successMsg.style.display = "initial";
  successMsg.innerHTML = message;
  successMsg.style.padding = "";
  successMsg.style.opacity = "1";

  setTimeout(function() {

    successMsg.style.opacity = "0";
    setTimeout(function() {
      successMsg.innerHTML = "";
      successMsg.style.padding = "0";
    }, 1000);

  }, 3000);

}

function save(ele, type) {

  var info = document.getElementById("info");
  var currentBubble = document.getElementById(info.getAttribute("bubbleID"));
  var infoGroup = document.getElementById("info-groupe");
  var currentGroup = document.getElementById(infoGroup.getAttribute("bubbleID"));

  if (type == "color") { //Changement de la couleur de la bulle

    window.localStorage.setItem(getUserID() + "-color-" + currentBubble.id, currentBubble.style.backgroundColor);
    sendSuccessMsg("🎉 Vous avez bien sauvegardé la couleur de cette bulle");

  } else if (type == "infos") { //Changement des informations de la bulle

    var infoJob = document.getElementById("info-job");
    var infoMore = document.getElementById("info-more");

    if (infoJob.value != currentBubble.getAttribute("job")) {
      window.localStorage.setItem(getUserID() + "-job-" + currentBubble.id, infoJob.value);
      currentBubble.setAttribute("job", infoMore.value);
    }
    if (infoMore.value != currentBubble.getAttribute("more")) {
      window.localStorage.setItem(getUserID() + "-more-" + currentBubble.id, infoMore.value);
      currentBubble.setAttribute("more", infoMore.value);
    }

    var infoEtiquette = document.getElementById("info-etiquette");
    if (infoEtiquette.value != "") {
      window.localStorage.setItem(getUserID() + "-etiquette-" + currentBubble.id, infoEtiquette.value);
      if (currentBubble.children.length > 0) task = currentBubble.children[0];
      else task = null;
      currentBubble.textContent = infoEtiquette.value;
      currentBubble.appendChild(task);
    }

    sendSuccessMsg("🎉 Vous avez bien sauvegardé les nouvelles informations de cette bulle");

  } else if (type == "forme") { //Changement de la forme de la bulle

    var currentFormeOfBubble = intersection(formes, currentBubble.getAttribute("class").split(" "));
    if (currentFormeOfBubble == "circle") {
      window.localStorage.removeItem(getUserID() + "-forme-" + currentBubble.id);
      sendSuccessMsg("🎉 Vous avez bien sauvegardé la forme de cette bulle");
    } else if (currentFormeOfBubble != null) {
      window.localStorage.setItem(getUserID() + "-forme-" + currentBubble.id, currentFormeOfBubble);
      sendSuccessMsg("🎉 Vous avez bien sauvegardé la forme de cette bulle");
    }

  } else if (type == "colorGroup") { //Changement de la couleur du groupe

    window.localStorage.setItem(getUserID() + "-color-" + currentGroup.id, currentGroup.style.backgroundColor);
    sendSuccessMsg("🎉 Vous avez bien sauvegardé la couleur de ce Groupe");

  } else if (type == "infosGroup") { //Changement du nom du groupe

    var input = document.getElementById("group-etiquette-input");
    window.localStorage.setItem(getUserID() + "-etiquette-" + currentGroup.id, input.value);
    document.getElementById("group-etiquette").innerHTML = input.value;
    currentGroup.getElementsByClassName("group-title")[0].innerHTML = input.value;
    sendSuccessMsg("🎉 Vous avez bien sauvegardé le nom du Groupe !");

  }

}

function reinit(ele, type) {

  var info = document.getElementById("info");
  var currentBubble = document.getElementById(info.getAttribute("bubbleID"));

  if (type == 'infos') {

    window.localStorage.removeItem(getUserID() + "-job-" + currentBubble.id);
    window.localStorage.removeItem(getUserID() + "-more-" + currentBubble.id);
    window.localStorage.removeItem(getUserID() + "-etiquette-" + currentBubble.id);
    if (currentBubble.children.length > 0) task = currentBubble.children[0];
    else task = null;
    currentBubble.textContent = currentBubble.getAttribute("prenom").substring(0, 1).toUpperCase()
      + currentBubble.getAttribute("nom").substring(0, 1).toUpperCase();
    currentBubble.appendChild(task);

    document.getElementById("info-etiquette").value = "";

    sendSuccessMsg("🎉 Vous avez bien réinitialiser les informations de cette bulle");

  }

}

var zoom = 1;

function zoomer(e, ele) {

  if (ele.classList.contains("liste")) return;

  var delta = e.deltaY;
  if ((zoom > 0.2 && delta > 0) || (zoom < 4 && delta < 0)) {

    if (delta < 0) zoom += 0.1;
    else zoom -= 0.1;
    document.querySelector("#Network").style.transform = "scale(" + zoom + ")";

  }

}

function recherche(ele) {

  var bubbles = document.getElementsByClassName("bubble");
  var toSearch = ele.value.toUpperCase();
  for (b of bubbles) {

    if (b.classList.contains("recherche")) {
      b.style.backgroundColor = window.localStorage.getItem(getUserID() + "-color-" + b.id) || "";
      b.classList.remove("recherche");
    }

    if (ele.value != "" && !b.classList.contains("group")) { //retirer la recherche

      if (b.getAttribute("nom").toUpperCase().includes(toSearch) || b.getAttribute("prenom").toUpperCase().includes(toSearch)
        || b.getAttribute("job").toUpperCase().includes(toSearch) || b.getAttribute("more").toUpperCase().includes(toSearch)) {

          if (b.classList.contains("ingroup") && b.style.display == "none") {
            b.parentElement.style.backgroundColor = "#f1c40f";
            b.parentElement.classList.add("recherche");
          } else {
            b.style.backgroundColor = "#f1c40f";
            b.classList.add("recherche");
          }

        }

    } else if (ele.value != "" && b.classList.contains("group")) { //Pour les groupes

      if (b.getAttribute("etiquette").toUpperCase().includes(toSearch)) {
        b.style.backgroundColor = "#f1c40f";
        b.classList.add("recherche");
      }

    }

  }

}

function removeMember(bubbleID) {

  if (!confirm("Ce membre disparaitra de votre réseau... Voulez-vous continuer votre action ?")) return;

  if (!bubbleID) bubbleID = document.getElementById("info").getAttribute("bubbleID");
  var currentBubble = document.getElementById(bubbleID);

  //Destruction de toutes les préférences de la bulle
  window.localStorage.removeItem(getUserID() + "-color-" + bubbleID);
  window.localStorage.removeItem(getUserID() + "-forme-" + bubbleID);
  window.localStorage.removeItem(getUserID() + "-job-" + bubbleID);
  window.localStorage.removeItem(getUserID() + "-more-" + bubbleID);
  window.localStorage.removeItem(getUserID() + "-etiquette-" + bubbleID);

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {

    if (this.readyState == 4 && this.status == 200) {

      if (this.responseText == "ok") {

        var userid = getUserID();

        localStorage.removeItem(userid + "-etiquette-" + currentBubble.id);
        localStorage.removeItem(userid + "-color-" + currentBubble.id);
        localStorage.removeItem(userid + "-forme-" + currentBubble.id);
        localStorage.removeItem(userid + "-job-" + currentBubble.id);
        localStorage.removeItem(userid + "-more-" + currentBubble.id);

        if (currentBubble.classList.contains("ingroup")) { //Si dans groupe modifier les infos de son groupe

          oldgroupid = currentBubble.parentElement.id;
          temp = oldgroupid.split(":");
          if (temp.includes(currentBubble.id)) temp.splice(temp.indexOf(currentBubble.id), 1);
          newgroupid = temp.join(":");

          //On refait les espace mémoire
          if (localStorage.getItem(userid + "-etiquette-" + oldgroupid) != null) {
            localStorage.setItem(userid + "-etiquette-" + newgroupid, localStorage.getItem(userid + "-etiquette-" + oldgroupid));
            localStorage.removeItem(userid + "-etiquette-" + oldgroupid);
          }

          if (localStorage.getItem(userid + "-color-" + oldgroupid) != null) {
            localStorage.setItem(userid + "-color-" + newgroupid, localStorage.getItem(userid + "-color-" + oldgroupid));
            localStorage.removeItem(userid + "-color-" + oldgroupid);
          }

        }

        closeInfo();

        initBubbles();
        sendSuccessMsg("🎉 Ce membre a bien été retiré de votre réseau");

      } else {

        sendSuccessMsg("❌ Un problème a été rencontré pendant la tentative de suppression :/");

      }

    }

  }
  xhttp.open("GET", "phps/removeMember.php?id=" + bubbleID);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send();

}

function newFriend() {

  var ele = document.createElement("div");
  ele.classList.add("newFriend");
  ele.innerHTML = "💙 Un nouvelle amis a bien été ajouté à votre réseau !";
  ele.style.opacity = "1";
  document.body.appendChild(ele);

  setTimeout('document.getElementsByClassName("newFriend")[0].style.opacity = "0"', 3000);

}

function closeInfo() {

  var info = document.getElementById("info");
  var currentBubble = document.getElementById(info.getAttribute("bubbleID"));
  var currentBubbleInInfoCheck = document.getElementById(document.getElementById("info").getAttribute("bubbleID"));

  if (currentBubble == null) return;
  showInfo(currentBubble);

}

function closeInfoGroup() {

  var info = document.getElementById("info-groupe");
  var currentGroup = document.getElementById(info.getAttribute("bubbleID"));
  var currentBulle = document.getElementById(document.getElementById("info").getAttribute("bubbleID"));

  if (currentGroup == null && currentBulle != null && currentBulle.classList.contains("ingroup")) openGroup(currentBulle.parentElement);
  if (currentGroup == null) return;
  openGroup(currentGroup);
  showInfoGroup(currentGroup);

}

function saveAll() {

  var bubbles = document.getElementsByClassName("bubble");
  var id = getUserID();

  for (var b of bubbles) {

    if (b.style.backgroundColor != "#3498db") {
      window.localStorage.setItem(id + "-color-" + b.id, b.style.backgroundColor);
    }
    if (intersection(formes, b.getAttribute("class").split(" ")) != null) {
      window.localStorage.setItem(id + "-forme-" + b.id, intersection(formes, b.getAttribute("class").split(" ")));
    }

  }

  sendSuccessMsg("🎉 Vous avez bien sauvegardé toutes les apparences actuelles !");

}
