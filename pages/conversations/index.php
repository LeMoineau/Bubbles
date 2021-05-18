<!DOCTYPE html>

<?php

  $title = "Bubbles - Vos conversations";
  $headerTask = "Conversations";
  $styles = ['css/style.css', 'css/convStyle.css', 'css/createConv.css', 'css/friendStyle.css'];
  include "../../phps/header.php";

  //Verif des demande d'amis
  $sql = "SELECT login, friends FROM account WHERE id=?";
  $stmt = $mysql->prepare($sql);
  $stmt->bind_param("s", $_SESSION["id"]);
  if ($stmt->execute()) {

    $stmt->store_result();
    $stmt->bind_result($log, $friends);
    $stmt->fetch();

    $stmt->close();

  }

?>

    <div id="conv-nav">
      <input class="conv-addFriend" placeholder="Ajouter un ami / <?php echo $log . "#" . $_SESSION['id']; ?> " onkeypress="sendFriendRequest(event, this)">
      <input type="text" id="conv-search" value="" placeholder="Rechercher une discussion" onkeyup="searchConv()">
      <h4 onclick="closeList(this, 'friend-list')">Vos demandes d'amis : <i style="font-size: 14px">Ouvert</i></h4>
      <div id="friend-list"> </div>
      <h4 onclick="closeList(this, 'conv-list')">Ouvrir une discussion : <i style="font-size: 14px">Ouvert</i></h4>
      <div id="conv-list"> </div>
      <div id="create-conv-button" onclick="openCreateDiv();"> Créer une nouvelle discussion </div>

    </div>

    <div id="create-conv" onclick="openCreateDiv()">
      <div id="create-conv-container" onclick="dontCloseCreateDiv()">
        <h3>Créer une nouvelle Discussion</h3>
        <input id="create-conv-name" type="text" name="conv-name" value="" placeholder="Le nom de la Discussion">
        <datalist id="create-conv-list-logins"> </datalist>
        <div class="create-conv-line">
            <input type="text" id="create-conv-addMember-input" value="" placeholder="Nom ou ID du membre à ajouter" list="create-conv-list-logins">
            <div id="create-conv-addMember" onclick="addMemberFromGroupe()"> Ajouter </div>
        </div>
        <div id="create-conv-membersAdded-list"> </div>
        <input style="display: none" id="create-conv-addMember-stock" type="hidden" name="conv-members" value="">
        <input type="submit" name="" value="💬 Créer la conversation !" onclick="createConv()">
      </div>
    </div>

    <div id="conv-msgs-container">

    </div>

    <div id="conv-buttonPanel">
      <div class="conv-button" onclick="deleteConv()">
        <div class="conv-button-head"> 👥 </div>
        <div class="conv-button-title"> Paramètres </div>
      </div>
      <div id="sender">
        <input type="text" name="" value="" placeholder="Tapez ici votre message..." onkeydown="sendMessage(event, this)">
      </div>
    </div>

    <div id="successMsg">

    </div>

  </body>

  <script type="text/javascript" src="javas/java.js"></script>
  <script type="text/javascript" src="javas/friendManager.js"></script>
  <script type="text/javascript" src="javas/createConv.js"></script>
  <script type="text/javascript" src="javas/listener.js"></script>
  <script type="text/javascript"> initFriendRequest(<?php echo "'" . $friends . "'"?>); getLogins(); initConvs();
   user_id = <?php echo $_SESSION['id']; ?>; </script>
</html>
