<?php

session_start();
if (!isset($_SESSION['id'])) {
  header("../../index.php");
  exit();
}

include "../../phps/connectToBase.php";
if ($mysql->connect_error) {
  header("../../index.php");
  exit();
}

$sql = "SELECT network FROM account WHERE id=?";
$stmt = $mysql->prepare($sql);
$stmt->bind_param("s", $_SESSION['id']);
if ($stmt->execute()) {

  $stmt->store_result();
  $stmt->bind_result($net);
  $stmt->fetch();

  //VERIF SI $_GET['target'] est bien dans réseau
  //SI OUI, ajouter à tasks
  //Renvoyer vers addTask/index.php

  $stmt->close();

}

?>
