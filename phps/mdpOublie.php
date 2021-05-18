<?php

include "connectToBase.php";
if ($mysql->connect_error) {
  exit("oups pas de co");
}

if (strpos($_GET['login'], "@") !== false) { //Vérif si adresse ou pseudo entré
  $sql = "SELECT id, mail FROM account WHERE mail=?";
} else {
  $sql = "SELECT id, mail FROM account WHERE login=?";
}
$stmt = $mysql->prepare($sql);
$stmt->bind_param("s", $_GET['login']);
if ($stmt->execute()) {

  $stmt->store_result();
  $stmt->bind_result($id, $mail);
  $stmt->fetch();

  if ($stmt->num_rows() < 1 || $mail === '') {
    exit("pas de mail");
  } else {

    //GENERATE RANDOM STRING
    $alpha = "123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $len = strlen($alpha);
    $newmdp = "";
    for ($i = 0; $i<15; $i++) {
      $newmdp .= $alpha[rand(0, $len-1)];
    }

    $sql2 = "UPDATE account SET mdp=AES_ENCRYPT(?, 'galettes') WHERE id=?";
    $stmt2 = $mysql->prepare($sql2);
    $stmt2->bind_param("ss", $newmdp, $id);
    if ($stmt2->execute()) {

      $headers[] = "From: L'équipe de Bubbles <contact@bigstones.fr>";
      $headers[] = 'MIME-Version: 1.0';
      $headers[] = 'Content-type: text/html; charset=UTF-8';

      $template = file_get_contents("mail.html");
      $parts = explode("¤", $template);

      $parts[1] = $newmdp;
      $msg = implode("", $parts);

      $succes = mail($mail, "Information • Bubbles • Mot de Passe oublié", $msg, implode("\r\n", $headers), "-fcontact@bigstones.fr");

      echo "success";
      $stmt2->close();
    }

  }

  $stmt->close();

}


?>
