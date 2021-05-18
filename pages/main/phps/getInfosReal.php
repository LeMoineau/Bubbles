<?php

  include "../../../phps/connectToBase.php";

  if ($mysql->connect_error) {
    exit("oupsi");
  }

  session_start();
  if (!isset($_SESSION['id'])) {
    exit("session expirée");
  }

  $sql = "SELECT nom, prenom FROM account WHERE id=?";
  $stmt = $mysql->prepare($sql);
  $stmt->bind_param("s", $_GET['id']);
  if ($stmt->execute()) {

    $stmt->store_result();
    $stmt->bind_result($nom, $prenom);
    $stmt->fetch();

    echo $nom . "," . $prenom . "," . $_GET['id'];

    $stmt->close();

  }

?>
