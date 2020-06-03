<?php

session_start();
if (!isset($_SESSION['id'])) {
  exit("session expirée");
}

include "../../../phps/connectToBase.php";
if ($mysql->connect_error) {
  exit("probleme de co");
}

$sql = "SELECT login FROM account WHERE id=?";
$stmt = $mysql->prepare($sql);
$stmt->bind_param("s", $_GET['id']);
if ($stmt->execute()) {

  $stmt->store_result();
  $stmt->bind_result($login);
  $stmt->fetch();

  if ($stmt->num_rows < 1) {
    echo "oups";
  } else {
    echo $login . "#" . $_GET['id'];
  }

  $stmt->close();

} else {

  echo "oups";

}

?>
