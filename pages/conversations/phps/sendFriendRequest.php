<?php

session_start();
if (!isset($_SESSION['id'])) {
  exit("session expirée");
}

include "../../../phps/connectToBase.php";
if ($mysql->connect_error) {
  exit("probleme de co");
}

$login = $_GET['login'];
$id = $_GET['id'];

$sql = "SELECT id, friends FROM account WHERE login=?";
$stmt = $mysql->prepare($sql);
$stmt->bind_param("s", $login);
if ($stmt->execute()) {

  $stmt->store_result();
  $stmt->bind_result($realId, $friends);
  $stmt->fetch();

  if ($stmt->num_rows > 0) {

    if ($id == $realId && $id != $_SESSION['id']) { //Check if bon #id ou pas son propre #id

      $allFriends = explode(",", $friends);
      if (in_array($_SESSION['id'], $allFriends)) { //Check if pas déjà en ami
        $stmt->close();
        exit("ami");
      } else if (in_array("?" . $_SESSION['id'], $allFriends)) { //Check if pas déjà en demande
        $stmt->close();
        exit("demande");
      } else if (in_array("!" . $_SESSION['id'], $allFriends)) { //Check if pas bloqué
        $stmt->close();
        exit("bloqué");
      }

      $sql2 = "UPDATE account SET friends=? WHERE id=?";

      if ($friends == "") $friends = "?" . $_SESSION['id'];
      else $friends .= "?" . $_SESSION['id'];

      $stmt2 = $mysql->prepare($sql2);
      $stmt2->bind_param("ss", $friends, $realId);
      if ($stmt2->execute()) {

        echo "ok";
        $stmt2->close();

      }

    }

  }

  $stmt->close();

}

?>
