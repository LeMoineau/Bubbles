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

    $c[$index] = $_GET['id'];
    $conv = implode(",", $c);

    $sql2 = "UPDATE account SET convs=? WHERE id=?";
    $stmt2 = $mysql->prepare($sql2);
    $stmt2->bind_param("ss", $conv, $_SESSION['id']);
    if ($stmt2->execute()) {

      echo "ok";
      $stmt2->close();

    }

  }

  $stmt->close();

}

?>
