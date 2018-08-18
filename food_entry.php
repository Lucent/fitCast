<?php $hour_shift = 5; ?>
<!doctype html>
<html>
<head>
<script src="Script/interactivity.php"></script>
<style type="text/css">
/*** Nutrition Facts box ***/
#FactsHolder		{ display: none; }
#NutritionFacts		{ border: thin solid black; font-family: Helvetica, sans-serif; padding: 0 0.5ex; width: 14em; }
#NutritionFacts h1	{ font-family: "Franklin Gothic Heavy", "Helvetica Black"; font-weight: normal; margin: 0; }
#NutritionFacts h2	{ font-weight: normal; margin: 0; font-size: small; }
#NutritionFacts h3	{ border-top: 1em solid black; margin-top: 1ex; padding-top: 1ex; font-size: x-small; border-bottom: thin solid black; }
#NutritionFacts h4	{ margin: 0; padding-top: 1ex; text-align: right; font-size: x-small; border-top: 0.5ex solid black; border-bottom: thin solid black; }
p					{ margin: 0; border-bottom: thin solid black; }
p.Indent			{ margin-left: 1em; }

#DragTarget		{ float: right; width: 50%; height: 200px; }
.Hour			{ border-bottom: thin solid black; height: 2.2em; }
.ist			{ width: 30%; white-space: nowrap; }
.ood			{ text-overflow: ellipsis; overflow: hidden; margin: 2px; border: thin solid transparent; display: block; padding: 0 0.5ex; border-radius: 0.5ex; }
fieldset div:hover	{ overflow: visible; background-color: #EEE; }
.ist div > div		{ margin-left: 1ex; padding-left: 1ex; border-left: thin solid gray; display: none; }
.ist div:hover	> div	{ display: block; }
lgend			{ font-size: large; }

li {
    position: relative;
    margin-left: -15px;
    list-style: none;
}
li[id] {
    margin-left: -1px !important;
}
li[id] a {
    background: url("/images/document.png") 0 -1px no-repeat;
    padding-left: 23px;
    text-decoration: none;
    display: block;
}
li input {
	position: absolute;
	left: 0;
	margin-left: 0;
	opacity: 0;
	z-index: 2;
	cursor: pointer;
	height: 1em;
	width: 1em;
	top: 0;
}
li label {
	background: url("/images/folder-horizontal.png") 15px 1px no-repeat;
	cursor: pointer;
	display: block;
	padding-left: 37px;
}
li input + ul {
    background: url("/images/toggle-small-expand.png") 40px 0 no-repeat;
    margin: -15px 0 0 -44px;
    height: 1em;
}
li input + ul > li { display: none; margin-left: -14px !important; padding-left: 1px; }
li input:checked + ul {
		background: url("/images/toggle-small.png") 40px 5px no-repeat;
		margin: -1.25em 0 0 -44px; /* 20px */
		padding: 1.563em 0 0 80px;
		height: auto;
}
li input:checked + ul > li { display: block; margin: 0 0 2px; }
li input:checked + ul > li:last-child { margin: 0 0 1px; }


[draggable] {
	-moz-user-select: none;
	-khtml-user-select: none;
	-webkit-user-select: none;
	user-select: none;
}
</style>
</head>
<body>
<form>
<input type="text" id="SearchInput">
</form>

<div id="DragTarget" class="List">
<?php for ($hour = 0 + $hour_shift; $hour < 24 + $hour_shift; $hour++) { ?>
<div class="Hour"><?= date("g:i a", mktime($hour, 0, 0, 1, 1, 2012)) ?></div>
<?php } ?>
</div>

<div id="SearchResults" class="List"></div>

<script>
document.getElementById("SearchInput").onkeyup = fitCast.load_details;
</script>

<div id="FactsHolder">
<div id="NutritionFacts">
<h1>Nutrition Facts</h1>
<h2>Serving Size <span id="ServingSize"></span></h2>
<h2>Servings Per Container <span id="ServingsPerContainer"></span></h2>
<h3>Amount Per Serving</h3>
<p><strong>Calories</strong> <span id="Calories"></span></p>
<h4>% Daily Value</h4>
<p><strong>Total Fat</strong> <span id="TotalFat"></span></p>
<p class="Indent">Saturated Fat <span id="SaturatedFat"></span></p>
<p class="Indent"><i>Trans</i> Fat <span id="TransFat"></span></p>
<p><strong>Cholesterol</strong> <span id="Cholesterol"></span></p>
<p><strong>Sodium</strong> <span id="Sodium"></span></p>
<p><strong>Total Carbohydrate</strong> <span id="TotalCarbohydrate"></span></p>
<p class="Indent">Dietary Fiber <span id="DietaryFiber"></span></p>
<p class="Indent">Sugars <span id="Sugars"></span></p>
<p><strong>Protein</strong> <span id="Protein"></span></p>
</div>
</div>

</body>
</html>
