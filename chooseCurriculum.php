<?
$isajax=1;
include_once("inc/common.inc.php");
include_once("obj/Curriculum.obj.php");

$curriculums = fetchObjects("Curriculum", "from curriculum where userid = " . $_SESSION['userid'] . " order by name ASC");
$defaultCurriculum = QuickQuery("select value from usersetting where name = 'defaultcurriculum' and userid = " . $_SESSION['userid']);
$loadCurriculum=false;
if(isset($_SESSION['curriculummanager']['curriculumid'])){
	$loadCurriculum = $_SESSION['curriculummanager']['curriculumid'];
} else if($defaultCurriculum){
	$loadCurriculum = $defaultCurriculum;
}

?>
<select id="chooseCurriculum" onchange="loadCurriculum(this.value)">
	<option value="">-- Select a Curriculum --</option>
<?
	foreach($curriculums as $curriculum){
		$checked="";
		if(isset($_SESSION['curriculummanager']['curriculumid']) && ($curriculum->id == $_SESSION['curriculummanager']['curriculumid'])){
			$checked = " SELECTED ";
		} else if($curriculum->id == $defaultCurriculum){
			$checked = " SELECTED ";
		}
		?><option value="<?=$curriculum->id?>" <?=$checked?>><?=$curriculum->name?></option><?
	}
?>
</select>
<?
if($loadCurriculum != false){
	?>
		<script>loadCurriculum(<?=$loadCurriculum?>)</script>
	<?
}