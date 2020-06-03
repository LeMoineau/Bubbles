<?php

  include "connectToBase.php";

  if ($mysql->connect_error) {
    exit("Connexion echoué");
  }

  if (!isset($_POST['InsLogin'], $_POST['InsMdp'], $_POST['InsConfMdp'])) echo "false lacks";
  else {

    //Verif des mdp (verif des logins faites avant)
    if ($_POST['InsMdp'] == $_POST['InsConfMdp']) {

      $sql2 = "INSERT INTO account (login, mdp, mail, network) VALUES (?,?,'','-1')";
      $stmt2 = $mysql->prepare($sql2);
      $stmt2->bind_param("ss", $_POST['InsLogin'], $_POST['InsMdp']);
      if ($stmt2->execute()) {

        $sql = "SELECT id FROM account WHERE login=? AND mdp=?";
        $stmt = $mysql->prepare($sql);
        $stmt->bind_param("ss", $_POST['InsLogin'], $_POST['InsMdp']);
        if ($stmt->execute()) {

          $stmt->store_result();
          $stmt->bind_result($id);
          $stmt->fetch();

          session_start();
          $_SESSION['id'] = $id;
          echo "true";

          $stmt->close();

        }

        $stmt2->close();

      } else {
        echo "false failed";
      }

    } else echo "false Mdp";

  }

?>
