<?php

session_start();
if (!isset($_SESSION['id'])) {
  exit("session expirée");
}

include "../../../phps/connectToBase.php";
if ($mysql->connect_error) {
  exit("probleme de co");
}

$sql = "SELECT convs FROM account WHERE id=?";
$stmt = $mysql->prepare($sql);
$stmt->bind_param("s", $_SESSION['id']);
if ($stmt->execute()) {

  $stmt->store_result();
  $stmt->bind_result($convs);
  $stmt->fetch();

  $c = explode(",", $convs);
  $search = "?" . $_GET['id']; //$_GET['id'] de la forme 12, 45, 89 pas ?12,?45,?89
  $index = array_search($search, $c);

  if ($index !== false) { //Si trouvé

    unset($c[$index]); //On supprime
    $conv = implode(",", $c);

    $sql2 = "UPDATE account SET convs=? WHERE id=?";
    $stmt2 = $mysql->prepare($sql2);
    $stmt2->bind_param("ss", $conv, $_SESSION['id']);
    if ($stmt2->execute()) {

      //Modification dans le fichier txt
      $file_url = "../chats/xmls/" . $_GET['id'] . ".txt";
      $file_str = file_get_contents($file_url);
      $file = fopen($file_url, "w+");

      while (!flock($file, LOCK_EX)) { //Si quelqu'un est en train d'écrire dedans
        //On attend
      }

      $lines = explode(";\n", $file_str);
      $members = explode(",", explode(": ", $lines[1] )[1] ); //on obtiens 33,1 depuis members: 33,1
      $index2 = array_search($_SESSION['id'], $members); //On retire l'ID de l'user

      if ($index2 !== false) { //on vérifie que l'ID dans bien dans la liste member

        unset($members[$index2]);
        $member_str = implode(",", $members);
        $lines[1] = "members: " . $member_str;
        $new_file = implode(";\n", $lines);
        fwrite($file, $new_file);

        $sql3 = "UPDATE convs SET members=? WHERE id=?";
        $stmt3 = $mysql->prepare($sql3);
        $stmt3->bind_param("ss", $member_str, $_GET['id']);
        if ($stmt3->execute()) {

          echo "ok";
          $stmt3->close();

        }

      }

      flock($file, LOCK_UN);
      fclose($file);

      $stmt2->close();

    }

  }

  $stmt->close();

}

?>
