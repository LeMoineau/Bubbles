
var etape_tuto = 0;

var titles = ["Présentation"]
var paras = ["Pour commencer, vous pouvez ajouter un membre à votre Réseau !"]

function next_tuto() {

  var tuto = document.getElementById("tuto");
  var para = document.getElementById("tuto-para");
  var title = document.getElementById("tuto-title");

  etape_tuto++;
  title.innerHTML = titles[etape_tuto%titles.length];
  para.innerHTML = paras[etape_tuto%paras.length];

}

function ignore_tuto() {

  var tuto = document.getElementById("tuto");
  tuto.parentElement.removeChild(tuto);

}

function generate_tuto() {

  var tuto = document.createElement("div");
  tuto.id = "tuto";
  tuto.innerHTML = ' <h1 id="tuto-title">Bienvenue !</h1> '
   + '<p id="tuto-para">Voici pour votre plus grand bonheur la plateforme de gestion de réseau <b>"Bubbles"</b> !</p>'
   + '<div class="tuto-bottom">'
     + '<div class="tuto-ignore" onclick="ignore_tuto()"> Ignorer le Didactitiel </div>'
     + '<div class="tuto-next" onclick="next_tuto()"> Suivant </div>'
   + '</div>';
  document.body.appendChild(tuto);

}
