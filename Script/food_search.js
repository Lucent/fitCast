var getAjaxObj = function() {
	var xmlhttp, complete = false;
	if (window.XMLHttpRequest)
		xmlhttp = new XMLHttpRequest();
	else if (window.ActiveXObject)
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	else
		return null;

	this.connect = function (sURL, sVars, fnDone) {
		if (!xmlhttp) return false;
		complete = false;
		try {
			xmlhttp.open("GET", sURL + sVars, true);
			xmlhttp.onreadystatechange = function () {
				if (xmlhttp.readyState == 4 && !complete) {
					complete = true;
					fnDone(xmlhttp, sVars);
				}
			};
			xmlhttp.send("");
		}
		catch (e) { return false; }
		return true;
	};
	return this;
};

this.load_details = function(e) {
	e = e || event;
	var search = document.getElementById("SearchInput").value;
	var container = document.getElementById("SearchResults");
	container.innerHTML = "";
	if (search.length >= 3) {
//	if (!waiting) {
//		waiting = true;
//		throb_properties(document.getElementById("PropertyTab"));
		var conn = new getAjaxObj();
		conn.connect("Script/food_search.php?search=", search, populate_search_results);
	}
};

var handleSelectStart = function(e) {
	e.target.dragDrop();
	e.dataTransfer.setData("Text", e.target.id);
	return false;
};

var cancel = function(e) {
	if (e.preventDefault) e.preventDefault();
	e.dataTransfer.dropEffect = "copy";
};

var handleDragStart = function(e) {
	e.dataTransfer.setData("Text", e.target.id);
	e.dataTransfer.effectAllowed = "copy";
};

var handleDrop = function(e) {
	var el = document.createElement("div");
	var id = e.dataTransfer.getData("Text");
	el.innerHTML = document.getElementById(id).innerHTML;
	document.getElementById("DragTarget").appendChild(el);
	e.preventDefault(); // so firefox won't navigate to it
};

var populate_search_results = function(results) {
	var returned_data = JSON.parse(results.responseText);
	var container = document.getElementById("SearchResults");
	container.innerHTML = "";

	for (var item in returned_data) {
		var cont = document.createElement("div");
		cont.innerHTML = returned_data[item]["long"];
		cont.id = returned_data[item]["id"];
		cont.href = "#";
		cont.addEventListener("selectstart", handleSelectStart, false);
		cont.addEventListener("dragstart", handleDragStart, false);
		cont.setAttribute("draggable", "true"); // unnecessary?
		container.appendChild(cont);
	}
};

window.onload = function() {
	document.getElementById("DragTarget").addEventListener("drop", handleDrop, false);
	document.getElementById("DragTarget").addEventListener("dragover", cancel, false);
	document.getElementById("DragTarget").addEventListener("dragenter", cancel, false);
}
