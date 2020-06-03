<?php

session_start();
if (!isset($_SESSION['id'])) {
  exit("session expirée");
}

include "../../../phps/connectToBase.php";
if ($mysql->connect_error) {
  exit("probleme de co");
}

$file_name = "../chats/xmls/" . $_POST['id'] . ".txt";
$file_str = file_get_contents($file_name);
$lines = explode(";\n", $file_str);

if ($file_str !== false && count($lines) > 1) {

  $members = explode(",", explode(": ", $lines[1] )[1] ); //ATTENTION CHECK SI PAS DEJA
  echo implode(",", $members);
  if (in_array($_SESSION['id'], $members)) { //On vérifie que peut ouvrir le fichier car bie nmembre du groupe

    $file = fopen($file_name, "w+");

    if ($file === false) exit("pas ouvrir le fichier");

    while (!flock($file, LOCK_EX)) { //tant que fichier vérrouiller (en cours d'écriture)
      //on attent;
    }

    $last_msg = $lines[count($lines)-2];
    $tags = explode(" ¤ ", $last_msg);
    $last_id = (int)$tags[2] + 1;

    $newmsg = $file_str . $_SESSION['id'] . " ¤ " . date("j/m/y h:i") . " ¤ " . $last_id . " ¤ " . $_POST['content'] . ";\n";
    fwrite($file, $newmsg);

    echo $newmsg;

    flock($file, LOCK_UN);
    fclose($file);

  }

}

?>
