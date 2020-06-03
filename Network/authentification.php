<?php

  session_start();
  if (isset($_SESSION['id'])) { //Cas de non déconnection

    header("location: pages/main/main.php");

  }

  include "phps/connectToBase.php";
  if ($mysql->connect_error) {
    exit("pas de connexion");
  }

  /*

  error:
  -1: identifiant connexion faux
  -2: mots de passe création de compte différents
  -3: login de création de compte déjà prit
  -4: problème de connexion
  -5: pseudo contient symbole interdit

  */

  if (isset($_POST['login'], $_POST['mdp'])) {
    if (strpos($_POST['login'], "@") !== false) { //Check if mail ou pseudo entré
      $sql = "SELECT id FROM account WHERE mail=? AND mdp=?";
    } else {
      $sql = "SELECT id FROM account WHERE login=? AND mdp=AES_ENCRYPT(?, 'galettes')";
    }
    $stmt = $mysql->prepare($sql);
    $stmt->bind_param("ss", $_POST['login'], $_POST['mdp']);

    if ($stmt->execute()) {
      $stmt->store_result();
      $stmt->bind_result($id);
      $stmt->fetch();

      if ($stmt->num_rows == 1) {
        session_start();
        $_SESSION['id'] = $id;
        header("location: pages/main/main.php");
        $stmt->close();
        exit();
      } else {

        //Erreur mot de passe ou login incorrect
        header("location: index.php?error=1&login=" . $_POST['login'] . "#before-container");

      }
      $stmt->close();
    }

  } else if (isset($_POST['InsLogin'], $_POST['InsMdp'], $_POST['InsConfMdp'], $_POST['InsNom'], $_POST['InsPrenom'])) {

    $symb_interdit = ['#', ','];
    $contain = false;

    foreach ($symb_interdit as $sym) {
      if (strpos($_POST['InsLogin'], $sym)) $contain = true;
      if (strpos($_POST['InsNom'], $sym)) $contain = true;
      if (strpos($_POST['InsPrenom'], $sym)) $contain = true;
    }

    if ($contain === true) {

      header("location: index.php?error=5#before-container");

    } else if ($_POST['InsMdp'] == $_POST['InsConfMdp']) { //Verif si mdp pareils

      //Verif si pseudo pas déja prit
      $sql = "SELECT id FROM account WHERE login=?";
      $stmt = $mysql->prepare($sql);
      $stmt->bind_param("s", $_POST['InsLogin']);
      if ($stmt->execute()) {
        $stmt->store_result();
        $stmt->bind_result($id);
        $stmt->fetch();

        if ($stmt->num_rows == 0) {

          $sql2 = "INSERT INTO account (login, nom, prenom, mdp, mail, network, friends, links, convs) VALUES (? ,? ,? ,AES_ENCRYPT(?, 'galettes'),'','-1','','','')";
          $stmt2 = $mysql->prepare($sql2);
          $stmt2->bind_param("ssss", $_POST['InsLogin'], $_POST['InsNom'], $_POST['InsPrenom'], $_POST['InsMdp']);
          if ($stmt2->execute()) {

            $sql3 = "SELECT id FROM account WHERE login=? AND mdp=AES_ENCRYPT(?, 'galettes')";
            $stmt3 = $mysql->prepare($sql3);
            $stmt3->bind_param("ss", $_POST['InsLogin'], $_POST['InsMdp']);
            if ($stmt3->execute()) {
              $stmt3->store_result();
              $stmt3->bind_result($id);
              $stmt3->fetch();

              session_start();
              $_SESSION['id'] = $id;
              header("location: pages/main/main.php?tuto=1"); //SUCCESS !

              $stmt3->close();
            } else {

              //Erreur: probleme réseau
              header("location: index.php?error=4&newLogin=" . $_POST['InsLogin'] . "&nom=" . $_POST['InsNom'] . "&prenom=" . $_POST['InsPrenom'] . "#before-container");

            }

            $stmt2->close();
          }else {

            //Erreur: probleme réseau
            header("location: index.php?error=4&newLogin=" . $_POST['InsLogin'] . "&nom=" . $_POST['InsNom'] . "&prenom=" . $_POST['InsPrenom'] . "#before-container");

          }

        } else {

          //Erreur: login deja prit
          header("location: index.php?error=3#before-container");

        }

        $stmt->close();
      } else {

        //Erreur: probleme de réseau
        header("location: index.php?error=4&newLogin=" . $_POST['InsLogin'] . "&nom=" . $_POST['InsNom'] . "&prenom=" . $_POST['InsPrenom'] . "#before-container");

      }

    } else {

      //Erreur: mots de passe différents
      header("location: index.php?error=2&newLogin=" . $_POST['InsLogin'] . "&nom=" . $_POST['InsNom'] . "&prenom=" . $_POST['InsPrenom'] . "#before-container");

    }

  }

?>
