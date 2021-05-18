<?php

session_start();
if (!isset($_SESSION['id'])) {
  exit("session expirée");
}

include "../../../phps/connectToBase.php";
if ($mysql->connect_error) {
  exit("probleme de co");
}

//On prend la liste d'ami du sender et du current_user
$sql = "SELECT friends FROM account WHERE id=? OR id=?";
$stmt = $mysql->prepare($sql);
$stmt->bind_param("ss", $_SESSION['id'], $_GET['id']);
if ($stmt->execute()) {

  $stmt->store_result();
  $stmt->bind_result($friends);
  $allFriends;
  while ($stmt->fetch()) {
    $allFriends[] = $friends;
  }

  $myFriends = $allFriends[0];
  $hisFriends = $allFriends[1];
  echo $myFriends . " " . $hisFriends;

  $f = explode(",", $myFriends);
  $demand = "?" . $_GET['id'];
  if (in_array($demand, $f)) { //Demande d'ami bien dans liste d'ami (sous forme ?28)

    //On bloque et ajoute dans notre liste d'ami
    $index = array_search($demand, $f);
    $f[$index] = "!" . $_GET['id'];
    $newMyFriends = implode(",", $f);

    $sql2 = "UPDATE account SET friends=? WHERE id=?";
    $stmt2 = $mysql->prepare($sql2);
    $stmt2->bind_param("ss", $newMyFriends, $_SESSION['id']);
    if ($stmt2->execute()) {
      $stmt2->close();
    } else {
      echo "oups";
    }

    //On ajoute rien dans la liste d'ami du sender son ID

    $sql2 = "UPDATE account SET friends=? WHERE id=?";
    $stmt2 = $mysql->prepare($sql2);
    $stmt2->bind_param("ss", $newHisFriends, $_GET['id']);
    if ($stmt2->execute()) {
      $stmt2->close();
    } else {
      echo "oups";
    }

  } else {
    echo "oups";
  }

  $stmt->close();

} else {
  echo "oups";
}

?>
