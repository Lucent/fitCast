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
