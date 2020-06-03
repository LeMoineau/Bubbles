<?php

  if (isset($_POST['id'])) {

    include "connectToBase.php";

    if ($mysql->connect_error) {
      exit("Oups");
    }

    $sql = "SELECT login, mdp FROM account WHERE id=?";
    $stmt = $mysql->prepare($sql);
    $stmt->bind_param("s", $_POST['id']);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($login, $mdp);
    $stmt->fetch();
    $stmt->close();

    echo $login . ", " . $mdp;

  }


?>
