<?php

session_start();
if (!isset($_SESSION['id'])) {
  exit("session expirée");
}

include "../../../phps/connectToBase.php";
if ($mysql->connect_error) {
  exit("probleme de co");
}

$sql = "SELECT members FROM convs where id=?";
$stmt = $mysql->prepare($sql);
$stmt->bind_param("s", $_GET['id']);
if ($stmt->execute()) {

  $stmt->store_result();
  $stmt->bind_result($members);
  $stmt->fetch();

  $m = explode(",", $members);
  if (in_array($_SESSION['id'], $m)) {

    $file_name = "../chats/xmls/" . $_GET['id'] . ".txt";
    echo file_get_contents($file_name);

  }

  $stmt->close();

}

?>
