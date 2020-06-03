<?php

session_start();
if (!isset($_SESSION['id'])) {
  exit();
}

include "../../../phps/connectToBase.php";
if ($mysql->connect_error) {
  exit();
}

$sql = "SELECT nom, prenom FROM peoples WHERE id=?";
$stmt = $mysql->prepare($sql);
$stmt->bind_param("s", $_GET['bubble']);
if ($stmt->execute()) {

  $stmt->store_result();
  $stmt->bind_result($nom, $prenom);
  $stmt->fetch();

  echo $prenom . " " . $nom . " #" . $_GET['bubble'];

  $stmt->close();

}

?>
