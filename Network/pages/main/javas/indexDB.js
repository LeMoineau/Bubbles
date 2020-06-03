
if (!window.indexedDB) {
  alert("Mince, vous n'autorisez pas ");
} else {
  console.log("Version permettant la gestion de tâche");
}

var db
var dbVersion = 1;
var dbName = "tasksDB";

initTasks();

function initTasks() {

  //On va chercher dans base de donne toutes les tasks et on créer les pastilles en fonction de
  var request = window.indexedDB.open(dbName, dbVersion);

  request.onerror = function(event) {
    console.log("oups pas réussit à ouvrir: " + event.target.errorCode);
  }

  request.onsuccess = function(event) {
    console.log("Accés aux tâches confirmé !");
    db = event.target.result;
    initAllPastilles();
  }

  bubbleIDval = document.querySelector("#info").getAttribute("bubbleid");

  request.onupgradeneeded = function(event) {
    db = event.target.result;

    var objectStore;
    if (!db.objectStoreNames.contains("tasks")) {
      objectStore = db.createObjectStore("tasks", {autoIncrement: true});
    } else {
      objectStore = event.target.transaction.objectStore("tasks");
    }

    if (!objectStore.indexNames.contains("owner")) objectStore.createIndex("owner", "owner", {unique: false});
    if (!objectStore.indexNames.contains("content")) objectStore.createIndex("content", "content", {unique: false});
    if (!objectStore.indexNames.contains("bubbleID")) objectStore.createIndex("bubbleID", "bubbleID", {unique: false});
    if (!objectStore.indexNames.contains("color")) objectStore.createIndex("color", "color", {unique: false});
    if (!objectStore.indexNames.contains("epingle")) objectStore.createIndex("epingle", "epingle", {unique: false});
    if (!objectStore.indexNames.contains("archive")) objectStore.createIndex("archive", "archive", {unique: false});

    console.log("nouveaux index: " + objectStore.indexNames);
    event.target.transaction.oncomplete = function(event) {
        initAllPastilles();
    }
  }

}

function initAllPastilles() {

  bubbles = document.querySelectorAll(".bubble");
  for (b of bubbles) {

    showTasks(b.getAttribute("id") + "");

  }

}

function showTasks(bubbleidval) {

  var nbr_task = 0;
  var objectStore = db.transaction("tasks").objectStore("tasks")
  objectStore.index("bubbleID").getAllKeys(bubbleidval).onsuccess = function(event) {

    result = event.target.result;
    id = getUserID();

    objectStore.index("bubbleID").getAll(bubbleidval).onsuccess = function(event) {

      result2 = event.target.result;
      document.querySelector("#task-container").innerHTML = "";
      var lastArchived;

      for (i = 0; i<result2.length; i++) {

        r = result2[i];
        if (id == r.owner) {
          nbr_task++;

          //CREATION TASK-ITEM
          div = document.createElement("div");
          div.classList.add("task-item");
          div.setAttribute("taskid", result[i]);
          div.setAttribute("place", i);
          div.setAttribute("archive", r.archive);
          div.setAttribute("epingle", r.epingle);
          if (r.epingle) div.classList.add("epingle");
          if (r.archive) div.classList.add("archive");
          div.style["background-color"] = r.color;
          div.innerHTML += '<p title="' + r.content  + '" contenteditable="true" class="task-title" onkeydown="updateTaskContent(event, this)">' + r.content + '</p>'
          + '<div class="task-item-buttons">'
            + '<div class="task-item-button" onclick="epingleTask(this)" title="Epingler"> 📌 </div>'
            + '<div class="task-item-button" onclick="deleteTask(this)" title="Détruire"> 💣 </div>'
            + '<div class="task-item-button" onclick="archiveTask(this)" title="Archiver"> 📦 </div>'
            + '<div class="task-item-button colors" >🔵'
              + '<div class="task-colors">'
                + '<div class="task-color" style="background-color: #2ecc71;" onclick="changeTaskColor(this)"> </div>'
                + '<div class="task-color" style="background-color: #1abc9c;" onclick="changeTaskColor(this)"> </div>'
                + '<div class="task-color" style="background-color: #3498db;" onclick="changeTaskColor(this)"> </div>'
                + '<div class="task-color" style="background-color: #e74c3c;" onclick="changeTaskColor(this)"> </div>'
                + '<div class="task-color" style="background-color: #e67e22;" onclick="changeTaskColor(this)"> </div>'
                + '<div class="task-color" style="background-color: #f1c40f;" onclick="changeTaskColor(this)"> </div>'
                + '<div class="task-color" style="background-color: #f39c12;" onclick="changeTaskColor(this)"> </div>'
                + '<div class="task-color" style="background-color: #e056fd;" onclick="changeTaskColor(this)"> </div>'
                + '<div class="task-color" style="background-color: #8e44ad;" onclick="changeTaskColor(this)"> </div>'
                + '<div class="task-color" style="background-color: #95a5a6;" onclick="changeTaskColor(this)"> </div>'
              + '</div>'
            + '</div>'
          + '</div>';
          taskContainer = document.querySelector("#task-container");
          if (r.epingle) taskContainer.prepend(div)
          else if (r.archive) {
            taskContainer.appendChild(div);
            lastArchived = div;
          } else {
            if (!lastArchived) taskContainer.appendChild(div);
            else taskContainer.insertBefore(div, lastArchived);
          }
        }
      }

      //afficher une pastille
      showPastille(bubbleidval);

      taskMenu = document.querySelector("#task-menu");
      taskMenu.setAttribute("fit-size", 150 + nbr_task*60);
      if (taskMenu.style.height != taskMenu.getAttribute("fit-size") + "px" && taskMenu.style.height != "35px") showSection(taskMenu);

    }
  }

}

function showPastille(bubbleidval) {

  bubble = document.getElementById(bubbleidval);
  target = bubble.querySelector(".task-bubble");
  taskContainer = document.querySelector("#task-container")
  if (taskContainer.children.length > 0) {

    pastille_color = taskContainer.children[0].style.backgroundColor;
    if (target) target.style.backgroundColor = pastille_color;
    else bubble.innerHTML += "<div class='task-bubble' style='background-color: " + pastille_color + "'></div>";

  } else {

    if (target) target.parentElement.removeChild(target);

  }


}

function updateTaskContent(e, ele) {

  if (e.keyCode == 13) { //enter
    var objectStore = db.transaction(["tasks"], "readwrite").objectStore("tasks");
    var taskid = ele.parentElement.getAttribute("taskid") - 0;
    var request = objectStore.get(taskid).onsuccess = function(event) {

      result = event.target.result;
      result.content = ele.innerHTML;

      objectStore.put(result, taskid).onsuccess = function(event) {
        console.log("Content of task #" + ele.parentElement.getAttribute("taskid") + " updated to " + ele.innerHTML + " !");
        ele.setAttribute("title", ele.innerHTML);
      }

    }

    e.preventDefault();
  }

}

function epingleTask(ele) {

  taskItem = ele.parentElement.parentElement;
  taskid = taskItem.getAttribute("taskid") - 0;
  info = taskItem.parentElement.parentElement.parentElement;

  if (taskItem.getAttribute("epingle") == "true") {
    taskItem.setAttribute("epingle", "false");
    taskItem.classList.remove("epingle");
  }

  var objectStore = db.transaction(["tasks"], "readwrite").objectStore("tasks");

  divs = document.querySelectorAll(".task-item");
  for (d of divs) {

    if (d.getAttribute("epingle") == "true") {

      epingleTaskid = d.getAttribute("taskid") - 0;
      objectStore.get(epingleTaskid).onsuccess = function(event) {

        var result = event.target.result;
        if (result.epingle) result.epingle = false;

        objectStore.put(result, epingleTaskid);

      }

    }

  }

  objectStore.get(taskid).onsuccess = function(event) {

    var result = event.target.result;
    if (result.epingle) result.epingle = false;
    else result.epingle = true;
    result.archive = false;

    objectStore.put(result, taskid).onsuccess = function(event) {
      taskContainer = taskItem.parentElement;
      taskContainer.removeChild(taskItem);
      taskContainer.prepend(taskItem); //mettre au debut
      len = document.querySelectorAll(".epingle").length;
      for (i = 0; i<len; i++) {
        d = document.querySelectorAll(".epingle")[0];
        d.classList.remove("epingle");
        d.setAttribute("epingle", "false"); //suppression de tous les autres epingle
      }
      if (taskItem.classList.contains("archive")) taskItem.classList.remove("archive");
      taskItem.setAttribute("archive", "false");
      if (!result.epingle) {
        taskItem.setAttribute("epingle", "false");
      } else {
        taskItem.setAttribute("epingle", "true");
      }
      showTasks(info.getAttribute("bubbleid"));
      showPastille(info.getAttribute("bubbleid"));
    }

  }



}

function archiveTask(ele) {

  taskItem = ele.parentElement.parentElement;
  taskid = taskItem.getAttribute("taskid") - 0;
  info = taskItem.parentElement.parentElement.parentElement;

  var objectStore = db.transaction(["tasks"], "readwrite").objectStore("tasks")
  objectStore.get(taskid).onsuccess = function(event) {

    var result = event.target.result;
    if (result.archive) result.archive = false;
    else result.archive = true;
    result.epingle = false;

    objectStore.put(result, taskid).onsuccess = function(event) {
      taskContainer = taskItem.parentElement;
      taskContainer.removeChild(taskItem);
      taskContainer.appendChild(taskItem);
      taskItem.setAttribute("archive", result.archive);
      if (result.archive) taskItem.classList.add("archive");
      else if (taskItem.classList.contains("archive")) {
        taskItem.classList.remove("archive");
        showTasks(info.getAttribute("bubbleid"));
      }
      taskItem.setAttribute("epingle", "false");
      if (taskItem.classList.contains("epingle")) taskItem.classList.remove("epingle");
      showPastille(info.getAttribute("bubbleid"));
    }

  }

}

function deleteTask(ele) {

  taskItem = ele.parentElement.parentElement;
  taskid = taskItem.getAttribute("taskid") - 0;
  info = taskItem.parentElement.parentElement.parentElement;

  var request = db.transaction(["tasks"], "readwrite").objectStore("tasks").delete(taskid);
  request.onsuccess = function(event) {
    showTasks(info.getAttribute("bubbleid"));
  }

}

function changeTaskColor(ele) {

  taskItem = ele.parentElement.parentElement.parentElement.parentElement;
  taskid = taskItem.getAttribute("taskid") - 0;
  var objectStore = db.transaction("tasks", "readwrite").objectStore("tasks");
  var request = objectStore.get(taskid - 0);

  request.onerror = function(event) {
    console.log("erreur lors de la mise à jour de couleur: " + event.target.errorCode);
  }

  request.onsuccess = function(event) {

    var data = request.result;
    data.color = ele.style["background-color"];

    objectStore.put(data, taskid).onsuccess = function(event) {
        taskItem.style["backgroundColor"] = ele.style["background-color"];
        if (taskItem.parentElement.children[0] == taskItem) showPastille(taskItem.parentElement.parentElement.parentElement.getAttribute("bubbleid"));
    }

  }

}

function addTask(ele) {

  parent = ele.parentElement;
  inp = ele.querySelector("textarea");
  info = parent.parentElement;

  if (inp.value.length > 0) {

    var transaction = db.transaction(["tasks"], "readwrite");

    transaction.oncomplete = function(event) {

      console.log("saved !");
      //Créer task-item

    }

    transaction.onerror = function(event) {
      console.log("erreur lors de la création de la task: " + event.target.errorCode);
    }

    var objectStore = transaction.objectStore("tasks");
    var bubbleid = info.getAttribute("bubbleid");
    var request = objectStore.add({owner: getUserID(), content: inp.value, bubbleID: bubbleid, color: "#bdc3c7", epingle: false, archive: false});

    request.onerror = function(event) {
      console.log("erreur lors de l'ajout dans la base: " + event.target.errorCode);
    }

    request.onsuccess = function(event) {
      showTasks(bubbleid);
      inp.value = "";
    }

  }

}
