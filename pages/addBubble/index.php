<!DOCTYPE html>

<?php

  $title = "Bubbles - Les membres de votre réseau";
  $headerTask = "Ajouter une Bulle au réseau ";
  $styles = ['style.css'];
  include "../../phps/header.php";

  if (isset($_SESSION['id'])) { //CONNECTE

    /*
    /  AJOUT D'UN NOUVEAU MEMBRE CREE PAR L'UTILISATEUR
    */

    if (isset($_POST['nom'], $_POST['prenom'], $_POST['job'], $_POST['more'])) {

      include "../../phps/connectToBase.php";
      if ($mysql->connect_error) {
        exit("Un problème est survenu pendant le traitement de votre requête");
      }

      $sql = "INSERT INTO peoples (nom, prenom, job, more) VALUES (?,?,?,?)"; //insertion dans les "Peoples"
      $stmt = $mysql->prepare($sql);
      $stmt->bind_param("ssss", $_POST['nom'], $_POST['prenom'], $_POST['job'], $_POST['more']);
      if ($stmt->execute()) {

        $sql2 = "SELECT network FROM account WHERE id=?"; //recherche du network existant
        $stmt2 = $mysql->prepare($sql2);
        $stmt2->bind_param("s", $_SESSION['id']);
        if ($stmt2->execute()) {

          $stmt2->store_result();
          $stmt2->bind_result($net);
          $stmt2->fetch();

          $sql4 = "SELECT id FROM peoples WHERE nom=? AND prenom=? AND job=? AND more=?"; //recherche de l'id de nouveau "People" créé
          $stmt4 = $mysql->prepare($sql4);
          $stmt4->bind_param("ssss", $_POST['nom'], $_POST['prenom'], $_POST['job'], $_POST['more']);
          if ($stmt4->execute()) {

            $stmt4->store_result();
            $stmt4->bind_result($id_people);
            $stmt4->fetch();

            if ($stmt4->num_rows > 0) {
              $sql3 = "UPDATE account SET network=? WHERE id=?"; //Ajout du nouveau "People" dans son network
              $stmt3 = $mysql->prepare($sql3);
              $net .= "," . $id_people;
              $stmt3->bind_param("ss", $net, $_SESSION['id']);
              if ($stmt3->execute()) {

                header("location: ../main/main.php?newFriend=coucou");
                $stmt3->close();

              } else exit("erreur 303 X'(");
            }

            $stmt4->close();
          } else exit("erreur 404 :''C");



          $stmt2->close();
        } else exit("erreur 202 :'(");

        $stmt->close();
      } else {
        //exit("Un problème est survenu pendant le traitement de votre requête");
        exit("mince 101 :(");
      }

    }

  } else { //SESSION EXPIRE
    echo "<script> alert('Votre session a expiré...') </script>";
    header("location: ../../index.php");
  }

?>

  <main>

    <div id="preview-panel" class="panel">
      <div id="bubble-preview">MU</div>
      <div class="preview-line">
        <h2 id="Prénom-preview" onclick="focusForm(this, 0)">Marin</h2>
        <h2 id="Nom-preview" onclick="focusForm(this, 1)">Utopia</h2>
      </div>
      <div class="preview-line">
        <p id="Job-preview" style="font-style: italic;" onclick="focusForm(this, 2)">Visiteur des étoiles</p>
      </div>
      <div class="preview-line">
        <p id="Notes-preview" onclick="focusForm(this)">Se perd souvent dans ses rêves, tête la première dans la lune </p>
      </div>
      <button type="button">An other?</button>
    </div>
    <div id="form-panel" class="panel">
      <form class="" action="index.php" method="post">
        <h1>Les informations de votre nouvelle bulle 📝</h1>
        <div class="form-line">
          <input type="text" name="prenom" onkeyup="updatePreview(this, 'Prénom')" placeholder="Prénom" required>
          <input type="text" name="nom" onkeyup="updatePreview(this, 'Nom')" placeholder="Nom" required>
        </div>
        <div class="form-line">
          <input type="text" name="job" onkeyup="updatePreview(this, 'Job')" placeholder="Métier" required>
        </div>
        <div class="form-line">
          <textarea name="more" rows="3" onkeyup="updatePreview(this, 'Notes')" placeholder="Notes"></textarea>
        </div>
        <div class="form-line">
          <input type="submit" value="Create!">
        </div>
      </form>
    </div>
    <div id="sugg-panel" class="panel">
      <h2>Nos suggestions...</h2>
      <button type="button" onclick="checkPeoples(this)">Already exist?</button>
      <div id="sugg-container">
        <div class="suggestion prenom deja">
          <div class="suggestion-visu real" style="background-color: #9b59b6"> P3 </div>
          <div class="suggestion-title">
            📌 <b>pierrot 3</b> (déja dans votre réseau) <br> #27 <i>hacker</i><br>test
          </div>
        </div>
      </div>
    </div>

  </main>

    <div id="success">
      🎉 Ce membre a bien été ajouté à votre réseau !
    </div>

    <div id="deja">
      ⚡ Vous avez déjà ce Membre dans votre réseau...
    </div>

  </body>

  <script type="text/javascript" src="java.js"> </script>

</html>
