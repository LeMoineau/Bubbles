
<?php
  include "connectToBase.php";
  session_start();

  if ($mysql->connect_error) {
    exit("Cannot connect to Base");
  }

  if (!isset($_SESSION['id']))  { //SESSION EXPIRE
    echo "<script> alert('Votre session a expiré...') </script>";
    header("location: ../../index.php");
  }
?>

<html lang="fr" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title><?php echo $title; ?></title>
    <?php

    foreach ($styles as $style) {
      echo '<link rel="stylesheet" href="' . $style . '">';
    }

    ?>
    <link rel="icon" type="image/png" href="../../imgs/logoBubbles.png" />
    <link rel="stylesheet" href="../../global_ressources/navstyle.css">
    <link rel="stylesheet" href="../../global_ressources/global.css">
    <script type="text/javascript" src="../../global_ressources/navjava.js"> </script>
    <script type="text/javascript" src="../../global_ressources/globalfunctions.js"> </script>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
  </head>

  <body>

    <header id="header">
      <div id="header-left">
        <img src="../../imgs/logoBubbles.png" alt="B" style="height: 40px;">
        ubbles
      </div>
      <div id="header-middle">
        <h2>
          <?php
            $sql = "SELECT login FROM account WHERE id=?";
            $stmt = $mysql->prepare($sql);
            $stmt->bind_param("s", $_SESSION['id']);
            if ($stmt->execute()) {
              $stmt->store_result();
              $stmt->bind_result($log);
              $stmt->fetch();

              echo $headerTask . " de <b title='Vous pouvez changer votre pseudo dans les options 🔩'>" . $log . "</b> <i>#" . $_SESSION['id'] . "</i>";
              $stmt->close();
            }
          ?>
        </h2>
      </div>
      <div id="header-right">
        <div class="header-profil">
          <?php  echo substr($log, 0, 1); ?>
        </div>
        <nav id="nav-container">
          <div id="nav-menu">
            <a class="nav-button" href='../main/main.php'>
              <p>👥 Votre Réseau</p>
            </a>
            <a class="nav-button" href='../conversations/'>
              <p>💬 Vos Discussions</p>
            </a>
            <a class="nav-button" href='../addTask/'>
              <p>📆 Vos tâches</p>
            </a>
            <a class="nav-button" href='../patchnote/'>
              <p>📈 Patch note</p>
            </a>
            <a class="nav-button" href='../options/'>
              <p>🔩 Options</p>
            </a>
            <a class="nav-button" onclick="disconnect()">
              <p>📤 Se déconnecter</p>
            </a>
          </div>
        </nav>
      </div>
    </header>
