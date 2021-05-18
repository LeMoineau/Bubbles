<?php

  include "../../phps/connectToBase.php";
  if ($mysql->connect_error) {
    exit("coup dur");
  }

  $sql = "SELECT 1 FROM account WHERE login=?";
  $stmt = $mysql->prepare($sql);
  $stmt->bind_param("s", $_GET['login']);
  if ($stmt->execute()) {

    $stmt->store_result();
    $stmt->bind_result($logs);
    $stmt->fetch();

    echo $logs;
    $stmt->close();

  } else {

    echo "nope";

  }

?>
