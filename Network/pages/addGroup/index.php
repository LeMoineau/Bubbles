<!DOCTYPE html>

<?php

  $title = "Bubbles - Vos conversations";
  $headerTask = "Ajouter un Groupe dans le réseau ";
  $styles = ['style.css'];
  include "../../phps/header.php";

  if (!isset($_SESSION['id'])) {
    header("location: ../../index.php");
    exit("pas co");
  }

  include "../../phps/connectToBase.php";
  if ($mysql->connect_error) {
    exit("Un problème est survenu pendant le traitement de votre requête");
  }

  $sql = "SELECT network FROM account WHERE id=?";
  $stmt = $mysql->prepare($sql);
  $stmt->bind_param("s", $_SESSION["id"]);
  if ($stmt->execute()) {

    $stmt->store_result();
    $stmt->bind_result($net);
    $stmt->fetch();

    $stmt->close();

  }

  if (isset($_POST['etiquette'], $_POST['color'], $_POST['ingroup'])) { //Création officiel du groupe

    $groups = explode(":", $_POST['ingroup']);
    $nets = explode(",", $net);

    foreach ($groups as $group) { //Parcours de $groups à l'aide de $group -> Verif que toutes les id dans groupes sont ien dans le réseau

      if (!in_array($group, $nets)) {

        header("location: index.php?error=1");
        exit("Vous avez essayé de grujer vous dit donc !");
      }

    }

    foreach($nets as $target) { //Vérif que id pas déjà dans autre groupe

      if (strpos($target, ':')) { //Si c un groupe
        $eclateGroup = explode(":", $target);
        foreach($groups as $group) { //On parcourt chaque id
          if (in_array($group, $eclateGroup)) { //Et on verifie que une id dans le groupe a créer est déjà dans un groupe créé

            header("location: index.php?error=2");
            exit("Vous avez essayé de grujer vous dit donc !");
          }
        }
      }

    }

    //Retirage de chaque membre du groupe
    foreach ($groups as $group) { //Parcours de $groups à l'aide de $group

      unset($nets[array_search($group, $nets)]);

    }

    $newnet = implode(",", $nets) . "," . $_POST['ingroup'];
    $net = $newnet;

    $sql = "UPDATE account SET network=? WHERE id=?";
    $stmt = $mysql->prepare($sql);
    $stmt->bind_param("ss", $newnet, $_SESSION['id']);
    if ($stmt->execute()) {

      echo "<script type='text/javascript'>localStorage.setItem('" . $_SESSION['id'] . "-etiquette-" . $_POST['ingroup'] . "', '" . $_POST['etiquette'] . "');</script>";
      echo "<script type='text/javascript'>localStorage.setItem('" . $_SESSION['id'] . "-color-" . $_POST['ingroup'] . "', '" . $_POST['color'] . "');</script>";

      //header("location: index.php?success=2");
      $stmt->close();
    }

  }

?>

    <div id="all">
      <div class="box">
        <h1 class="title" style="background-color: #2ecc71">Membre du Réseau</h1>
        <div id="container">

        </div>
        <input id="recherche" type="text" value="" placeholder="Rechercher" onkeyup="recherche()">
      </div>

      <div class="box">
        <h1 id="test" class="title" style="background-color: #3498db">Membre du Groupe</h1>
        <div id="container2">

        </div>
      </div>

      <div class="box">
        <h1 class="title" style="background-color: #e67e22">Informations du Groupe</h1>
        <form action="index.php" method="post"autocomplete="off">
          <h3>Nom du Groupe</h3>
          <input type="text" name="etiquette" value="" placeholder="200IQ Team" onkeyup="changeTitle(this)" required>
          <h3>Couleur du Groupe</h3>
          <div class="info-line">
            <div class="info-color" style="background-color: #1abc9c" onclick="changeColor(this)"> </div>
            <div class="info-color" style="background-color: #16a085" onclick="changeColor(this)"> </div>
            <div class="info-color" style="background-color: #2ecc71" onclick="changeColor(this)"> </div>
            <div class="info-color" style="background-color: #27ae60" onclick="changeColor(this)"> </div>
            <div class="info-color" style="background-color: #3498db" onclick="changeColor(this)"> </div>
            <div class="info-color" style="background-color: #2980b9" onclick="changeColor(this)"> </div>
          </div>
          <div class="info-line">
            <div class="info-color" style="background-color: #9b59b6" onclick="changeColor(this)"> </div>
            <div class="info-color" style="background-color: #8e44ad" onclick="changeColor(this)"> </div>
            <div class="info-color" style="background-color: #f1c40f" onclick="changeColor(this)"> </div>
            <div class="info-color" style="background-color: #f39c12" onclick="changeColor(this)"> </div>
            <div class="info-color" style="background-color: #e67e22" onclick="changeColor(this)"> </div>
            <div class="info-color" style="background-color: #d35400" onclick="changeColor(this)"> </div>
          </div>
          <input id="input-color" type="hidden" name="color" value="">
          <input id="input-ingroup" type="hidden" name="ingroup" value="" required>
          <input type="submit" value="Créer le groupe">
        </form>
      </div>
    </div>

    <div id="msg"> </div>

  </body>

  <script type="text/javascript" src="java.js"></script>
  <script type="text/javascript"> initListe(<?php echo "'" . $net . "'" ?>); </script>
  <?php

  if (isset($_GET['error'])) echo "<script> msg('❌ Une erreur est survenue pendant la création de votre groupe') </script>";
  if (isset($_GET['success'])) echo "<script> msg('🎉 Votre groupe a bien été créé !') </script>";

  ?>

</html>
