var addclass = function(el, name) {
	if (!hasclass(el, name))
		el.className += (el.className ? " " : "") + name;
};

var removeclass = function(el, name) {
	if (hasclass(el, name))
		el.className = el.className.replace(new RegExp("(\\s|^)" + name + "(\\s|$)"), " ").replace(/^\s+|\s+$/g, "");
};

var hasclass = function(el, name) {
	return new RegExp("(\\s|^)" + name + "(\\s|$)").test(el.className);
};

Object.size = function(obj) {
	var size = 0, key;
	for (key in obj) {
		if (obj.hasOwnProperty(key)) size++;
	}
	return size;
};

var dump = function(obj, indent) {
	var result = "";
	if (indent == null) indent = "";

	for (var property in obj) {
		var value = obj[property];
		if (typeof value === "string")
			value = "'" + value + "'";
		else if (typeof value == "object") {
			if (value instanceof Array) {
				value = "[ " + value + " ]";
			} else {
				var od = dump(value, indent + "	");
				value = "\n" + indent + "{\n" + od + "\n" + indent + "}";
			}
		}
		result += indent + "'" + property + "' : " + value + ",\n";
	}
	return result.replace(/,\n$/, "");
};
