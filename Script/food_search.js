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
	e.dataTransfer.effectAllowed = "copy";
	e.dataTransfer.setData("Text", this.innerHTML);
//	this.addClassName('moving');
};

var handleOver = function(e) {
	if (e.preventDefault) e.preventDefault();
	e.dataTransfer.dropEffect = "copy";
};

var handleDrop = function(e) {
	var el = document.createElement("a");
	el.innerHTML = e.dataTransfer.getData("Text");
	document.getElementById("DragTarget").appendChild(el);
	e.dataTransfer.dropEffect = "copy";
};

var populate_search_results = function(results) {
	var returned_data = JSON.parse(results.responseText);
	var container = document.getElementById("SearchResults");
	container.innerHTML = "";

	for (var item in returned_data) {
		var cont = document.createElement("a");
		cont.innerHTML = returned_data[item]["long"];
		var final = container.appendChild(cont);
		final.setAttribute("draggable", "true");
		final.addEventListener("dragstart", handleDragStart, false);
//		box.addEventListener('dragenter', this.handleDragEnter, false);
//		box.addEventListener('dragover', this.handleDragOver, false);
//		box.addEventListener('dragleave', this.handleDragLeave, false);
//		box.addEventListener('drop', this.handleDrop, false);
//		box.addEventListener('dragend', this.handleDragEnd, false);
	}
	document.getElementById("DragTarget").addEventListener("dragover", handleOver, false);
	document.getElementById("DragTarget").addEventListener("drop", handleDrop, false);
};
