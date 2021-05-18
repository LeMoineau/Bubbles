
var global_friends = [];
var global_logins = [];

function ajax(url, wait, callback) {

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {

    if (this.readyState == 4 && this.status == 200) {

      callback(this.responseText);

    }

  }
  xhttp.open("GET", url, wait);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send();

}

function initFriendRequest(friends) {

  var friend = friends.split(",");
  global_friends = friend; //Pour ne pas refaire les requêtes
  var friendList = document.getElementById("friend-list")
  for (f of friend) {

    if (f.includes("?")) {

      ajax("phps/getLogin.php?id=" + f.substr(1), true, function(responseText) {

        if (responseText != "oups") {

          var d = document.createElement("div");
          d.classList.add("friend-item");
          d.setAttribute("friend-id", responseText.split("#")[1]);
          d.setAttribute("friend-login", responseText.split("#")[0]);
          var l1 = document.createElement("div");
          var l2 = document.createElement("div");
          l1.classList.add("friend-line");
          l2.classList.add("friend-line");
          l1.innerHTML = "<b>" + responseText + '</b>';
          l2.innerHTML = '<div class="friend-accept" onclick="acceptFriend(this)"> Accepter </div>'
            + '<div class="friend-refuse" onclick="refuseFriend(this)"> Fermer </div>'
            + '<div class="friend-block" onclick="blockFriend(this)"> Bloquer </div>';
          d.appendChild(l1);
          d.appendChild(l2);
          friendList.prepend(d);

        }

      });

    }

  }

}

function acceptFriend(ele) {

  var friendDiv =  ele.parentElement.parentElement;

  ajax("phps/acceptFriend.php?id=" + friendDiv.getAttribute("friend-id"), true, function(responseText) {

    if (!responseText.includes("oups")) {
      friendDiv.parentElement.removeChild(friendDiv);

      var friendEntireLogin = friendDiv.getAttribute("friend-login") + "#" + friendDiv.getAttribute("friend-id");
      document.getElementById("create-conv-list-logins").innerHTML += "<option value='" + friendEntireLogin + "'></option>";
        global_logins.push(friendEntireLogin);
        global_friends.push(friendDiv.getAttribute("friend-id"));

      sendSuccessMsg("🎉 " + friendDiv.getAttribute("friend-login") + " a bien été ajouté à votre liste d'ami !");
    }

  });

}

function refuseFriend(ele) {

  var friendDiv =  ele.parentElement.parentElement;

  ajax("phps/refuseFriend.php?id=" + friendDiv.getAttribute("friend-id"), true, function(responseText) {

    if (!responseText.includes("oups")) {
      friendDiv.parentElement.removeChild(friendDiv);
      sendSuccessMsg("🎉 Vous avez bien effacé la demande !");
    }

  });

}

function blockFriend(ele) {

  var friendDiv =  ele.parentElement.parentElement;

  ajax("phps/blockFriend.php?id=" + friendDiv.getAttribute("friend-id"), true, function(responseText) {

    if (!responseText.includes("oups")) {
      friendDiv.parentElement.removeChild(friendDiv);
      sendSuccessMsg("🎉 " + friendDiv.getAttribute("friend-login") + " a bien été bloqué !");
    }

  });

}

function sendFriendRequest(event, ele) {

  if (event.keyCode == 13) {

    var parts = ele.value.split("#");
    if (parts.length != 2 || isNaN(parts[1]) || parts[1] <= 0) return;

    ajax("phps/sendFriendRequest.php?login=" + parts[0] + "&id=" + parts[1], true, function(responseText) {

      if (responseText == "ok") {
        sendSuccessMsg("🎉 Vous avez bien envoyé une demande d'ami à " + parts[0] + " !");
      } else if (responseText == "ami") {
        sendSuccessMsg("🎉 Vous êtes déjà ami avec " + parts[0] + " ! Tout est génial !");
      } else if (responseText == "demande") {
        sendSuccessMsg("🎉 Votre demande a déjà été envoyé ! Vous n'avez plus qu'à attendre des nouvelles de " + parts[0] + "...");
      } else if (responseText == "bloqué") {
        sendSuccessMsg("❌ Mince ! " + parts[0] + " vous a bloqué(e)...", true);
      } else {
        sendSuccessMsg("❌ Mince ! Nous n'avons pu trouvé de " + ele.value + " dans notre réseau...", true);
      }
      ele.value = "";

    });

  }

}

function getLogins() {

  if (global_logins.length > 0) return;

  var listDiv = document.getElementById("create-conv-list-logins");

  for (f of global_friends) {

    ajax("phps/getLogin.php?id=" + f, false, function(responseText) {

      if (responseText != "oups") {
        listDiv.innerHTML += "<option value=" + responseText + "> </option>";
        global_logins.push(responseText);
      }

    });

  }

}
