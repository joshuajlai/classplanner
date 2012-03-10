<?
$isajax=1;
include_once("inc/common.inc.php");
include_once("obj/Curriculum.obj.php");


if(!isset($_POST['classidnum'])){
	error_log("Add class to curriculum called without class id");
	exit();
}

global $associated;
$classid = get_POST('classidnum') + 0;
$associated = QuickQueryList("select classid, '1' from curriculumassociation where curriculumid = " . $_SESSION['curriculummanager']['curriculumid'], true);
$addClasses = addPrerequisites($classid);

//var_dump($addClasses);
QuickUpdate("insert into curriculumassociation (curriculumid, classid) values " . implode(", ", $addClasses));


function addPrerequisites($classid){
	global $associated;
	$prerequisites = QuickQueryList("select prerequisite from prerequisite where classid = " . $classid);
	$addClasses = array();
	foreach($prerequisites as $prerequisite){
		if(!isset($associated[$prerequisite])){
			$addClasses = array_merge($addClasses, addPrerequisites($prerequisite));
		}
	}
	$addClasses[] = "(" . $_SESSION['curriculummanager']['curriculumid'] . ", " . $classid . ")";
	$associated[$classid] = true;
	return $addClasses;
}


?>
<script>
	curriculumClassList();
	loadCurriculumAddSelect();
</script>
<?

?>