<?php

  session_start();
  if (!isset($_SESSION['id'])) {
    exit("session expiré");
  }

  include "../../../phps/connectToBase.php";
  if ($mysql->connect_error) {
    exit("pas de co, coup dur");
  }

  $sql = "SELECT network FROM account WHERE id=?";
  $stmt = $mysql->prepare($sql);
  $stmt->bind_param("s", $_SESSION['id']);
  if ($stmt->execute()) {

    $stmt->store_result();
    $stmt->bind_result($net);
    $stmt->fetch();

    echo $net;
    $stmt->close();

  } else {
    echo "oups";
  }

?>
