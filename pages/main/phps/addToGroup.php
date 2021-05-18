<?php


session_start();
if (!isset($_SESSION['id'])) {
  exit("session expiré");
  header("location: ../../");
}

include "../../../phps/connectToBase.php";
if ($mysql->connect_error) {
  exit("pas de bonne co boloss");
}

include "../../../phps/getNetwork.php";
//$net initialiser

if ($net != "" && isset($_GET['groupid'], $_GET['bubbleid'])) {

  $nets = explode(",", $net);
  if (in_array($_GET['bubbleid'], $nets) && in_array($_GET['groupid'], $nets)) { //Vérif que ids sont bien dans le réseau

    $index = array_search($_GET['groupid'], $nets);
    $nets[$index] .= ":" . $_GET['bubbleid']; //On ajoute

    unset($nets[array_search($_GET['bubbleid'], $nets)]); //On retire bulle seul

    $newnet = implode(",", $nets); //On recole les morceaux

    $sql = "UPDATE account SET network=? WHERE id=?";
    $stmt = $mysql->prepare($sql);
    $stmt->bind_param("ss", $newnet, $_SESSION['id']);
    if ($stmt->execute()) {

      echo "ok";
      $stmt->close();

    }
  }

}


?>
