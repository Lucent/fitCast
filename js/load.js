const CALS_PER_POUND = 3500;
const PREDICT_POUNDS_ERROR = 2.5;
let BMR, offset;

function bootstrap() {
	const weight_eq = regress_property(intake_array.filter(e => e.weight != null), "weight");
	console.log("Refined equation for actual weight", weight_eq);

	store_cumulative_intake(intake_array, 0);
	let intake_eq = regress_property(intake_array.filter(e => e.predicted_regress != null), "predicted_regress");

	BMR = intake_eq[0] - weight_eq[0];
	store_cumulative_intake(intake_array, BMR);
	intake_eq = regress_property(intake_array.filter(e => e.predicted_regress != null), "predicted_regress");
	console.log("Refined equation for predicted weight", intake_eq);
	offset = weight_eq[1] - intake_eq[1];

	store_cumulative_intake(intake_array, BMR);

	document.querySelector("h3 > output").textContent = Math.round(BMR * CALS_PER_POUND);
	draw_chart(offset);
	const r_squared = least_squares(intake_array, offset);
	console.log("r^2 = ", r_squared.toFixed(2));

	colorize_inputs(BMR);
	watch_changes();
}

function colorize_inputs(BMR) {
	const range = ["green", "black", "red"];
	BMR *= CALS_PER_POUND;

	let domain = [BMR * 0.6, BMR, BMR * 1.2];
	const daily_interpolator = d3.scaleLinear().domain(domain).range(range).interpolate(d3.interpolateLab);
	const inputs = document.querySelectorAll("tbody td:first-of-type input");
	for (const input of inputs)
		if (input.value) {
			let color = daily_interpolator(input.value);
			input.style.backgroundColor = color;
			set_text_color.call(input, color);
		}


	domain = [-1 * PREDICT_POUNDS_ERROR, 0, PREDICT_POUNDS_ERROR];
	const cumulative_interpolator = d3.scaleLinear().domain(domain).range(range).interpolate(d3.interpolateLab);
	const outputs = document.querySelectorAll("tbody td:last-of-type output");
	for (const output of outputs) {
		const actual_weight = output.parentNode.previousSibling.querySelector("input");
		if (actual_weight.value) {
			let color = cumulative_interpolator(Number(output.textContent) - Number(actual_weight.value));
			output.parentNode.style.backgroundColor = color;
			set_text_color.call(output.parentNode, color);
		}
	}
}

function store_cumulative_intake(array, BMR) {
	const intake_inputs = [...document.querySelectorAll("tbody td:first-of-type input")].reverse();

	let running_total = 0;
	intake_inputs.forEach(intake_input => {
		const row = intake_input.parentNode.parentNode;
		let index = array.findIndex(e => e.date === row.id);
		if (index === -1)
			index = array.push({ date: row.id }) - 1;
		array.sort((a, b) => (Date.parse(a.date) > Date.parse(b.date)) ? 1 : -1);
		let intake = Number(intake_input.value);
		if (intake_input.value) {
			array[index].intake = intake;
			running_total += intake / CALS_PER_POUND - BMR;
			if (array[index + 1])
				array[index + 1].predicted_regress = running_total;
		}
		if (array[index + 1])
			array[index + 1].predicted = running_total;
		if (array[index].predicted) {
			row.cells[2].querySelector("output").textContent = array[index].predicted.toFixed(1);
			row.cells[4].querySelector("output").textContent = (array[index].predicted + offset).toFixed(1);
		}
	});
}

function watch_changes() {
	const inputs = document.querySelectorAll("tbody input");
	for (const input of inputs) {
		input.addEventListener("change", () => store_cumulative_intake(intake_array, BMR));
		input.addEventListener("change", () => colorize_inputs(BMR));
	}
}

function highest_contrast_color(rgb) {
	const CUTOFF = 0.1791;
	function rgb_to_luminance(rgb) {
		function c_adjust(c) {
			c /= 255;
			if (c <= 0.03928)
				c /= 12.92;
			else
				c = Math.pow((c + 0.055) / 1.055, 2.4);																			 return c;
		}
		return 0.2126 * c_adjust(rgb.r) + 0.7152 * c_adjust(rgb.g) + 0.0722 * c_adjust(rgb.b);
	}
	const lightness = rgb_to_luminance(d3.color(rgb).rgb());
	return lightness > CUTOFF ? "Black" : "White";
}

function set_text_color(color) {
	let contrasting = highest_contrast_color(color || "#FFFFFF");
	this.classList.toggle("Black", contrasting === "Black");
	this.classList.toggle("White", contrasting === "White");
}

function regress_property(array, property) {
	const days_property = array.map((x,y) => [Date.parse(x.date) / 1000 / 60 / 60 / 24, Number(x[property])]);
	const line = regression.linear(days_property, { precision: 5 });
	return line.equation;
}

function draw_chart(offset) {
	intake_array = intake_array.filter(e => e.intake != null || e.weight != null);

	const data = {
		labels: intake_array.map(e => e.date),
		datasets: [{
			label: "Measured weight",
			data: intake_array.map(e => e.weight),
			borderColor: "green"
		},{
			label: "Predicted weight",
			data: intake_array.map(e => e.predicted_regress + offset),
			borderColor: "red"
		}]
	};

	const context = document.getElementById("LineChart").getContext("2d");
	const chart = new Chart(context, {
		type: "line",
		data,
		options: {
			spanGaps: true,
			scales: {
				x: {
					type: 'time'
				}
			}
		}
	});
}

function least_squares(array, offset) {
	array = array.filter(e => e.intake != null && e.weight != null);
	let squares_total = 0;
	let y_values = [];
	array.forEach(e => {
		squares_total += (e.weight - (e.predicted_regress + offset)) ** 2;
		y_values.push(Number(e.weight));
	});
	const avg = y_values.reduce((a, b) => (a + b)) / y_values.length;
	let sst = 0;
	array.forEach(e => sst += (e.weight - avg) ** 2);

	return 1 - squares_total / sst;
}

bootstrap();
