<?php

  /*
  /  AJOUT D'UN MEMBRE DEJA PRESENT DANS LA BASE DE DONNE
  */

  session_start();
  if (!isset($_SESSION['id'])) { //verif de connexion
    exit("session expirée, vrai coup dur :/");
  }

  include "../../phps/connectToBase.php"; //connexion à la base de donne
  if ($mysql->connect_error) {
    exit("pas de co");
  }

  if (isset($_SESSION['prenom'])) $prenoms = explode(",", $_SESSION['prenom']); //création des arrays de chaques
  else $prenoms = explode(",", "-1,-1");
  if (isset($_SESSION['nom'])) $noms = explode(",", $_SESSION['nom']);
  else $noms = explode(",", "-1,-1");
  if (isset($_SESSION['job'])) $jobs = explode(",", $_SESSION['job']);
  else $jobs = explode(",", "-1,-1");;
  if (isset($_SESSION['more'])) $mores = explode(",", $_SESSION['more']);
  else $mores = explode(",", "-1,-1");;

  //verif si id donné en GET bien présent dans la base
  if (in_array($_GET['id'], $prenoms) || in_array($_GET['id'], $noms) || in_array($_GET['id'], $jobs) || in_array($_GET['id'], $mores)) {

    $sql = "SELECT network FROM account WHERE id=?"; //Prend le network du connecté
    $stmt = $mysql->prepare($sql);
    $stmt->bind_param("s", $_SESSION['id']);
    if ($stmt->execute()) {

      $stmt->store_result();
      $stmt->bind_result($net);
      $stmt->fetch();

      $nets = explode(",", $net);
      foreach ($nets as $groups) { //vérif si pas déjà dans un groupe

        $ingroup = explode(":", $groups);
        if (in_array($_GET['id'], $ingroup)) {

          echo "deja";
          exit();

        }

      }

      if (in_array($_GET['id'], $nets)) { //verif si déjà dans réseau seul

        echo "deja";

      } else {

        $net .= "," . $_GET['id'];

        $sql2 = "UPDATE account SET network=? WHERE id=?";
        $stmt2 = $mysql->prepare($sql2);
        $stmt2->bind_param("ss", $net, $_SESSION['id']);
        if ($stmt2->execute()) {

          echo "cool";
          $stmt2->close();

        }

      }

      $stmt->close();

    }

  } else echo "oups";

?>
