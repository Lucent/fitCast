var delimiter = ", ";

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

	var tree = results_to_nested_list(returned_data);

	while (move_singles_up_level(tree)) { }

	obj_to_dom(tree, container);
};

var results_to_nested_list = function(data) {
	var obj = {};
	for (var item in data) {
		var result = data[item];
		var brand = result["manufacturer"] || "Generic";
		var desc = [brand].concat(result["long"].split(delimiter));

		var pointer = obj;
		for (var x = 0; x < desc.length; x++) {
			var attr = desc[x];
			var just_set_number = false;
			if (!(attr in pointer)) {
				if (x === desc.length - 1) { // if we're at the last level
					pointer[attr] = result["id"];
					just_set_number = true;
				} else
					pointer[attr] = {};
			}
			if (!just_set_number && typeof pointer[attr] === "number")
				pointer[attr] = {"Default": pointer[attr]};
			pointer = pointer[attr];
		}
	}
	return obj;
};

var move_singles_up_level = function(obj) {
	var change_made = false;
	var item = [], x = 0;
	for (var node in obj) {
		var obj2 = obj[node];
		for (var node2 in obj2) {
			var item = obj2[node2];
			if (Object.size(obj2) === 1) {
				obj[node + delimiter + node2] = item;
				delete obj[node];
				change_made = true;
			}
		}
		if (move_singles_up_level(obj2))
			change_made = true;
	}
	return change_made;
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
