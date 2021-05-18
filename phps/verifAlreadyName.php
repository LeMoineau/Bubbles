<?php

    include "connectToBase.php";

    if($mysql->connect_error) {
      exit('Could not connect');
    }

    $symb_interdit = ['#', ','];
    $contain = false;

    foreach ($symb_interdit as $sym) {
      if (strpos($_GET['login'], $sym)) $contain = true;
    }

    if ($contain === true) {

      echo "Symbole";

    } else {

      $sql = "SELECT id FROM account WHERE login=?";

      $stmt = $mysql->prepare($sql);
      $stmt->bind_param("s", $_GET['login']);
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($id);
      $stmt->fetch();

      $nbr_row = $stmt->num_rows;
      echo $nbr_row;

      $stmt->close();
    }



?>
