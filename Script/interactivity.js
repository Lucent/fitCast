"use strict";
var today = 6;

var calc_color = function(value, start, end, min, max) {
	var n = (value - min) / (max - min), result;
	end = parseInt(end, 16);
	start = parseInt(start, 16);

	result = start + ((( Math.round(((((end & 0xFF0000) >> 16) - ((start & 0xFF0000) >> 16)) * n))) << 16) + (( Math.round(((((end & 0x00FF00) >> 8) - ((start & 0x00FF00) >> 8)) * n))) << 8) + (( Math.round((((end & 0x0000FF) - (start & 0x0000FF)) * n)))));

	return "#" + ((result >= 0x100000) ? "" : (result >= 0x010000) ? "0" : (result >= 0x001000) ? "00" : (result >= 0x000100) ? "000" : (result >= 0x000010) ? "0000" : "00000") + result.toString(16);
};

/*var getminmax = function(values) {
	var minmax = [Number.MAX_VALUE, Number.MIN_VALUE];
	for (var x in valueArray) {
		if (valueArray[x] < minmax[0] && valueArray[x] != -Infinity && !isNaN(valueArray[x]) && valueArray[x] !== "") minmax[0] = valueArray[x];
		if (valueArray[x] > minmax[1] && valueArray[x] != -Infinity && !isNaN(valueArray[x]) && valueArray[x] !== "") minmax[1] = valueArray[x];
	}
	return minmax;
};*/

var approximateFractions = function(d) {
	var numerators = [0, 1];
	var denominators = [1, 0];

	var maxNumerator = getMaxNumerator(d);
	var d2 = d;
	var calcD, prevCalcD = NaN;
	for (var i = 2; i < 1000; i++)	{
		var L2 = Math.floor(d2);
		numerators[i] = L2 * numerators[i-1] + numerators[i-2];
		if (Math.abs(numerators[i]) > maxNumerator) return;

		denominators[i] = L2 * denominators[i-1] + denominators[i-2];

		calcD = numerators[i] / denominators[i];
		if (calcD == prevCalcD) return;

		//appendFractionsOutput(numerators[i], denominators[i]);

		if (calcD == d) return [numerators[i], denominators[i]];

		prevCalcD = calcD;

		d2 = 1/(d2-L2);
	}
};

var getMaxNumerator = function(f) {
	 var f2 = null;
	 var ixe = f.toString().indexOf("E");
	 if (ixe == -1) ixe = f.toString().indexOf("e");
	 if (ixe == -1) f2 = f.toString();
	 else f2 = f.toString().substring(0, ixe);

	 var digits = null;
	 var ix = f2.toString().indexOf(".");
	 if (ix == -1) digits = f2;
	 else if (ix == 0) digits = f2.substring(1, f2.length);
	 else if (ix < f2.length) digits = f2.substring(0, ix) + f2.substring(ix + 1, f2.length);

	 var L = digits;

	 var numDigits = L.toString().length;
	 var L2 = f;
	 var numIntDigits = L2.toString().length;
	 if (L2 == 0) numIntDigits = 0;
	 var numDigitsPastDecimal = numDigits - numIntDigits;

	 for (var i = numDigitsPastDecimal; i > 0 && L % 2 == 0; i--) L /= 2;
	 for (var i = numDigitsPastDecimal; i > 0 && L % 5 == 0; i--) L /= 5;

	 return L;
};

var drawChart = function() {
	var tableData = new google.visualization.arrayToDataTable(data);

	// Set chart options
	var topbuffer = 10, bottombuffer = 10;
	var options = {
		width: (days + 1) * blocksize + leftmargin,
		height: verticalblocks * blocksize + topbuffer + bottombuffer,
		legend: "none",
		chartArea: {
			left: leftmargin,
			top: topbuffer,
			bottom: bottombuffer,
			right: 0,
			width: (days + 1) * blocksize,
			height: verticalblocks * blocksize
		},
		hAxis: {
			gridlines: {
				count: days + 2
			},
			viewWindow: {
				min: startday,
				max: startday + days + 1
			},
			baselineColor: "#CCC"
		},
		vAxis: {
			gridlines: {
				count: verticalblocks + 1
			},
			title: "Weight (lbs)",
			titleTextStyle: {
				fontSize: "16",
				italic: false,
				bold: true
			},
			textStyle: {
				fontSize: 14
			},
			baselineColor: "transparent"
		},
		series: {
			0: { color: actualColor, pointSize: 3 },
			1: { color: measuredColor, lineWidth: 0, pointSize: 7 }
		}
	};

	var chart = new google.visualization.LineChart(document.getElementById("Chart"));
	var formatter2 = new google.visualization.NumberFormat({prefix: 'May ', fractionDigits: 0, suffix: ", 2012"});
	formatter2.format(tableData, 0);
	var formatter = new google.visualization.NumberFormat({suffix: ' lbs', fractionDigits: 1});
	formatter.format(tableData, 1);
	formatter.format(tableData, 2);

	var color_table_row = function(num, color) {
		var rows = document.getElementById("Table").tBodies[0].rows;
		for (var row = 0; row < rows.length; row++) {
			if (rows[row].cells.length === days + 2 && row !== today && num !== 0)
				rows[row].cells[num].style.backgroundColor = color;
		}
	};
	var chart_hover = function(e) {
		color_table_row(e.row + 1, "yellow")
	};
	var chart_leave = function(e) {
		color_table_row(e.row + 1, "")
	};
	var lastClicked;
	var click_chart = function() {
		var selectedItem = chart.getSelection()[0], shift = 7;
		if (lastClicked)
			document.getElementById("Table").tBodies[0].rows[lastClicked.column + shift].cells[lastClicked.row + 1].className = "";
		if (selectedItem) {
			document.getElementById("Table").tBodies[0].rows[selectedItem.column + shift].cells[selectedItem.row + 1].className = "Selected";
			lastClicked = {row: selectedItem.row, column: selectedItem.column};
		}
	}
	var enter_table_row = function(el) {
		el = this || el;
		if (el.colSpan === 1) {
			chart.setSelection([{row: el.cellIndex - 1}]);
			chart_hover({row: el.cellIndex - 1});
		}
	};
	var leave_table_row = function(el) {
		el = this || el;
		if (el.colSpan === 1) {
			chart.setSelection([{}]);
			chart_leave({row: el.cellIndex - 1});
		}
	};
	google.visualization.events.addListener(chart, "onmouseover", chart_hover);
	google.visualization.events.addListener(chart, "onmouseout", chart_leave);
	google.visualization.events.addListener(chart, "select", click_chart);

	chart.draw(tableData, options);

	var rows = document.getElementById("Table").tBodies[0].rows;
	for (var row = 0; row < rows.length; row++) {
		var cells = rows[row].cells;
		for (var cell = 0; cell < cells.length; cell++) {
			better_mouseover(cells[cell], enter_table_row);
			better_mouseout(cells[cell], leave_table_row);
		}
//		tbl[row].onclick = click_chart;
	}
};

var better_mouseover = function(sink, callback) {
	if (typeof sink.onmouseenter !== "undefined")
		sink.onmouseenter = callback;
	else
		sink.onmouseover = function (e) {
			for (var el = e.relatedTarget; el && (el !== sink); el = el.parentNode) {};
			if (!el) callback(e.toElement);
		};
};

var better_mouseout = function(sink, callback) {
	if (typeof sink.onmouseleave !== "undefined")
		sink.onmouseleave = callback;
	else
		sink.onmouseout = function (e) {
			for (var el = e.relatedTarget; el && (el !== sink); el = el.parentNode) {};
			if (!el) callback(e.fromElement);
		};
};

var load_script = function(file) {
	var s = document.createElement("script");
	s.src = file;
	s.setAttribute("async", "true");
	s.type = "text/javascript";
	document.getElementsByTagName("head")[0].appendChild(s);
};

window.onload = function() {
	var chart_lib = "http://www.google.com/jsapi?autoload=" + encodeURIComponent(JSON.stringify({
		"modules": [{
			"name": "visualization",
			"version": "1",
			"packages": ["corechart"],
			"callback": "drawChart"
		}]
	}));
	load_script(chart_lib);

	var cells = document.getElementById("Table").tBodies[0].rows[today].cells;
	var goodColor = "00FF00",
		badColor = "FF0000",
		max = 0.3;

	for (var cell = 0; cell < cells.length; cell++) {
		var todayChgCell = cells[cell];
		var todayChgVal = todayChgCell.innerHTML * 1;
		var negative = todayChgVal < 0;

		// Color background of cells red to green
		if (negative) {
			todayChgVal = Math.max(todayChgVal, -1 * max);
			todayChgCell.style.backgroundColor = calc_color(todayChgVal, "FFFFFF", goodColor, 0, -1 * max);
		} else {
			todayChgVal = Math.min(todayChgVal, max);
			todayChgCell.style.backgroundColor = calc_color(todayChgVal, "FFFFFF", badColor, 0, max);
		}

		// Change numbers to fractions
		var frac = approximateFractions(Math.abs(todayChgVal));
//		todayChgCell.innerHTML = (negative ? "-" : "") + frac[0] + "/" + frac[1];
	}
};
