<?php

session_start();
if (!isset($_SESSION['id'])) {
  exit("session expirée");
}

include "../../../phps/connectToBase.php";
if ($mysql->connect_error) {
  exit("probleme de co");
}

$sql = "SELECT convs FROM account WHERE id=?"; //On prend les id des convs
$stmt = $mysql->prepare($sql);
$stmt->bind_param("s", $_SESSION['id']);
if ($stmt->execute()) {

  $stmt->store_result();
  $stmt->bind_result($convs);
  $stmt->fetch();

  $conv = explode(",", $convs);
  $toSend = [];
  foreach($conv as $c) { //Pour chaque ID

    /*
    ATTENTION plusieurs types d'ID:
    -ID  -> dans groupe certifié
    -?ID -> à confirmer
    */
    $conv_id = $c;
    if (strpos($conv_id, "?") !== false) {
      $conv_id = substr($conv_id, 1);
    }

    $sql2 = "SELECT name, members, admins FROM convs WHERE id=?"; //On prend les infos
    $stmt2 = $mysql->prepare($sql2);
    $stmt2->bind_param("s", $conv_id);
    if ($stmt2->execute()) {

      $stmt2->store_result();
      $stmt2->bind_result($name, $member, $admins);
      $stmt2->fetch();

      if ($stmt2->num_rows > 0) {
          $toSend[] = $c . ":" . $name . ":" . $member . ":" .$admins;
      }

      $stmt2->close();

    }

  }

  $send = implode(";", $toSend);
  echo $send;

  $stmt->close();

}

?>
