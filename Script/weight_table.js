var food, exercise, net, change, actual, measured;
var tableData, chart, options;

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

this.drawChart = function() {
	var topbuffer = 10, bottombuffer = 10;
	options = {
		animation: {
			duration: 500,
			easing: "inAndOut"
		},
		width: (days + 1) * blocksize + leftmargin,
		height: verticalblocks * blocksize + topbuffer + bottombuffer,
		legend: "none",
		focusTarget: "category",
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
				color: "#CACACA",
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
				color: "#CACACA",
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

	tableData = new google.visualization.arrayToDataTable(data);

	chart = new google.visualization.LineChart(document.getElementById("Chart"));
//	var formatter2 = new google.visualization.DateFormat({});
//	formatter2.format(tableData, 0);
	var format_weight = new google.visualization.NumberFormat({suffix: ' lbs', fractionDigits: 1});
	format_weight.format(tableData, 1);
	format_weight.format(tableData, 2);

	var color_table_row = function(num, color) {
		var rows = document.getElementById("Table").tBodies[0].rows;
		for (var row = 0; row < rows.length; row++) {
			if (rows[row].cells.length === days + 2 && row !== change && num !== 0)
				rows[row].cells[num].style.backgroundColor = color;
		}
	};
	var chart_hover = function(e) {
		color_table_row(e.row + 1, "yellow")
	};
	var chart_leave = function(e) {
		color_table_row(e.row + 1, "")
	};
	var enter_table_row = function(e) {
		e = e || event;
		var el = (e.srcElement || e.target);
		console.log("entering " + el.cellIndex);
		if (el.colSpan === 1) {
			chart.setSelection([{row: el.cellIndex - 1}]);
			chart_hover({row: el.cellIndex - 1});
		}
	};
	var leave_table_row = function(e) {
		e = e || event;
		var el = (e.srcElement || e.target);
		console.log("leaving " + el.cellIndex);
		if (el.colSpan === 1) {
			chart.setSelection([{}]);
			chart_leave({row: el.cellIndex - 1});
		}
	};
	google.visualization.events.addListener(chart, "onmouseover", chart_hover);
	google.visualization.events.addListener(chart, "onmouseout", chart_leave);

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

	var inputs = document.getElementById("Table").getElementsByTagName("input");
	for (var cell = 0; cell < inputs.length; cell++) {
		inputs[cell].onchange = update_chart;
		inputs[cell].onkeypress = number_typed;
	}
};

var number_typed = function(e) {
	var ZERO = 48, NINE = 57, DOT = 46;
	var code = e ? e.which : event.keyCode;
	return (code < 31 || (code >= ZERO && code <= NINE) || code === DOT);
};

var update_chart = function(e) {
	e = e || event;
	var el = (e.srcElement || e.target);
	var map = {};
	map[measured] = 2;

	var col = el.parentNode.cellIndex - 1;
	var row = el.parentNode.parentNode.rowIndex;

	if (row === food || row === exercise) {
		var cells = document.getElementById("Table").rows[food].cells.length;
		for (var x = col; x < cells - 1; x++)
			recalculate_net(x);
	} else
		tableData.setValue(col, map[row], Number(el.value) || null);

	draw_change_colors();
	chart.draw(tableData, options);
};

var recalculate_net = function(col) {
	var table = document.getElementById("Table").rows;
	var food_val = Number(table[food].cells[col+1].firstChild.value);
	var exercise_val = Number(table[exercise].cells[col+1].firstChild.value);
	var net_val = food_val - exercise_val;
	var actual_yesterday_val = Number(table[actual].cells[col].getAttribute("noround"));
	var bmr = expenditure(metabolism["sex"], actual_yesterday_val, Number(metabolism["height"]), Number(metabolism["age"]), Number(metabolism["lifestyle"]));
	if (net_val == 0)
		var change_val = 0;
	else
		var change_val = (net_val - bmr) / 3500;
	var actual_val = actual_yesterday_val + change_val;

	table[net].cells[col+1].innerHTML = net_val;
	if (col !== 0) {
		table[change].cells[col+1].innerHTML = change_val.toFixed(2);
		table[actual].cells[col+1].innerHTML = actual_val.toFixed(1);
		table[actual].cells[col+1].setAttribute("noround", actual_val);

		tableData.setValue(col, 1, actual_val || null);
	}
}

var expenditure = function(sex, weight, height, age, lifestyle) {
    switch (sex) {
        case "male":
            return (66 + 6.23 * weight + 12.7 * height - 6.76 * age) * lifestyle;
        case "female":
            return (655 + 4.35 * weight + 4.7 * height - 4.7 * age) * lifestyle;
    }
}

var better_mouseover = function(sink, callback) {
	if (typeof sink.onmouseenter !== "undefined")
		sink.onmouseenter = callback;
	else
		sink.onmouseover = function (e) {
			for (var el = e.relatedTarget; el && el !== sink; el = el.parentNode) {};
			if (!el && e.fromElement.cellIndex !== e.toElement.cellIndex) callback(e);
		};
};

var better_mouseout = function(sink, callback) {
	if (typeof sink.onmouseleave !== "undefined")
		sink.onmouseleave = callback;
	else
		sink.onmouseout = function (e) {
			for (var el = e.relatedTarget; el && el !== sink; el = el.parentNode) {};
			if (!el && e.fromElement.cellIndex !== e.toElement.cellIndex) callback(e);
		};
};

var load_script = function(file) {
	var s = document.createElement("script");
	s.src = file;
	s.setAttribute("async", "true");
	s.type = "text/javascript";
	document.getElementsByTagName("head")[0].appendChild(s);
};

var draw_change_colors = function() {
	var cells = document.getElementById("Table").tBodies[0].rows[change].cells;
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

this.draw_chart_page = function() {
	food = document.getElementById("Food").rowIndex;
	exercise = document.getElementById("Exercise").rowIndex;
	net = document.getElementById("Net").rowIndex;
	change = document.getElementById("Change").rowIndex;
	actual = document.getElementById("Actual").rowIndex;
	measured = document.getElementById("Measured").rowIndex;

	var chart_lib = "http://www.google.com/jsapi?autoload=" + encodeURIComponent(JSON.stringify({
		"modules": [{
			"name": "visualization",
			"version": "1",
			"packages": ["corechart"],
			"callback": "fitCast.drawChart"
		}]
	}));
	load_script(chart_lib);

	draw_change_colors();
};
