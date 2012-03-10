<?
include_once("inc/common.inc.php");
include_once("obj/Curriculum.obj.php");

$_SESSION['curriculummanager'] = array();

$curriculums = fetchObjects("Curriculum", "from curriculum where userid = " . $_SESSION['userid']);

$TITLE="Curriculum Manager";
$PAGETITLE="Curriculum Manager";
include_once("nav.inc.php");
?>
<script src="script/curriculumScripts.js" language="javascript" type="text/javascript"></script>
<script src="script/classtreeScripts.js" language="javascript" type="text/javascript"></script>


<div>
	Welcome, please select one of your saved curriculums or create a new one.
</div>

<table width="100%" class="tableMenu">
	<tr>
		<td>
			<table>
				<tr>
					<td>
						<div id="chooseCurriculumDiv"></div>
					</td>
					<td>
						<?=button("New", "loadCurriculum('new')")?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<div id="curriculum"></div>
			<div id="saveCurriculum"></div>
		</td>
	</tr>
</table>
<?
include_once("navbottom.inc.php");
?>
<script>
	loadCurriculumSelect();
</script>