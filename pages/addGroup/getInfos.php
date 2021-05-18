<?php

  session_start();
  if (!isset($_SESSION['id']) || !isset($_GET['id'])) {
    exit("session expirée");
  }

  include "../../phps/connectToBase.php";
  if ($mysql->connect_error) {
    exit("oups, pas de co");
  }

  $sql = "SELECT nom, prenom, job, more FROM peoples WHERE id=?";
  $stmt = $mysql->prepare($sql);
  $stmt->bind_param("s", $_GET['id']);
  if ($stmt->execute()) {

    $stmt->store_result();
    $stmt->bind_result($nom, $prenom, $job, $more);
    $stmt->fetch();

    echo $nom . "," . $prenom . "," . $job . "," . $more . "," . $_GET["id"];
    $stmt->close();

  } else {
    echo "oups";
  }


?>
