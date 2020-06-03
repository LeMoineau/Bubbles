<!DOCTYPE html>

<?php

  $title = "Bubbles - Votre réseau";
  $headerTask = "Réseau";
  $styles = ['css/styleMobile.css', 'css/style.css', 'css/tutostyle.css'];
  include "../../phps/header.php";

?>

    <div id="ButtonPanel">
      <a class="button" href="../addBubble">
        <p>Ajouter un Membre au Réseau 💙</p>
      </a>
      <a class="button" href="../addGroup">
        <p>Créer un Groupe 📁</p>
      </a>
      <select id="button-affich" class="button" style="font-size: 15px; padding-right: 0;" onchange="changeAffichage(this)">
        <option value="circle">Affichage en Cercle 📏</option>
        <option value="liste">Affichage en Liste 📏</option>
        <option value="wrapHor">Affichage Hor. 📏</option>
        <option value="wrapVer">Affichage Ver. 📏</option>
      </select>
      <input type="text" name="" value="" placeholder="Rechercher" class="button" onkeyup="recherche(this)">
    </div>

    <div id="Network" style="left: 0px; top: 0px;" left="0" top="0" onwheel="zoomer(event, this)" class="">
    </div>

    <div id="info"> <!-- Barre d'information de la Bulle -->
      <div class="line">
        <h2 id="info-prenom"></h2>
        <h2 id="info-nom"></h2>
      </div>
      <div id="info-menu" class="info-section" fit-size="350"> <!-- Changement d'infos -->
        <h3 onclick="showSection(this.parentElement)">📝 Modifier le Métier et/ou les Infos</h3>
        <input type="text" name="etiquette" id="info-etiquette" value="" placeholder="Etiquette de la Bulle">
        <input name="job" type="text" id="info-job" value="">
        <textarea name="more" id="info-more" rows="5" cols="80"></textarea>
        <input type="submit" onclick="save(this, 'infos')" value="Sauvegarder les modifications">
        <input type="button" onclick="reinit(this, 'infos')" class="reinit" value="Réinitialiser">
      </div>

      <div id="color-menu" class="info-section" fit-size="270"> <!-- Changement de couleur -->
        <h3 onclick="showSection(this.parentElement)">🔵 Changer la couleur de la Bulle</h3>
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
        <input type="submit" onclick="save(this, 'color')" value="Sauvegarder les modifications">
      </div>

      <div id="form-menu" class="info-section" fit-size="230"> <!-- Changement de forme -->
        <h3 onclick="showSection(this.parentElement)">🌀 Changer la forme de la Bulle</h3>
        <div class="info-line">
          <div class="info-forme square" onclick="changeForme(this)"> A </div>
          <div class="info-forme circle" onclick="changeForme(this)"> B </div>
          <div class="info-forme losange" onclick="changeForme(this)"> C </div>
          <div class="info-forme ovale" onclick="changeForme(this)"> D </div>
        </div>
        <input type="submit" onclick="save(this, 'forme')" value="Sauvegarder les modifications">
      </div>

      <div id="group-menu" class="info-section" fit-size="210"> <!-- Ajouter a un groupe -->
        <h3 onclick="showSection(this.parentElement); openAddToGroupe(this);">📂 Ajouter cette Bulle à un groupe</h3>
        <div class="info-label"> Ajouter Test testeur </div>
        <select id="allGroups-select" class="info-select" name=""> </select>
        <input type="submit" onclick="addToGroupe()" value="Ajouter à ce Groupe">
      </div>

      <div id="task-menu" class="info-section" fit-size="360" style="height: 35px"> <!-- Ajouter a un groupe -->
        <h3 onclick="showSection(this.parentElement); showTasks(this.parentElement.parentElement.getAttribute('bubbleid'));">📆 Assigner une tâche</h3>
        <div class="task-line">
          <textarea class="task-area" placeholder="Une nouvelle tâche ?"></textarea>
          <button title="Ajouter cette tâche" onclick="addTask(this.parentElement)"> ➕ </button>
        </div>
        <div id="task-container">

        </div>
      </div>

      <div id="close-menu" class="info-special-button" style="background-color: #e74c3c" onclick="removeMember()">
        ❌ Retirer ce membre de votre Réseau </div>
      <div class="info-line" style="margin-bottom: 40px;">
        <div class="info-special-button" style="background-color: #bdc3c7" onclick="closeInfo()"> Fermer </div>
        <div class="info-special-button" style="background-color: #2ecc71" onclick="saveAll()"> Sauvegarder tous </div>
      </div>

      <nav id="info-nav">
        <a href="#info-menu" title="section des informations"> 📝 </a>
        <a href="#color-menu" title="section des couleurs"> 🔵 </a>
        <a href="#form-menu" title="section des formes"> 🌀 </a>
        <a href="#group-menu" title="section des groupes"> 📂 </a>
        <a href="#group-menu" title="section des tâches"> 📆 </a>
        <a href="#close-menu" title="section de fermeture"> ❌ </a>
      </nav>
    </div>



    <div id="info-groupe"> <!-- Barre d'information du Groupe -->
      <div class="line">
        <h2 id="group-etiquette"></h2>
      </div>

      <div class="info-section" fit-size="200"> <!-- Changement d'infos -->
        <h3 onclick="showSection(this.parentElement)">📝 Changer le nom du Groupe</h3>
        <input id="group-etiquette-input" type="text" name="etiquette" value="" placeholder="Ex: 200IQ Team">
        <input type="submit" onclick="save(this, 'infosGroup')" value="Sauvegarder les modifications">
      </div>

      <div class="info-section" fit-size="270"> <!-- Changement de couleur -->
        <h3 onclick="showSection(this.parentElement)">🔵 Changer la couleur du Groupe</h3>
        <div class="info-line">
          <div class="info-color" style="background-color: #1abc9c" onclick="changeColorGroup(this)"> </div>
          <div class="info-color" style="background-color: #16a085" onclick="changeColorGroup(this)"> </div>
          <div class="info-color" style="background-color: #2ecc71" onclick="changeColorGroup(this)"> </div>
          <div class="info-color" style="background-color: #27ae60" onclick="changeColorGroup(this)"> </div>
          <div class="info-color" style="background-color: #3498db" onclick="changeColorGroup(this)"> </div>
          <div class="info-color" style="background-color: #2980b9" onclick="changeColorGroup(this)"> </div>
        </div>
        <div class="info-line">
          <div class="info-color" style="background-color: #9b59b6" onclick="changeColorGroup(this)"> </div>
          <div class="info-color" style="background-color: #8e44ad" onclick="changeColorGroup(this)"> </div>
          <div class="info-color" style="background-color: #f1c40f" onclick="changeColorGroup(this)"> </div>
          <div class="info-color" style="background-color: #f39c12" onclick="changeColorGroup(this)"> </div>
          <div class="info-color" style="background-color: #e67e22" onclick="changeColorGroup(this)"> </div>
          <div class="info-color" style="background-color: #d35400" onclick="changeColorGroup(this)"> </div>
        </div>
        <input type="submit" onclick="save(this, 'colorGroup')" value="Sauvegarder les modifications">
      </div>

      <div class="info-section" fit-size="210"> <!-- Ajouter a un groupe -->
        <h3 onclick="showSection(this.parentElement); openRemoveFromGroupe(this);">📁 Retirer une Bulle du Groupe</h3>
        <div class="info-label"> Ajouter Test testeur </div>
        <select id="allInGroups-select" class="info-select" name=""> </select>
        <input type="submit" onclick="removeFromGroupe()" value="Retirer du Groupe">
      </div>

      <div class="info-special-button" style="background-color: #e74c3c" onclick="destroyGroupe()">
        ❌ Dissoudre le Groupe </div>
      <div class="info-line">
        <div class="info-special-button" style="background-color: #bdc3c7" onclick="closeInfoGroup()"> Fermer </div>
        <div class="info-special-button" style="background-color: #2ecc71" onclick="saveAll()"> Sauvegarder tous </div>
      </div>
    </div>

    <div id="info-success-msg"></div>

  </body>

  <script type="text/javascript" src="javascript.js"> </script>
  <script type="text/javascript" src="listener.js"> </script>
  <script type="text/javascript" src="javas/tutojava.js"> </script>
  <script type="text/javascript" src="javas/indexDB.js"> </script>
  <script type="text/javascript"> initBubbles(); </script>

  <?php

    if (isset($_GET['newFriend'])) {
      echo "<script> newFriend(); </script>";
    } else if (isset($_GET['tuto'])) {
      //echo "<script> generate_tuto(); </script>";
    }

  ?>
</html>
