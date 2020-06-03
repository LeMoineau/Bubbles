
function addMemberFromGroupe() {

    var input = document.getElementById("create-conv-addMember-input");
    var membersAddedDiv = document.getElementById("create-conv-membersAdded-list");
    var stock = document.getElementById("create-conv-addMember-stock");

    var membersAdded = stock.value.split(",");

    if (global_logins.includes(input.value) && !membersAdded.includes(input.value)) {

      var nomOfFriend = input.value.split("#")[0];
      membersAddedDiv.innerHTML += '<div class="create-conv-membersAdded" id="' + input.value
        + '" onclick="removeMemberFromGroupe(this)">' + nomOfFriend + ' ✕ </div>';
      if (stock.value.length > 0) stock.value += "," + input.value;
      else stock.value = input.value;

    }

    input.value = "";

}

function removeMemberFromGroupe(ele) {

  var membersAddedDiv = document.getElementById("create-conv-membersAdded-list");
  var stock = document.getElementById("create-conv-addMember-stock");

  var membersAdded = stock.value.split(",");
  if (membersAdded.includes(ele.id)) {
      membersAdded.splice(membersAdded.indexOf(ele.id), 1);
      stock.value = membersAdded.join(",");
  }

  ele.parentElement.removeChild(ele);

}

function createConv() {

  var input = document.getElementById("create-conv-name");
  var membersAddedDiv = document.getElementById("create-conv-membersAdded-list");
  var stock = document.getElementById("create-conv-addMember-stock");

  var toSend = [];
  for (s of stock.value.split(",")) {
    toSend.push(s.split("#")[1]);
  }

  ajax("phps/createConv.php?name=" + input.value + "&conv-members=" + toSend.join(","), true, function(responseText) {

    if (responseText != "oups") {
      var div = document.createElement("div");
      div.id = responseText;
      div.classList.add("conv-item");
      div.setAttribute("onclick","openChat(this)");

      var logins = [];
      for (s of stock.value.split(",")) {
        logins.push(s.split("#")[0]);
      }

      if (input.value.length > 0) div.innerHTML = "<h1>" + input.value + "</h1><h2>" + logins.join(", ") + "</h2>";
      else div.innerHTML = "<h1> Nouveau Groupe </h1><h2>" + logins.join(", ") + "</h2>";
      div.setAttribute("members", toSend);
      div.setAttribute("name", input.value);
      document.getElementById("conv-list").appendChild(div);

      openCreateDiv();
    }

  });

}
