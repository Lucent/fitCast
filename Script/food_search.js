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
	return false;
};

var cancel = function(e) {
	if (e.preventDefault) e.preventDefault();
	e.dataTransfer.dropEffect = "copy";
};

var handleDragStart = function(e) {
	e.dataTransfer.setData("Text", this.id);
	e.dataTransfer.effectAllowed = "copy";
};

var handleDrop = function(e) {
	var el = document.createElement("div");
	var id = e.dataTransfer.getData("Text");
	el.innerHTML = document.getElementById(id).innerHTML;
	e.target.appendChild(el);
	if (e.preventDefault) e.preventDefault(); // so firefox won't navigate to it
};

var populate_search_results = function(results) {
	var returned_data = JSON.parse(results.responseText);
	var container = document.getElementById("SearchResults");
	container.innerHTML = "";

	var tree = {};
	for (var item in returned_data) {
		var result = returned_data[item];
		var brand = result["manufacturer"] || "Generic";
		var desc = [brand].concat(result["long"].split(", "));

		var pointer = tree;
		for (var x = 0; x < desc.length; x++) {
			var attr = desc[x];
			if (!(attr in pointer)) {
				if (x === desc.length - 1)
					pointer[attr] = result["id"];
				else
					pointer[attr] = {};
			}
			var pointer = pointer[attr];
		}
	}

	obj_to_dom(tree, container);
//	console.log(dump(tree, "\t"));
};

var obj_to_dom = function(obj, dom) {
	var item = [], x = 0;
	for (var node in obj) {
		item[x] = document.createElement("div");
		item[x].innerHTML = node;
		if (typeof obj[node] === "number") {
			item[x].id = obj[node];
			item[x].addEventListener("selectstart", handleSelectStart, false);
			item[x].addEventListener("dragstart", handleDragStart, true);
		}
		dom.appendChild(item[x]);
		obj_to_dom(obj[node], item[x]);
	}
};

window.onload = function() {
	document.getElementById("DragTarget").addEventListener("drop", handleDrop, false);
	document.getElementById("DragTarget").addEventListener("dragover", cancel, false);
	document.getElementById("DragTarget").addEventListener("dragenter", cancel, false);
}
