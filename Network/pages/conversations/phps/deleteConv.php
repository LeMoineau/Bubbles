<?php

  include "../../../phps/connectToBase.php";
  if($mysql->connect_error) {
    exit("probleme de co :/");
  }

  session_start();
  if (!isset($_SESSION['id'])) {
    exit("session expirée :/");
  }

  if (!isset($_GET['id'])) exit("pas de GET");

  include "leaveConv.php";
  leaveConv($_GET['id'], $mysql); //On retire la conv de sa liste (dans account)

  $filename = "../chats/xmls/" . $_GET['id'] . ".txt";
  $conv = file_get_contents($filename);
  $parts = explode(";\n", $conv);

  $members = explode(": ", $parts[1]);
  $members_ids = explode(",", $members[1]); //Liste des membres
  if (in_array($_SESSION['id'], $members_ids)) { //SUPPRESSION DE LA LISTE DES MEMBRES
    $index = array_search($_SESSION['id'], $members_ids);
    unset($members_ids[$index]); //On retire son id de la liste des membres
  }

  //VERIF SI ADMIN -> si oui alors def de nouveau ou suppression de conv -> si non rien
  $admins = explode(": ", $parts[2]);
  $admins_ids = explode(",", $admins[1]);
  if (in_array($_SESSION['id'], $admins_ids)) { //si admin

    $index = array_search($_SESSION['id'], $admins_ids);
    unset($admins_ids[$index]);

    if (count($admins_ids) == 0) { //Plus d'admin

        if (count($members_ids) == 0) { //Plus de membres non plus -> suppression

          $sql = "DELETE FROM convs WHERE id = ?";
          $stmt = $mysql->prepare($sql);
          $stmt->bind_param("s", $_GET['id']);
          if ($stmt->execute()) {

            echo "delete file okok";
            unlink($filename);
            $stmt->close();

          }

        } else {

          $admins_ids[] = $members_ids[0]; //Le premier membre devient admin

          $parts[1] = "members: " . implode(",", $members_ids);
          $parts[2] = "admins: " . implode(",", $admins_ids);
          $newfile = implode(";\n", $parts);

          //SAUVEGARDE DANS FICHIER
          $file = fopen($filename, "w+");

          if ($file === false) exit("pas ouvrir le fichier");

          while (!flock($file, LOCK_EX)) { //tant que fichier vérrouiller (en cours d'écriture)
            //on attent;
          }

          $last_msg = $parts[count($parts)-2];
          $tags = explode(" ¤ ", $last_msg);
          $last_id = (int)$tags[2] + 1;

          $newfile .= "-1 ¤ " . date("j/m/y h:i") . " ¤ " . $last_id . " ¤ Une personne semble être partie...;\n";
          fwrite($file, $newfile);

          flock($file, LOCK_UN);
          fclose($file);

          $sql2 = "UPDATE convs SET members=?, admins=? WHERE id=?";
          $stmt2 = $mysql->prepare($sql2);
          $stmt2->bind_param("sss", implode(",", $members_ids), implode(",", $admins_ids), $_GET['id']);
          if ($stmt2->execute()) {

            echo "ok";
            $stmt2->close();

          }

        }
    }
  }

  echo "ok";

?>
