<? $hour_shift = 5; ?>
<!doctype html>
<html>
<head>
<script src="Script/interactivity.php"></script>
<style type="text/css">
#DragTarget		{ float: right; width: 50%; height: 200px; }
.Hour			{ border-bottom: thin solid black; height: 2.2em; }
.List div span	{ display: block; }
.List			{ width: 30%; white-space: nowrap; }
.List .Food		{ text-overflow: ellipsis; overflow: hidden; margin: 2px; border: thin solid transparent; display: block; padding: 0 0.5ex; border-radius: 0.5ex; }
.List div:hover	{ overflow: visible; border: thin solid #CCC; background-color: #EEE; }
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
