<? $hour_shift = 5; ?>
<!doctype html>
<html>
<head>
<script src="Script/interactivity.php"></script>
<style type="text/css">
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
    padding-left: 21px;
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
    margin: -0.938em 0 0 -44px; /* 15px */
    height: 1em;
}
li input + ul > li { display: none; margin-left: -14px !important; padding-left: 1px; }
li input:checked + ul {
		background: url("/images/toggle-small.png") 40px 5px no-repeat;
		margin: -1.25em 0 0 -44px; /* 20px */
		padding: 1.563em 0 0 80px;
		height: auto;
}
li input:checked + ul > li { display: block; margin: 0 0 0.125em;  /* 2px */}
li input:checked + ul > li:last-child { margin: 0 0 0.063em; /* 1px */ }


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
<? for ($hour = 0 + $hour_shift; $hour < 24 + $hour_shift; $hour++) { ?>
<div class="Hour"><?= date("g:i a", mktime($hour, 0, 0, 1, 1, 2012)) ?></div>
<? } ?>
</div>

<div id="SearchResults" class="List"></div>

<script>
document.getElementById("SearchInput").onkeyup = fitCast.load_details;
</script>
</body>
</html>
