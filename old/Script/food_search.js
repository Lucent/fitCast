var delimiter = ", ", search_waiting = false;
var KEYPRESS_DELAY = 200;

var getAjaxObj = function() {
	var xmlhttp, complete = false;
	if (window.XMLHttpRequest)
		xmlhttp = new XMLHttpRequest();
	else if (window.ActiveXObject)
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	else
		return null;

	this.connect = function (sURL, sVars, fnDone, el) {
		if (!xmlhttp) return false;
		complete = false;
		try {
			xmlhttp.open("GET", sURL + sVars, true);
			xmlhttp.onreadystatechange = function () {
				if (xmlhttp.readyState == 4 && !complete) {
					complete = true;
					fnDone(xmlhttp, sVars, el);
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

	document.getElementById("FactsHolder").appendChild(document.getElementById("NutritionFacts"));
	if (container.firstChild)
		container.removeChild(container.firstChild);

	if (search.length >= 3) {
		clearTimeout(search_waiting);
		search_waiting = setTimeout(function() { execute_search(search); }, KEYPRESS_DELAY);
	}
};

var execute_search = function(search) {
	var conn = new getAjaxObj();
	conn.connect("Script/food_search.php?search=", search, populate_search_results);
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

	if (returned_data.length) {
		var tree = results_to_nested_list(returned_data);
		while (move_singles_up_level(tree)) { }
		obj_to_dom(tree, container, []);
	} else {
		container.innerHTML = "No results";
	}
};

var results_to_nested_list = function(data) {
	var obj = {};
	for (var item in data) {
		var result = data[item];
		var brand = result["manufacturer"] || "Generic";
		var desc = [].concat(replacements(result["long"]).split(delimiter));

		var pointer = obj;
		for (var x = 0; x < desc.length; x++) {
			var attr = clean_string(desc[x]);
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

var clean_string = function(str) {
	return str.replace(/^([\s]*)|([\s]*)$/g, "");
};

var replacements = function(str) {
	str = str.replace("Cereals ready-to-eat", "Cereals");
	str = str.replace("KELLOGG'S", "KELLOGG");
	return str;
};

var move_singles_up_level = function(obj) {
	var change_made = false;
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

var obj_to_dom = function(obj, dom, count) {
	var count = count.slice();
	count.push(0);

	if (Object.size(obj)) {
		var list = document.createElement("ul");
		for (var node in obj) {
			var item = document.createElement("li");
			if (typeof obj[node] === "number") {
				// item
				var anchor = document.createElement("a");
				anchor.innerHTML = node;
				item.id = obj[node];
				if (item.addEventListener) {
					item.addEventListener("selectstart", handleSelectStart, false);
					item.addEventListener("dragstart", handleDragStart, true);
				} else {
					item.attachEvent("selectstart", handleSelectStart, false);
					item.attachEvent("dragstart", handleDragStart, true);
				}
				item.appendChild(anchor);
				item.onclick = fetch_nutrition_data;
			} else {
				// folder
				count[count.length - 1]++;
				var checkbox = document.createElement("input");
				checkbox.setAttribute("type", "checkbox");
				checkbox.id = "Folder_" + count.join("-");
				var label = document.createElement("label");
				label.innerHTML = node;
				label.htmlFor = "Folder_" + count.join("-");
				item.appendChild(label);
				item.appendChild(checkbox);
			}
			list.appendChild(item);
			obj_to_dom(obj[node], item, count);
		}
		dom.appendChild(list);
	}
};

var fetch_nutrition_data = function(e) {
	var conn = new getAjaxObj();
	conn.connect("Script/nutrition_data.php?id=", this.id, show_nutrition_data, this);
};

var show_nutrition_data = function(data, id, el) {
	var returned_data = JSON.parse(data.responseText);

	fill_nutrition_facts_box(returned_data);
	el.appendChild(document.getElementById("NutritionFacts"));
};

var fill_nutrition_facts_box = function(data) {
	var field_map = [
		["Calories",	"kcal",			""],
		["ServingSize",	"units1",		null],
		["TotalFat",	"fat",			"g"],
		["SaturatedFat","fat_sat",		"g"],
		["Cholesterol",	"cholesterol",	"mg"],
		["Sodium",		"sodium",		"mg"],
		["TotalCarbohydrate",	"carb",	"g"],
		["DietaryFiber","fiber",		"g"],
		["Sugars",		"sugar",		"g"],
		["Protein",		"protein",		"g"]
	];
	for (var row = 0; row < field_map.length; row++) {
		var num = field_map[row];
		if (num[2] !== null)
			document.getElementById(num[0]).innerHTML = Math.round(data[num[1]] * (data.weight1 || 100) / 100) + num[2];
		else
			document.getElementById(num[0]).innerHTML = data[num[1]] || "100g";
	}
};

window.onload = function() {
	if (document.getElementById("DragTarget").addEventListener) {
		document.getElementById("DragTarget").addEventListener("drop", handleDrop, false);
		document.getElementById("DragTarget").addEventListener("dragover", cancel, false);
		document.getElementById("DragTarget").addEventListener("dragenter", cancel, false);
	} else {
		document.getElementById("DragTarget").attachEvent("drop", handleDrop, false);
		document.getElementById("DragTarget").attachEvent("dragover", cancel, false);
		document.getElementById("DragTarget").attachEvent("dragenter", cancel, false);
	}
}
