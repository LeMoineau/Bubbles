<?php

  include "../../../phps/connectToBase.php";
  if ($mysql->connect_error) {
    exit("probleme de co");
  }

  session_start();
  if (!isset($_SESSION['id'])) {
    header("location: ../../../");
    exit("session expiré");
  }

  $sql = "SELECT network FROM account WHERE id=?";
  $stmt = $mysql->prepare($sql);
  $stmt->bind_param("s", $_SESSION['id']);
  if ($stmt->execute()) {

    $stmt->store_result();
    $stmt->bind_result($net);
    $stmt->fetch();

    $nets = explode(",", $net);
    if (in_array($_GET['id'], $nets)) { //Si dans réseau mais pas dans groupe

      unset($nets[array_search($_GET["id"], $nets)]);
      $newnet = implode(",", $nets);

    } else { //Vérif si dans groupe

      $test = "false";
      foreach($nets as $groups) {

        $ingroups = explode(":", $groups);
        if (in_array($_GET['id'], $ingroups)) {

          $index = array_search($groups, $nets); //on prend index du groupe dans lequel est la bulle a retirer
          unset($ingroups[array_search($_GET['id'], $ingroups)]); //On retire membre du groupe
          $nets[$index] = implode(":", $ingroups); //On remet nouveau groupe dans réseau

          $test = "true";
        }

      }

      if($test == "false") { //Pas dans groupe
        exit("pas dans réseau");
      }

      $newnet = implode(",", $nets);

    }

    $sql2 = "UPDATE account SET network=? WHERE id=?";
    $stmt2 = $mysql->prepare($sql2);
    $stmt2->bind_param("ss", $newnet, $_SESSION['id']);
    if ($stmt2->execute()) {

      echo "ok";
      $stmt2->close();

    }
    $stmt->close();

  }


?>
