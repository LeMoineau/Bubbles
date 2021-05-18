<?php

if (session_id() == "") session_start();
if (!isset($_SESSION['id'])) {
  exit("session expiré");
  header("location: ../../");
}

include "../../phps/connectToBase.php";
if ($mysql->connect_error) {
  exit("pas de bonne co boloss");
}

$sql = "SELECT network FROM account WHERE id=?"; //On recupère le network pour vérifier si pas de trafic
$stmt = $mysql->prepare($sql);
$stmt->bind_param("s", $_SESSION['id']);
$net = "";
if ($stmt->execute()) {

  $stmt->store_result();
  $stmt->bind_result($net);
  $stmt->fetch();

  $stmt->close();

}

?>
