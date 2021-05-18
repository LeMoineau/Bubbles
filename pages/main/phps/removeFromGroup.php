
<?php

session_start();
if (!isset($_SESSION['id'])) {
  exit("session expiré :/");
}

include "../../../phps/connectToBase.php";
if ($mysql->connect_error) {
  exit("pas de connexion");
}

include "../../../phps/getNetwork.php";
if ($net == "") {
  exit("probleme de réseau");
}

$nets = explode(",", $net); //GET: groupid, bubbleid
if (in_array($_GET['groupid'], $nets)) {

  $group = $nets[array_search($_GET['groupid'], $nets)];
  $ingroups = explode(":", $group);
  if (in_array($_GET['bubbleid'], $ingroups)) {

    $index = array_search($_GET['groupid'], $nets);
    unset($ingroups[array_search($_GET['bubbleid'], $ingroups)]); //On retire l'id de la bulle a retirer de l'id du group 9:25:4 -> 9:4
    $nets[$index] = implode(":", $ingroups); //$group = implode()
    $newnet = implode(",", $nets) . "," . $_GET['bubbleid'];

    $sql = "UPDATE account SET network=? WHERE id=?";
    $stmt = $mysql->prepare($sql);
    $stmt->bind_param("ss", $newnet, $_SESSION["id"]);
    if ($stmt->execute()) {

      echo "-" . implode(":", $ingroups);
      $stmt->close();

    }

  }

}

?>
