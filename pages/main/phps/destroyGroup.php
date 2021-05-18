<?php

session_start();
if (!isset($_SESSION['id'])) {
  exit("problème de co les gars");
}

include "../../../phps/connectToBase.php";
if ($mysql->connect_error) {
  exit("oupsi");
}

include "../../../phps/getNetwork.php";
if ($net == "") {
  exit("problème de réseau :/");
}

$nets = explode(",", $net);
if (in_array($_GET['groupid'], $nets)) {

  $index = array_search($_GET['groupid'], $nets);
  $group = $nets[$index];
  $ingroups = explode(":", $group);
  $ingroups = implode(",", $ingroups);
  $nets[$index] = $ingroups;
  $newnet = implode(",", $nets);

  $sql = "UPDATE account SET network=? WHERE id=?";
  $stmt = $mysql->prepare($sql);
  $stmt->bind_param("ss", $newnet, $_SESSION['id']);
  if ($stmt->execute()) {

    echo "ok";
    $stmt->close();

  }

}



?>
