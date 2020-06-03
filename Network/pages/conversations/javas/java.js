
var user_id;

function findLoginWithID(id) {

  if (id[0] == "\n") {
    id = id.substr(1);
  }

  //Recherche quand au milieu ou au début de liste
  var parts = global_logins.join(",").split("#" + id + ","); //test#33,nom#15,nom2#25
  if (parts.length > 1) {
    var whereName = parts[0].split(",");
    return whereName[whereName.length-1];
  }

  //recherche en fin de liste
  var end = global_logins[global_logins.length-1];
  var parts = end.split("#");
  if (parts[1] == id) return parts[0];

  //Si c'est nous qui envoyons
  if (id == user_id) return "Vous";
  else if (id == "-1") return "Les petits Cailloux travailleurs";

  return "Quelqu'un";
}

function searchConv() {

  var convInput = document.getElementById("conv-search");
  var toSearch = convInput.value;
  var convContainer = document.getElementById("conv-list");

  if (toSearch.length < 3) {
    if (toSearch.length == 2) {
      for (ele of convContainer.children) {
        ele.style.backgroundColor = "";
      }
    }
    return;
  }

  toAddatTop = [];
  for (ele of convContainer.children) {

    if (ele.innerHTML.toUpperCase().includes(toSearch.toUpperCase())) {
      toAddatTop.push(ele);
      ele.style.backgroundColor = "#f1c40f";
    } else {
      ele.style.backgroundColor = "";
    }

  }

  var limit = toAddatTop.length;
  for (var i = 0; i<limit; i++) {

    convContainer.prepend(toAddatTop[0]);

  }

}

function initConvs() {

  ajax("phps/initConvs.php", true, function(responseText) {

    var infos = responseText.split(";");
    for (info of infos) {

      var all = info.split(":"); //ID:nom:membres
      if (all.length < 3) return;
      var id = all[0];
      var name = all[1];
      var members = all[2];
      var logins = [];
      for (ids of members.split(",")) {

        logins.push(findLoginWithID(ids));

      }

      if (id.includes("?")) {

        var div = document.createElement("div");
        div.classList.add("conv-item");
        div.classList.add("invite");
        div.innerHTML = "<h1> Vous êtes invité à rejoindre <i>" + name + "</i></h1><h2>" + logins.join(", ") + "</h2>";

        var lineChoice = document.createElement("div");
        lineChoice.classList.add("conv-item-line");
        lineChoice.innerHTML = '<div class="conv-item-choice" onclick="enterGroup(this)"> Accepter </div>'
          + '<div class="conv-item-choice" onclick="exitGroup(this)"> Refuser </div>';

        div.setAttribute("id", id.substr(1));
        div.setAttribute("members", members);
        div.setAttribute("logins", logins.join(", "));
        div.setAttribute("name", name);

        div.appendChild(lineChoice);
        document.getElementById("conv-list").appendChild(div);

      } else {

        var div = document.createElement("div");
        div.classList.add("conv-item");
        div.setAttribute("onclick","openChat(this)");
        div.innerHTML = "<h1>" + name + "</h1><h2>" + logins.join(", ") + "</h2>";
        div.setAttribute("id", id);
        div.setAttribute("members", members);
        div.setAttribute("name", name);

        document.getElementById("conv-list").appendChild(div);

      }

    }

  });

}

function enterGroup(ele) {

  var convItem = ele.parentElement.parentElement;

  //Changer dans base de donneé account
  ajax("phps/enterGroup?id=" + convItem.id, true, function(responseText) {

    if (responseText == "ok") {

      convItem.setAttribute("onclick", "openChat(this)");
      convItem.innerHTML = "<h1>" + convItem.getAttribute("name") + "</h1><h2>" + convItem.getAttribute("logins") + "</h2>";
      convItem.classList.remove("invite");

      sendSuccessMsg("🎉 Vous êtes bien rentré dans le Groupe <b>" + convItem.getAttribute("name") + "</b> !");

    } else {

      sendSuccessMsg("❌ Oups ! un problème est survenu pendant l'acceptation de cette demande...", true);
    }

  });


}

function exitGroup(ele) {

  var convItem = ele.parentElement.parentElement;

  //Enlever du fichier txt et dans base de donné (convs et account)
  ajax("phps/exitGroup.php?id=" + convItem.id, true, function(responseText) {

    if (responseText == "ok") {

      convItem.parentElement.removeChild(convItem);
      sendSuccessMsg("🎉 Vous vous êtes bien retiré du Groupe <b>" + convItem.getAttribute("name") + "</b> !");

    } else {
      alert(responseText);
      sendSuccessMsg("❌ Oups ! un problème est survenu pendant le refus de cette demande...", true);
    }

  });

}

function deleteConv() {

  if (!confirm("Etes-vous sûr de vouloir partir de cette converation ? (elle disparaitra pour toujours...)")) return;

  var convId = document.getElementById("conv-msgs-container").getAttribute("conv-selected");

  ajax("phps/deleteConv.php?id=" + convId, true, function(responseText) {

    alert(responseText);
    if (responseText.includes("okok")) {
      document.getElementById("conv-msgs-container").setAttribute("conv-selected", " ");
      document.getElementById(convId).parentElement.removeChild(document.getElementById(convId));
      document.getElementById("conv-" + convId).parentElement.removeChild(document.getElementById("conv-" + convId));
      sendSuccessMsg("Vous êtes bien parti(e) de cette conversation !");
    }

  });

}

function openChat(ele, scroll) {

  var convs = document.getElementsByClassName("conv-msgs");
  var container = document.getElementById("conv-msgs-container");
  for (conv of convs) {
    conv.style.display = "none";
  }

  var init = true;

  if (document.getElementById('conv-' + ele.id) != null) { //AFFICHAGE DE LA BONNE "CONV-MSGS"

    var init = false;
    document.getElementById('conv-' + ele.id).style.display = "flex";

  } else { //CREATION SI PREMIERE FOIS QUE OUVRE DISCUSSIOn

    var div = document.createElement('div');
    div.id = "conv-" + ele.id;
    div.classList.add('conv-msgs');
    container.appendChild(div);

  }
  container.setAttribute("conv-selected", ele.id);

  //UPDATE MSG
  var convDiv = document.getElementById('conv-' + ele.id);

  ajax("phps/openChat.php?id=" + ele.id, true, function(responseText) {

    var messages = responseText.split("<Messages>;")[1];
    if (messages == "undefined") return;
    var msg = messages.split(";\n");

    for (i = msg.length-1; i >= 0; i--) {

      var m = msg[i];
      var infos = m.split(" ¤ ");

      if (infos.length > 3) {

        var msgID = infos[2];
        var sender = infos[0];
        var dateMsg = infos[1];
        var content = infos[3];

        if (document.getElementById(ele.id + "-msg-" + msgID) == null) {
            var line = document.createElement("div");
            line.classList.add("conv-msg-line");

            if (sender == user_id) line.classList.add("from-us"); //Cas d'envoyeur particulier
            else if (sender == -1) line.classList.add("from-admin");

            var div = document.createElement("div");
            div.classList.add("conv-msg-item");
            div.id = ele.id + "-msg-" + msgID;
            div.setAttribute("date", dateMsg);
            div.setAttribute("sender", sender);
            div.innerHTML = "<h1>" + content + "</h1><h2>" + findLoginWithID(sender) + " - " + dateMsg + "</h2>";
            line.appendChild(div);

            if (init) convDiv.prepend(line);
            else convDiv.appendChild(line);
        } else {

          if(!scroll) convDiv.scrollTop = convDiv.scrollHeight - convDiv.clientHeight;
          return;
        }

      }

    }

    //Scroll en bas de message
    if(!scroll) convDiv.scrollTop = convDiv.scrollHeight - convDiv.clientHeight;

  });

}

function sendMessage(event, ele) {

  if(event.keyCode == 13) {

    var msg = ele.value;
    if (msg.length > 1) {

      var container = document.getElementById("conv-msgs-container");

      if (container.getAttribute("conv-selected") != null) {

        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {

          if (this.readyState == 4 && this.status == 200) {

            ele.value = "";
            openChat(document.getElementById(container.getAttribute("conv-selected")));

          }

        }
        xhttp.open("POST", "phps/sendMessage.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("id=" + container.getAttribute("conv-selected") + "&content=" + msg);

      }

    }

  }

}

function closeList(ele, id) {

  var target = document.getElementById(id);
  var state = ele.getElementsByTagName("i")[0];
  if (state.innerHTML == "Fermé") {
    state.innerHTML = "Ouvert";
    target.style.minHeight = target.children.length * 72 + "px";
    target.style.height = target.children.length * 72 + "px";
  } else {
    state.innerHTML = "Fermé";
    target.style.minHeight = "0px";
    target.style.height = "0px";
  }

}

function openCreateDiv() {

  var createDiv = document.getElementById("create-conv");

  if (createDiv.getAttribute("work-on") == "true") {
    createDiv.setAttribute("work-on", "false");
    return;
  }

  if (createDiv.style.display == "flex") {
    createDiv.style.display = "none";
  } else {
    createDiv.style.display = "flex";
  }

}

function dontCloseCreateDiv() {

  var createDiv = document.getElementById("create-conv");
  createDiv.setAttribute("work-on", "true");

}

function sendSuccessMsg(msg, bad) {

  var successMsg = document.getElementById("successMsg");
  successMsg.innerHTML = msg;
  successMsg.style.display = "block";
  if (bad != null) {
    successMsg.style.backgroundColor = "#e74c3c";
  } else {
    successMsg.style.backgroundColor = "#2ecc71";
  }

  setTimeout(function() {

    successMsg.style.display = "none";

  }, 4000);

}
