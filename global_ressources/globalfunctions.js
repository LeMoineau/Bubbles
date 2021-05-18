
function ajax(url, method, func) {

  var xhttp = new XMLHttpRequest(); //Création des bulle de suggestions
  xhttp.onreadystatechange = function() {

    if (this.readyState == 4 && this.status == 200) {

      func(this.responseText);

    }

  }
  xhttp.open("GET", url, method); //false = pas d'impulsions | true = impulsion mais pas warning
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send();

}

function getRandomInt(max) {
  return Math.floor(Math.random() * max);
}

function intersection(list1, list2) {

  var inter = null;
  for (var ele of list1) {
    if (list2.includes(ele)) inter = ele;
  }
  return inter;

}

function getUserID() {

  var xhttp = new XMLHttpRequest();
  var id = -1;

  xhttp.onreadystatechange = function() {

    if (this.readyState == 4 && this.status == 200) {
      id = this.responseText;
    }

  };
  xhttp.open("GET", "phps/getID.php", false);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send();

  return id;

}
