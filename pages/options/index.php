<!DOCTYPE html>

<?php

  $title = "Bubbles - Options de votre Compte";
  $headerTask = "Options du compte";
  $styles = ['style.css'];
  include "../../phps/header.php";

    $varChanged = "";

    $sql = "SELECT login, nom, prenom, mdp, mail FROM account WHERE id=?"; //Recupère les infos actuels du compte
    $stmt = $mysql->prepare($sql);
    $stmt->bind_param("s", $_SESSION['id']);
    if ($stmt->execute()) {

      $stmt->store_result();
      $stmt->bind_result($log, $nom, $prenom, $mdp, $mail);
      $stmt->fetch();

      //Changement de nom
      if (isset($_POST['newNom']) && $_POST['newNom'] !== "" && $_POST['newNom'] !== $nom) {

        $sql2 = "UPDATE account SET nom=? WHERE id=?"; //changement de mdp dans Base
        $stmt2 = $mysql->prepare($sql2);
        $stmt2->bind_param("ss", $_POST['newNom'], $_SESSION['id']);
        if ($stmt2->execute()) {

          $stmt2->fetch();
          $stmt2->close();

          if ($varChanged == "") $varChanged .= "votre Nom";
          else $varChanged .= ", votre Nom";
        }

      }

      //Changement de prénom
      if (isset($_POST['newPrenom']) && $_POST['newPrenom'] !== "" && $_POST['newPrenom'] !== $prenom) {

        $sql2 = "UPDATE account SET prenom=? WHERE id=?"; //changement de mdp dans Base
        $stmt2 = $mysql->prepare($sql2);
        $stmt2->bind_param("ss", $_POST['newPrenom'], $_SESSION['id']);
        if ($stmt2->execute()) {

          $stmt2->fetch();
          $stmt2->close();

          if ($varChanged == "") $varChanged .= "votre Prénom";
          else $varChanged .= ", votre Prénom";
        }

      }

      //Changement du pseudo
      if (isset($_POST['newLogin']) && $_POST['newLogin'] !== "" && $_POST['newLogin'] !== $log) {

        $symbols = ['#', ','];
        $contain = false;

        foreach ($symbols as $sym) {
          if (strpos($_POST['newLogin'], $sym)) $contain = true;
        }

        if ($contain == false) {
          $sql3 = "SELECT 1 FROM account WHERE login=?"; //Verif si déjà ce pseudo
          $stmt3 = $mysql->prepare($sql3);
          $stmt3->bind_param("s", $_POST['newLogin']);
          if ($stmt3->execute()) {

            $stmt3->store_result();
            $stmt3->bind_result($logs);
            $stmt3->fetch();

            if ($logs !== 1) {

              $sql2 = "UPDATE account SET login=? WHERE id=?"; //changement de pseudo dans Base
              $stmt2 = $mysql->prepare($sql2);
              $stmt2->bind_param("ss", $_POST['newLogin'], $_SESSION['id']);
              if ($stmt2->execute()) {

                $stmt2->fetch();
                $stmt2->close();

                $varChanged .= "votre Pseudo";
              }

            }
            $stmt3->close();
          }
        } else {
          header("location: ?success='Ce nouveau Pseudo contient des symboles interdits...'");
        }

      }

      //Changement de mdp
      if (isset($_POST['newMdp']) && $_POST['newMdp'] !== "" && $_POST['newMdp'] !== $mdp && $_POST['newMdp'] == $_POST['confMdp']
          && $_POST['newMdp'] != $mdp) {

        $sql2 = "UPDATE account SET mdp=AES_ENCRYPT(?, 'galettes') WHERE id=?"; //changement de mdp dans Base
        $stmt2 = $mysql->prepare($sql2);
        $stmt2->bind_param("ss", $_POST['newMdp'], $_SESSION['id']);
        if ($stmt2->execute()) {

          $stmt2->fetch();
          $stmt2->close();

          if ($varChanged == "") $varChanged .= "votre Mot de Passe";
          else $varChanged .= ", votre Mot de Passe";
        }

      }

      //Changement du mail
      if (isset($_POST['newMail']) && $_POST['newMail'] !== "" && $_POST['newMail'] !== $mail && strpos($_POST['newmail'], "@") !== false) {

        $sql3 = "SELECT id FROM account WHERE mail=?"; //Vérif si pas déjà ce mail
        $stmt3 = $mysql->prepare($sql3);
        $stmt3->bind_param("s", $_POST['mail']);
        if ($stmt3->execute()) {

          $stmt3->store_result();
          $stmt3->bind_result($temp);
          $stmt3->fetch();

          if ($stmt3->num_rows < 1) {

            $sql2 = "UPDATE account SET mail=? WHERE id=?"; //changement de mdp dans Base
            $stmt2 = $mysql->prepare($sql2);
            $stmt2->bind_param("ss", $_POST['newMail'], $_SESSION['id']);
            if ($stmt2->execute()) {

              $stmt2->close();

              if ($varChanged == "") $varChanged .= "votre Adresse Mail";
              else $varChanged .= ", votre Adresse Mail";
            }

          }

        }

      }

      //Conclusion par changement de page et message succes
      if ($varChanged !== "") header("location: ?success='🎉 Vous avez bien modifié " . $varChanged . "'");
      $stmt->close();
    }

?>

  <nav id="option-nav">
    <a onclick="showSection(['profil-section', 'mail-section'])"> Vos informations 🐣 </a>
    <a onclick="showSection(['login-section', 'mdp-section'])"> Vos identifiants 🐥 </a>
    <a onclick="showSection(['pref-section'])"> Vos préférences 🐓 </a>
  </nav>

    <div id="success-msg"></div>

    <main id="container">
      <form action="index.php" method="POST" class="section" style="display: flex;" id="profil-section">
        <h1>Changer vos noms 👤</h1>
        <input type="text" name="newPrenom" value="" placeholder="Prénom <?php echo ": " . $prenom; ?>">
        <input type="text" name="newNom" value="" placeholder="Nom <?php echo ": " . $nom; ?>">
        <div class="submit-line">
          <input type="submit" value="Update!">
        </div>
      </form>
      <form action="index.php" method="POST" class="section" style="display: flex;" id="mail-section">
        <hr>
        <h1>Lier votre compte avec un email 📧</h1>
        <input type="email" name="newMail" value="" placeholder="Ajouter une adresse mail">
        <div class="submit-line">
          <input type="submit" value="Update!">
        </div>
      </form>
      <form action="index.php" method="POST" class="section" id="login-section">
        <h1>Changer votre login 🎩</h1>
        <input type="text" name="newLogin" id="input-login" value="" onkeyup="checkAlreadyLogin(this)" placeholder="<?php echo $log; ?>">
        <p id="info-login"></p>
        <div class="submit-line">
          <input type="submit" value="Update!">
        </div>
      </form>
      <form action="index.php" method="POST" class="section" id="mdp-section">
        <hr>
        <h1>Changer votre mot de passe 🔑</h1>
        <input type="password" name="currentMdp" value="" placeholder="Votre mot de passe actuel">
        <input type="password" name="newMdp" value="" placeholder="Votre nouveau mot de passe">
        <input type="password" name="confMdp" value="" placeholder="Confirmer votre nouveau mot de passe">
        <div class="submit-line">
          <input type="submit" value="Update!">
        </div>
      </form>
      <div class="section" id="pref-section" style="width: 500px">
        <h1>🐓 Mes Préférences</h1>
        <h3 class="options-title"> 🔸 Stockage des préférences</h3>
        <p class="options-txt">Vos personnalisations de Réseau sont sauvegardées dans votre Navigateur.</br>Ainsi, si vous voulez changer d'appareils ou nettoyer
        votre navigateur, nous vous conseillons de <b>Télécharger vos préférences</b> pour les <b>Importer</b> plus tard.</p>
        <div class="options-button" onclick="downloadPref()"> Télécharger vos préférences </div>
        <label class="options-button options-files">
          Importer des préférences
          <input style="display: none" type="file" onchange="readPref(event)">
        </label>
        <h3 class="options-title"> 🔸 Réinitialisation des préférences </h3>
        <div class="options-button" onclick="reinit()"> Réinitialiser toutes les préférences </div>
      </div>
    </main>

      <div class="section" >

      </div>

    </div>

  </body>

  <script type="text/javascript" src="java.js"></script>

</html>

<?php

  if (isset($_GET['success'])) {

    if (strpos($_GET['success'], 'Prénom') || strpos($_GET['success'], 'Nom') || strpos($_GET['success'], 'Mail')) {
      echo "<script> showSection(['profil-section', 'mail-section']); </script>";
    } else if (strpos($_GET['success'], 'Pseudo') || strpos($_GET['success'], 'Mot de Passe')) {
      echo "<script> showSection(['login-section', 'mdp-section']); </script>";
    }
    echo "<script> successMsg(" . $_GET['success'] . ") </script>";

  }

?>
