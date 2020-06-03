
<?php

include '../../../phps/connectToBase.php';
if ($mysql->connect_error) {
  exit("probleme de co");
}

if (!isset($_SESSION['id'])) {
  exit("session expirée");
}

function leaveConv($convId, $mysql) {

  $sql = "SELECT convs FROM account WHERE id=?";
  $stmt = $mysql->prepare($sql);
  $stmt->bind_param("s", $_SESSION['id']);
  if ($stmt->execute()) {

    $stmt->store_result();
    $stmt->bind_result($convs);
    $stmt->fetch();

    $conv = explode(",", $convs);
    if (in_array($convId, $conv)) { //Si bien la conv dans sa liste de conv

      $index = array_search($convId, $conv);
      unset($conv[$index]);
      $newconvs = implode(",", $conv);

      $sql2 = "UPDATE account SET convs=? WHERE id=?";
      $stmt2 = $mysql->prepare($sql2);
      $stmt2->bind_param("ss", $newconvs, $_SESSION['id']);
      if ($stmt2->execute()) {

        echo "ok";
        $stmt2->close();

      }

    }

    $stmt->close();

  }

}

if (isset($_GET['id'])) leaveConv($_GET['id'], $mysql);

?>
