<?php

  session_start();
  if (!isset($_SESSION['id'])) {
    exit("oups");
  }

  echo $_SESSION['id'];

?>
