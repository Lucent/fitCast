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

var handleDragStart = function(e) {
	e.preventDefault();
	e.dataTransfer.effectAllowed = "copy";
	e.dataTransfer.setData("Text", this.innerHTML);
//	this.addClassName('moving');
};

var cancel = function(e) {
	if (e.preventDefault) e.preventDefault();
	return false;
};

var handleDrop = function(e) {
	if (e.preventDefault) e.preventDefault();
	var el = document.createElement("a");
	el.innerHTML = e.dataTransfer.getData("Text");
	document.getElementById("DragTarget").appendChild(el);
//	e.dataTransfer.dropEffect = "copy";
};

var populate_search_results = function(results) {
	var returned_data = JSON.parse(results.responseText);
	var container = document.getElementById("SearchResults");
	container.innerHTML = "";

	for (var item in returned_data) {
		var cont = document.createElement("a");
		cont.innerHTML = returned_data[item]["long"];
		cont.href = "#";
		var final = container.appendChild(cont);
		final.setAttribute("draggable", "true"); // unnecessary?
	}
};

window.onload = function() {
	document.getElementById("DragTarget").addEventListener("drop", handleDrop, false);
	document.getElementById("DragTarget").addEventListener("dragover", cancel, false);
	document.getElementById("DragTarget").addEventListener("dragenter", cancel, false);
}
