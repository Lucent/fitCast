var calc_color = function(value, start, end, min, max) {
	var n = (value - min) / (max - min), result;
	end = parseInt(end, 16);
	start = parseInt(start, 16);

	result = start + ((( Math.round(((((end & 0xFF0000) >> 16) - ((start & 0xFF0000) >> 16)) * n))) << 16) + (( Math.round(((((end & 0x00FF00) >> 8) - ((start & 0x00FF00) >> 8)) * n))) << 8) + (( Math.round((((end & 0x0000FF) - (start & 0x0000FF)) * n)))));

	return "#" + ((result >= 0x100000) ? "" : (result >= 0x010000) ? "0" : (result >= 0x001000) ? "00" : (result >= 0x000100) ? "000" : (result >= 0x000010) ? "0000" : "00000") + result.toString(16);
}

function approximateFractions(d) {
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
}

function getMaxNumerator(f) {
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

	 for (var i = numDigitsPastDecimal; i > 0 && L%2 == 0; i--) L /= 2;
	 for (var i = numDigitsPastDecimal; i > 0 && L%5 == 0; i--) L /= 5;

	 return L;
}


onload = function() {
	var tbl = document.getElementById("Table").tBodies[0].rows;
	var goodColor = "00FF00", badColor = "FF0000", max = 1;

	for (var row = 0; row < tbl.length; row++) {
		var todayChgCell = tbl[row].cells[7];
		var todayChgVal = todayChgCell.innerHTML;
		var negative = todayChgVal < 0;

		if (negative) {
			if (todayChgVal < -1 * max)
				todayChgCell.style.backgroundColor = "#" + goodColor;
			else
				todayChgCell.style.backgroundColor = calc_color(todayChgVal, "FFFFFF", goodColor, 0, -1 * max);
		} else {
			if (todayChgVal > max)
				todayChgCell.style.backgroundColor = "#" + badColor;
			else
				todayChgCell.style.backgroundColor = calc_color(todayChgVal, "FFFFFF", badColor, 0, max);
		}

		var frac = approximateFractions(Math.abs(todayChgVal));
//		todayChgCell.innerHTML = (negative ? "-" : "") + frac[0] + "/" + frac[1];
	}
};

var data = [];
