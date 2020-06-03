<!DOCTYPE html>

<?php

$title = "Bubbles - Vos Tâches";
$headerTask = "Tâches";
$styles = ['style.css'];
include "../../phps/header.php";

$sql = "SELECT network FROM account WHERE id=?";
$stmt = $mysql->prepare($sql);
$stmt->bind_param("s", $_SESSION['id']);
if ($stmt->execute()) {

  $stmt->store_result();
  $stmt->bind_result($net);
  $stmt->fetch();

  $stmt->close();

}

?>

    <main id="container">

      <form id="task-field" action="phps/addTask.php" method="POST">
        <datalist id="actionsList"></datalist>
        <input id="task-action" class="task-input" type="text" placeholder="Donner des Hamburgers" list="actionsList" onkeyup="updateAction(this)" autocomplete="list">
        <h4> à / avec </h4>
        <datalist id="personsList"></datalist>
        <input id="task-person" class="task-input" type="text" placeholder="Sammy" list="personsList" onkeyup="updateAction(this)" autocomplete="list">
        <h4> avant le </h4>
        <input id="task-date" class="task-input" type="text" placeholder="01/01/1999" value="" onkeyup="updateAction(this)">
        <input id="task-submit" type="submit" value="Ajouter ➜">
      </form>
      <div id="task-prec">
        <h4>Vos autres tâches :</h4>
        <div class="task-item">
          <div class="task-item-title">
            faire les courses avec Personne le 15/03/2020
          </div>
          <div class="task-item-buttons">
            <div class="task-item-button">
              💣
              <div class="task-item-button-info"> Supprimer </div>
            </div>
            <div class="task-item-button">
              📦
              <div class="task-item-button-info"> Archiver </div>
            </div>
          </div>
        </div>
        <div class="task-item">
          <div class="task-item-title">
            Détruire dayeb tous les jours
          </div>
          <div class="task-item-buttons">
            <div class="task-item-button">
              💣
              <div class="task-item-button-info"> Supprimer </div>
            </div>
            <div class="task-item-button">
              📦
              <div class="task-item-button-info"> Archiver </div>
            </div>
          </div>
        </div>
      </main>

    </div>

  </body>

  <script type="text/javascript" src="java.js"></script>
  <script type="text/javascript"> initListes(<?php echo '"' . $net . '"'; ?>); </script>

</html>
