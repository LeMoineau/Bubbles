<?php

  include "../../phps/connectToBase.php";

  if ($mysql->connect_error) {
    exit("oupsi");
  }

  session_start();
  if (!isset($_SESSION['id'])) {
    exit("session expirée");
  }

  $sql = "SELECT nom, prenom, job, more FROM peoples WHERE id=?";
  $stmt = $mysql->prepare($sql);
  $stmt->bind_param("s", $_GET['id']);
  if ($stmt->execute()) {

    $stmt->store_result();
    $stmt->bind_result($nom, $prenom, $job, $more);
    $stmt->fetch();

    $sql2 = "SELECT network FROM account WHERE id=?";
    $stmt2 = $mysql->prepare($sql2);
    $stmt2->bind_param("s", $_SESSION['id']);
    if ($stmt2->execute()) {

      $stmt2->store_result();
      $stmt2->bind_result($net);
      $stmt2->fetch();

      $nets = explode(",", $net);

      $find = "false";
      foreach ($nets as $groups) { //Vérif si dans groupe

        $ingroup = explode(":", $groups);
        if (in_array($_GET['id'], $ingroup)) {
          echo $nom . "," . $prenom . "," . $job . "," . $more . ",deja";
          $find = "true";
        }

      }

      if ($find == "false") {
        if (in_array($_GET['id'], $nets)) { //Vérif si pas déja dans réseau solo
          echo $nom . "," . $prenom . "," . $job . "," . $more . ",deja";
        } else {
          echo $nom . "," . $prenom . "," . $job . "," . $more . ",new";
        }
      }

      $stmt2->close();

    }

    $stmt->close();

  }

?>
