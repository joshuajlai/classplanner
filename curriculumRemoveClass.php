<?
$isajax=1;

include_once("inc/common.inc.php");
include_once("obj/Classes.obj.php");
include_once("obj/Curriculum.obj.php");

$classid=0;
if(isset($_POST['classidnum'])){
	$classid = get_POST('classidnum')+0;
}

if($classid == 0){
	error_log("curriculumRemoveClass called with invalid class id: " . $_POST['classidnum']);
	exit();
}

if(!isset($_SESSION['curriculummanager']['curriculumid'])){
	error_log("curriculumRemoveClass called with no curriculum id in session");
	exit();
}
$curriculumid = $_SESSION['curriculummanager']['curriculumid'];
$error = "";
$classlist = QuickQueryList("select classid from curriculumassociation where curriculumid = " . $curriculumid);
if(QuickQuery("select count(*) from prerequisite where prerequisite = " . $classid . " . and classid in (" . implode(", ", $classlist) . ")")){
	$error = "That class is a prerequisite for other classes in this curriculum and cannot be removed";
} else {
	QuickUpdate("delete from curriculumassociation where curriculumid = " . $curriculumid . " and classid = " . $classid);
}

if($error){
?>
	<script>
		alert('<?=$error?>');
	</script>
<?
} else {
?>
	<script>
		loadCurriculumAddSelect();
		curriculumClassList();
	</script>
<?
}
?>