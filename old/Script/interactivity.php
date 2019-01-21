"use strict";

window.onerror = function(msg, url, line) {
	var img = new Image();
	img.src = "Images/Spacer.GIF?msg=" + encodeURIComponent(msg) + ';url=' + encodeURIComponent(url) + ";line=" + line;
	img = null;
};

var _gaq = [["_setAccount", "UA-99745-1"], ["_trackPageview"]], _qoptions = { qacct: "p-acpWpszny_8go" };

var fitCast = new function() {
<?php
include "weight_table.js";
include "food_search.js";
include "utility.js";
?>
}; // End fitCast namespace
