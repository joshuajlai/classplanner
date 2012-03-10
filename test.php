<?
/*
include_once("inc/common.inc.php");
include_once("obj/Classes.obj.php");
include_once("obj/Curriculum.obj.php");
include_once("obj/ClassTree.obj.php");

$curriculumid=2;
$curriculum = ClassTree::fetchCurriculum($curriculumid);


include_once("nav.inc.php");
?>
<script src="script/curriculumScripts.js" language="javascript" type="text/javascript"></script>
<script src="script/classtreeScripts.js" language="javascript" type="text/javascript"></script>

<table class="submenu">
<?
ClassTree::displayCurriculum($curriculum);
?>
</table>

<?


//var_dump(ereg("^[A-Za-z]+.*", "122"));
*/

$file = '<a href="testing">HELLO</a>';
$spe = utf8_encode($file);
$html = htmlentities($file, ENT_NOQUOTES);
$json = json_encode($file);
$bad = '"<a href=\"testing\">HELLO<\/a>"';
?>
<html>
<body>
	<span id='test3'><?=$spe?></span>
	<br>
	<span id='test'><?=$bad?></span>
	<br>
	<span id='test2'></span>

<script src="http://yui.yahooapis.com/2.7.0/build/yahoo/yahoo-min.js"></script> 
<script src="http://yui.yahooapis.com/2.7.0/build/json/json-min.js"></script> 
<script>
	var temp = <?=$json?>;
	console.log(temp);
	var raw = document.getElementById('test').innerHTML;
	console.log(raw);
	dump(raw);
	//var parse = YAHOO.lang.JSON.parse(temp);
	//console.log(parse);
	//dump(parse);
</script>
</body>
</html>