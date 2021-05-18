<?php

  include "../../phps/connectToBase.php";

  if ($mysql->connect_error) {
    exit("Pas de connection au serveur :/");
  }

  session_start();
  if (!isset($_SESSION['id'])) {
    exit("Connection expirée, coup dur");
  }

  $sql = "SELECT id FROM peoples WHERE prenom LIKE ? AND nom LIKE ? AND job LIKE ? AND more LIKE ?";
  $stmt = $mysql->prepare($sql);
  if (isset($_GET['prenom'])) $prenom = "%" . $_GET['prenom'] . "%";
  else $prenom = "*";
  if (isset($_GET['nom'])) $nom = "%" . $_GET['nom'] . "%";
  else $nom = "%%";
  if (isset($_GET['job'])) $job = "%" . $_GET['job'] . "%";
  else $job = "%%";
  if (isset($_GET['more'])) $more = "%" . $_GET['more'] . "%";
  else $more = "%%";

  $stmt->bind_param("ssss", $prenom, $nom, $job, $more);
  if ($stmt->execute()) {

    $stmt->store_result();
    $stmt->bind_result($ids);

    $all = [];
    while ($stmt->fetch()) {
      $all[] = $ids;
    }

    $sql2 = "SELECT id FROM account WHERE prenom LIKE ? AND nom LIKE ?";
    $stmt2 = $mysql->prepare($sql2);
    $stmt2->bind_param("ss", $prenom, $nom);
    if ($stmt2->execute()) {

      $stmt2->store_result();
      $stmt2->bind_result($ids);

      while ($stmt2->fetch()) {
        $all[] = "r" . $ids;
      }

      $stmt2->close();
    }

    $_SESSION['ids_searched'] = implode(",", $all);
    echo implode(",", $all);

    $stmt->close();

  }


?>
