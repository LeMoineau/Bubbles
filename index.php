<!DOCTYPE html>

<html lang="fr" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Bubbles</title>
    <link rel="icon" type="image/png" href="imgs/logoBubbles.png" />
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/style2.css">
    <link rel="stylesheet" href="css/connexion.css">
    <link rel="stylesheet" href="global_ressources/global.css">
  </head>
  <body>

    <header>
      <div id="logo">
        <img src="imgs/logoBubbles.png">
        ubbles
      </div>
      <div id="header-buttons">
        <a class="header-button">
          <div> Qui sommes-nous ? </div>
          <div style="width: 160px">
            <div class="header-button-underline"> </div>
          </div>
        </a>
        <a href="#before-container" class="header-button">
          <div> Se lancez ! </div>
          <div style="width: 90px">
            <div class="header-button-underline"> </div>
          </div>
        </a>
        <a href="#pepite-section" class="header-button">
          <div> D'autres pépites... </div>
          <div style="width: 147px">
            <div class="header-button-underline"> </div>
          </div>
        </a>
      </div>
    </header>

    <main>

      <div id="first-section">
        <svg class="behind"> </svg>
        <div class="demo">
          <div style="top: calc(50% - 35px); left: calc(50% - 35px); background-color: #2ecc71; animation-duration: 0.6s" class="demo-bubbles">
            Matthieu
            <div class="demo-info" style="margin-top: 0px; margin-left: 185px; width: 250px; height: 20px;">
              🥤 Ne sois pas timide, je vais t'aider
            </div>
          </div>
          <div style="top: calc(50% + 100px); left: calc(50% + 100px); background-color: #f39c12; animation-duration: 0.8s" class="demo-bubbles">
            Brian
            <div class="demo-info" style="margin-top: -65px; margin-left: 0px; width: 200px; height: 20px;">
              🎁 Bienvenue sur Bubbles !
            </div>
          </div>
          <div style="top: calc(50% - 170px); left: calc(50% + 100px); animation-duration: 0.7s" class="demo-bubbles">
            Pierre
            <div class="demo-info" style="margin-top: -65px; margin-left: 100px; width: 150px; height: 20px;">
              🎈 Coucou les amis !
            </div>
          </div>
          <div style="top: calc(50% + 100px); left: calc(50% - 170px); background-color: #e74c3c; animation-duration: 0.7s" class="demo-bubbles">
            Ilias
            <div class="demo-info" style="margin-top: 80px; margin-left: 0px;">
              🌀 Vous pouvez changer la couleur et la <br> forme de vos bulles selon vos envies !
            </div>
          </div>
          <div style="top: calc(50% - 170px); left: calc(50% - 170px); background-color: #9b59b6; animation-duration: 0.5s" class="demo-bubbles">
            Tristan
            <div class="demo-info" style="margin-top: 80px; margin-left: -150px;">
              📝 Vous pouvez ajouter une étiquette à une <br> bulle pour lui donner vie !
            </div>
          </div>
        </div>
        <svg class="behind" style="background-color: transparent; z-index: 0; width: 800px;">
          <polygon points="0,0 800,0 500,800 0,800" class="form"/>
        </svg>
        <div class="real-info">
          <h1>Bubbles, la manière simple et pratique d'organiser votre réseau de connaissance !</h1>
          <p>Plus besoin de vous perdre dans vos carnets d'adresse, tes listes de contacts et tout autres tracas ! Vous n'avez plus qu'à admirer</p>
          <p>Vous ne raterez pas non plus la possibilité de discuter avec vos amis et de gérer votre temps avec la gestion de tâche</p>
          <a href="#before-container"><button> Lancez-vous maintenant 👌 </button></a>
        </div>
      </div>

      <h1 id="before-container">Vous partez pour l'aventure ?</h1>
      <p id="infoInscriptionText">
        <?php
          if (isset($_GET['error'])) {
            if ($_GET['error'] == 2) {
              echo "Vos mots de passes ne sont pas identiques :/";
            } else if ($_GET['error'] == 3) {
              echo "Ce pseudo est déjà prit :/";
            } else if ($_GET['error'] == 5) {
              echo "Mince ! Votre nom, prénom ou pseudo contient des symboles interdits (# ou , par exemple)";
            }
          }
        ?>
        <p id='infoConnexionText'>
        <?php
          if (isset($_GET['error']) && $_GET['error'] == "1") {
            echo "💥 MINCE ! Nous ne trouvons pas ton compte. Vérifie ton mot de passe et ton nom d'utilisateur";
          }
        ?>
        </p>
      </p>
      <div id="container">
        <div class="panel">
          <h1>Décollage immédiat 🚀</h1>
          <form action="authentification.php" method="post">
            <input id="loginInput" type="text" name="login" autocomplete="off" placeholder="Nom d'utilisateur / Adresse Mail"
              value="<?php if (isset($_GET['login'])) echo $_GET['login']; ?>" required>
            <input type="password" name="mdp" placeholder="Mot de Passe" required>
            <div class="submit-line">
              <div id="mdpOublieButton" onclick="mdpOublie()"> Mot de passe oublié ? </div>
              <input id="ConnexionValidButton" type="submit" name="valid" value="Lauch!">
            </div>
          </form>
        </div>
        <div class="panel">
          <h1>Création de la fusée 🛸</h1>
          <form action="authentification.php" method="post">
            <div id="part-1" class="part" style="display: flex;">
              <input type="text" value="<?php if (isset($_GET['prenom'])) echo $_GET['prenom']; ?>" name="InsPrenom" placeholder="Votre prénom" required>
              <input type="text" value="<?php if (isset($_GET['nom'])) echo $_GET['nom']; ?>" name="InsNom" placeholder="Votre nom" required>
            </div>
            <div id="part-2" class="part">
              <input type="text" name="InsLogin" autocomplete="off" onkeyup="verifName(this)" placeholder="Votre login"
              value ="<?php if (isset($_GET['newLogin'])) echo $_GET['newLogin']; ?>" required>
              <p id="VerifNameText"></p>
            </div>
            <div id="part-3" class="part">
              <input type="password" name="InsMdp" placeholder="Mot de Passe" required>
              <input type="password" name="InsConfMdp" placeholder="Confirme ton mot de passe" required>
            </div>
            <div class="submit-line">
              <input id="InscriptionPrecButton" disabled type="button" name="valid" value="Retour" onclick="prec()">
              <input id="InscriptionValidButton" type="button" name="valid" value="Suivant" onclick="next()">
            </div>
          </form>
        </div>
        <!--
        <div class="panel">
          <h1>Essaye !</h1>
          <form action="pages/main/main.php" method="post">
            <input id="EssayerValidButton" type="submit" name="valid" value=" Se Lancer ⚡">
          </form>
        </div>
        -->
      </div>

      <div id="pepite-section">
        <svg class="behind">
          <polygon points="0,0 900,0 600,800 0,800" style="fill: #2980b9;" class="form"/>
          <polygon points="0,0 500,0 800,800 0,800" class="form"/>
        </svg>
      </div>

    </main>

    <nav>
      <a href="#first-section" class="nav-button">
        <div class="nav-button-title"> Qui sommes-nous ? </div>
        <div class="nav-button-visu"> </div>
      </a>
      <a href="#before-container" class="nav-button">
        <div class="nav-button-title"> Se lancez ! </div>
        <div class="nav-button-visu"> </div>
      </a>
      <a href="#pepite-section" class="nav-button">
        <div class="nav-button-title"> D'autres pépites... </div>
        <div class="nav-button-visu"> </div>
      </a>
    </nav>

    <footer>
      <div>© Bubbles 2020-2021</div>
      <div style="font-family:'Solway', sans-serif;">Powered by bigstones.fr</div>
    </footer>

  </body>

  <script type="text/javascript" src="javascript.js"> </script>

</html>
