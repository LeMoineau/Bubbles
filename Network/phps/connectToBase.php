<?php

  $host = "localhost";
  $user = "root";
  $mdp = "";
  $base = "network";
  //bigstoneef007.mysql.db

  $mysql = new mysqli($host, $user, $mdp, $base);

  // Convention $mysql pour toute les connection aux base donnee
  // + verification de mysql->connect_error

?>
