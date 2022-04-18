const CALS_PER_POUND = 3500;

function bootstrap() {
	const weight_eq = regress_property("weight");
	console.log(weight_eq);

	store_cumulative_intake(0);
	let intake_eq = regress_property("predicted");
	let BMR = intake_eq[0] - weight_eq[0];

	store_cumulative_intake(BMR);
	intake_eq = regress_property("predicted");
	console.log(intake_eq);
	const offset = weight_eq[1] - intake_eq[1];
	predict_from_eq(offset);

	document.querySelector("h3 > output").textContent = BMR * CALS_PER_POUND;
	draw_chart(offset);

	colorize_inputs(BMR);
	watch_changes();
}

function predict_from_eq(offset) {
	const rows = [...document.querySelectorAll("tbody > tr")].reverse();
	rows.forEach(row => {
		const index = intake_array.findIndex(e => e.date === row.id);
		if (intake_array[index].predicted)
			row.cells[4].querySelector("output").textContent = (intake_array[index].predicted + offset).toFixed(1);
	});
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


	domain = [-2.5, 0, 2.5];
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

function store_cumulative_intake(BMR) {
	const has_value = [...document.querySelectorAll("tbody td:first-of-type input:not([value=''])")].reverse();

	let running_total = 0;
	has_value.forEach(input => {
		const row = input.parentNode.parentNode;
		if (row.querySelector("input")) {
			let intake = row.querySelector("input").value * 1;
			const index = intake_array.findIndex(e => e.date === row.id);
			running_total += intake / CALS_PER_POUND - BMR;
			row.cells[2].querySelector("output").textContent = running_total.toFixed(1);
			intake_array[index].predicted = running_total;
		}
	});
}

function watch_changes() {
	const inputs = document.querySelectorAll("tbody input");
	for (const input of inputs) {
		input.addEventListener("change", store_cumulative_intake);
		input.addEventListener("change", colorize_inputs);
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

function regress_property(property) {
	const days_property = intake_array.filter(e => e[property] != null).map((x,y) => [Date.parse(x.date) / 1000 / 60 / 60 / 24, Number(x[property])]);
	return regression.linear(days_property).equation;
}

function draw_chart(offset) {
	intake_array = intake_array.filter(e => e.intake != null || e.weight != null);

	const data = {
		labels: intake_array.map(e => e.date),
		datasets: [{
			label: 'Measured weight',
			data: intake_array.map(e => e.weight),
			borderColor: "green"
		},{
			label: 'Predicted weight',
			data: intake_array.map(e => e.predicted + offset),
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

bootstrap();
