<?
$isajax=1;

include_once("inc/common.inc.php");
include_once("obj/Curriculum.obj.php");
include_once("obj/Classes.obj.php");


$curriculumid = $_SESSION['curriculummanager']['curriculumid'];

$curriculum = new Curriculum($curriculumid);

$classlist = QuickQueryList("select classid from curriculumassociation where curriculumid = " . $curriculum->id);
if($curriculum->universityid == null){
	$universityQuery = " and universityid is null and userid = " . $_SESSION['userid'];
} else {
	$universityQuery = " and universityid = " . $curriculum->universityid;
}
$addableClasses = fetchObjects("Classes", "from class where 1 " . $universityQuery . " and id not in ('" . implode("','", $classlist) . "')");


if($addableClasses){
?>
<table class="subMenu">
	<tr>
		<th align="left">Add Class:</th>
		<td><div id="addClassDiv"></div></td>
	</tr
	<tr>
		<td>
			<select id="addCurriculumClass">
			<?
				foreach($addableClasses as $addClass){
					?><option value="<?=$addClass->id?>"><?=htmlentities($addClass->symbol . ": " . $addClass->name)?></option><?
				}
			?>
			</select>
		</td>
		<td>
			<? button("Add", "curriculumAddClass()"); ?>
		</td>
	</tr>
</table>
<?
} else {
?>
<table class="subMenu">
	<tr>
		<th align="left">Add Class:</th>
	</tr>
	<tr>
		<td>No Classes to Add</td>
	</tr>
</table>
<?
}
?>
