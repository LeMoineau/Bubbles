<?php

session_start();
if (!isset($_SESSION['id'])) {
  exit("session expirée");
}

include "../../../phps/connectToBase.php";
if ($mysql->connect_error) {
  exit("probleme de co");
}

function createDOM ($id, $name, $members, $admins) {

  $file_name = "../chats/xmls/" . $id . ".txt";
  $file = fopen($file_name, "w");
  flock($file, LOCK_EX);
  fwrite($file, "name: " . $name . ";\n");
  fwrite($file, "members: " . $members . ";\n");
  fwrite($file, "admins: " . $admins . ";\n");
  fwrite($file, "\n<Messages>;\n");
  fwrite($file, "-1 ¤ " . date("j/m/y h:i") . " ¤ 1 ¤ C'est un nouveau groupe qui se créer !;\n");
  flock($file, LOCK_UN);
  fclose($file);

}

if (!isset($_GET['name']) || $_GET['name'] === "") {
  $_GET['name'] = "Nouveau Groupe";
}

$sql = "SELECT friends FROM account WHERE id=?"; //VERIF
$stmt = $mysql->prepare($sql);
$stmt->bind_param("s", $_SESSION['id']);
if ($stmt->execute()) {

  $stmt->store_result();
  $stmt->bind_result($friends);
  $stmt->fetch();

  $friend = explode(",", $friends); //liste des amis de l'user
  $membersAdded = explode(",", $_GET['conv-members']); //liste des amis dans le groupe

  if (count($membersAdded) < 1) {
    $stmt->close();
    exit("pas assez de membre");
  }

  //VERIF POUR CHAQUE SI BIEN DANS LISTE D'AMI
  $verif = "0";
  foreach ($membersAdded as $m) {
    if (!in_array($m, $friend)) {
      $verif = "1";
    }
  }
  if ($verif == "1") {

    echo "oups";

  } else {

    $sql2 = "INSERT INTO convs (name, members, admins) VALUES(?,?,?)"; //INSERTION
    $stmt2 = $mysql->prepare($sql2);
    $membersAdded[] = $_SESSION['id'];
    $members = implode(",", $membersAdded);
    $stmt2->bind_param("sss", $_GET['name'], $members, $_SESSION['id']);
    if ($stmt2->execute()) {

      $id_conv = $mysql->insert_id; //On prend l'id de la dernière ligne insérée

      //Création du fichier de char
      createDOM($id_conv, $_GET['name'], $members, $_SESSION['id']);

      $sql4 = "UPDATE account SET convs = CONCAT(convs, ',', ?) WHERE id=?"; //On ajoute à l'hote
      $stmt4 = $mysql->prepare($sql4);
      $stmt4->bind_param("ss", $id_conv, $_SESSION["id"]);
      if ($stmt4->execute()) {

        $stmt4->close();

      }

      unset($membersAdded[array_search($_SESSION['id'], $membersAdded)]);
      foreach ($membersAdded as $m) {

        $sql4 = "UPDATE account SET convs = CONCAT(convs, ',?', ?) WHERE id=?"; //On ajoute à l'hote
        $stmt4 = $mysql->prepare($sql4);
        $stmt4->bind_param("ss", $id_conv, $m);
        if ($stmt4->execute()) {

          $stmt4->close();

        }

      }


      echo $id_conv;
      $stmt2->close();
    }

  }

  $stmt->close();

}



?>
