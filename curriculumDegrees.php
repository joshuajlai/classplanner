<?
$isajax=1;
include_once("inc/common.inc.php");

$universityid = 0;
if(isset($_POST['universityid'])){
	$universityid = get_POST('universityid');
}

/*
if($universityid == 0){
	error_log("loadDegree called with universityid of " . $_POST['universityid']);
	exit();
}
*/

if($universityid == "custom"){
	$degrees = array();
} else {
	$degrees = QuickQueryList("select id, name from degree where universityid = " . DBSafe($universityid), true);
}
?>
<select id="chooseDegree" onchange="curriculumClassListByCriteria(<?=$universityid?>, this.value)">
	<option value="custom" >-- Custom Degree --</option>
<?
	foreach($degrees as $degreeid => $name){
		?><option value="<?=$degreeid?>" ><?=htmlentities($name)?></option><?
	}
?>
</select>