const CALS_PER_POUND = 3500;

function bootstrap() {
	append_running_total();
	draw_chart();
	colorize_inputs();
	watch_changes();
}

var colorize_inputs = function() {
	const range = ["green", "white", "red"];

	let domain = [BMR - 500, BMR, BMR + 500];
	const daily_interpolator = d3.scaleLinear().domain(domain).range(range).interpolate(d3.interpolateLab);
	const inputs = document.querySelectorAll("tbody input");
	for (const input of inputs)
		if (input.value) {
			let color = daily_interpolator(input.value);
			input.style.backgroundColor = color;
			set_text_color.call(input, color);
		}

/*
	domain = [-5 * POUND, 0, 5 * POUND];
	const cumulative_interpolator = d3.scaleLinear().domain(domain).range(range).interpolate(d3.interpolateLab);
	const outputs = document.querySelectorAll("tbody td:not(:last-of-type) output");
	for (const output of outputs) {
		let color = cumulative_interpolator(output.textContent * 1);
		output.parentNode.style.backgroundColor = color;
		set_text_color.call(output.parentNode, color);
	}
*/
}

var append_running_total = function() {
	const has_value = [...document.querySelectorAll("tbody td:first-of-type input:not([value=''])")].reverse();

	let running_total = 0;
	has_value.forEach(input => {
		const row = input.parentNode.parentNode;
		if (row.querySelector("input")) {
			let intake = row.querySelector("input").value * 1;
			running_total += intake - BMR;
			row.cells[2].querySelector("output").textContent = (running_total / CALS_PER_POUND).toFixed(1);
			const index = intake_array.findIndex(e => e.date === row.id);
			const predicted_delta = (running_total / CALS_PER_POUND);
			intake_array[index].predicted = predicted_delta;
			row.cells[4].querySelector("output").textContent = predicted_delta.toFixed(1);
		}
	});
}

function watch_changes() {
	const inputs = document.querySelectorAll("tbody input");
	for (const input of inputs) {
		input.addEventListener("change", append_running_total);
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

function draw_graph() {
	const width = "4em";
	const height = "500px";

	let svg = d3.select("#LineChart").append("svg")
		.attr("width", width)
		.attr("height", height);
	let x = d3.scaleTime();//.rangeRound([0, width]);
	let y = d3.scaleLinear();//.rangeRound([height, 0]);
	let parse_time = d3.timeParse("%Y-%m-%d");
//	x.domain(d3.extent(intake_array, d => d.date));
//	y.domain([0, d3.max(intake_array, d => d.intake)]);
	let line = d3.line().x(d => x(parse_time(d.date))).y(d => d.intake);

	svg.append("path")
		.datum(intake_array)
		.attr("d", line);
}

function draw_chart() {
	intake_array = intake_array.filter(e => e.intake != null || e.weight != null);

	const days_calories_measured = intake_array.filter(e => e.weight != null).map((x,y) => [Date.parse(x.date) / 1000 / 60 / 60 / 24, Number(x.weight) * CALS_PER_POUND]);
	const measured_range = [Math.min(...days_calories_measured.map(e => e[1])), Math.max(...days_calories_measured.map(e => e[1]))];
	const weight_eq = regression.linear(days_calories_measured).equation;
	console.log(regression.linear(days_calories_measured))

	const days_calories_predicted = intake_array.filter(e => e.predicted != null).map((x,y) => [Date.parse(x.date) / 1000 / 60 / 60 / 24, Number(x.predicted) * CALS_PER_POUND]);
	const predicted_range = [Math.min(...days_calories_predicted.map(e => e[1])), Math.max(...days_calories_predicted.map(e => e[1]))];
	const predict_eq = regression.linear(days_calories_predicted).equation;
	console.log(regression.linear(days_calories_predicted))

	const BMR_adjust = predict_eq[0] - weight_eq[0];
	const graph_offset = predict_eq[1] - weight_eq[1];
	document.querySelector("h3 > output").textContent = BMR + BMR_adjust;

	let range;
	if (predicted_range[1] - predicted_range[0] > measured_range[1] - measured_range[0])
		range = predicted_range[1] - predicted_range[0];
	else
		range = measured_range[1] - measured_range[0];

	const data = {
		labels: intake_array.map(e => e.date),
		datasets: [{
			label: 'Measured weight',
			data: intake_array.map(e => e.weight),
			borderColor: "blue",
			yAxisID: "weight"
		},{
			label: 'Predicted weight',
			data: intake_array.map(e => e.predicted),
			borderColor: "red",
			yAxisID: "predict"
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
				},
				predict: {
					type: "linear",
					display: true,
					position: "left",
					min: (measured_range[0] + graph_offset) / CALS_PER_POUND,
					max: (measured_range[0] + graph_offset + range) / CALS_PER_POUND
				},
				weight: {
					type: "linear",
					display: true,
					position: "right",
					min: measured_range[0] / CALS_PER_POUND,
					max: (measured_range[0] + range) / CALS_PER_POUND
				}
			}
		}
	});

}

bootstrap();
