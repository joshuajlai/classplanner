<?
$isajax=1;

include_once("inc/common.inc.php");
include_once("obj/Curriculum.obj.php");
include_once("obj/Classes.obj.php");
include_once("obj/University.obj.php");
include_once("obj/ClassTree.obj.php");
include_once("obj/Degree.obj.php");

if(isset($_POST['curriculumid'])){
	if(get_POST('curriculumid') == 'new')
		$_SESSION['curriculummanager']['curriculumid'] = get_POST('curriculumid');
	else
		$_SESSION['curriculummanager']['curriculumid'] = get_POST('curriculumid') + 0;
}

$curriculumid=0;
if(isset($_SESSION['curriculummanager']['curriculumid'])){
	$curriculumid = $_SESSION['curriculummanager']['curriculumid'];
} else {
	exit();
}
$curriculum = new Curriculum($curriculumid);
$degree = new Degree($curriculum->degreeid);
$university = new University($curriculum->universityid);
$checked = "";
if($curriculumid == QuickQuery("select value from usersetting where name = 'defaultcurriculum' and userid = " . $_SESSION['userid'])){
	$checked = " CHECKED ";
}

//initialize the array so php doesn't complain but don't fetch unless necessary.
$universities = array();

if($curriculumid == 'new'){
	$universities = fetchObjects("University", "from university");
} else {
	$classlist = QuickQueryList("select classid from curriculumassociation where curriculumid = " . $curriculumid);
	if($curriculum->universityid == null){
		$universityQuery = " and universityid is null ";
	} else {
		$universityQuery = " and universityid = " . $curriculum->universityid;
	}
	$addableClasses = fetchObjects("Classes", "from class where 1 " . $universityQuery . " and id not in ('" . implode("','", $classlist) . "')");
}
?>

<table  class="classList" width="100%" style="background-color: #cccccc">
	<tr>
		<td>
			<table class="subMenu">
				<tr>
					<th>Curriculum Name:</th>
					<td><input id="curriculumname" type="text" size="30" maxlength="100" value="<?=$curriculum->name?>"></td>
				</tr>
				<tr>
					<th>Default Curriculum:</th>
					<td><input id="defaultcurriculum" type="checkbox" <?=$checked?>></td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td>
			<table>
				<tr>
					<td>
						<table class="subMenu">

							<tr>
								<th align="left">University</th>
								<th align="left">Degree</th>
							</tr>
<?
							if($curriculumid == 'new'){
?>
							<tr>
								<td>
									<select id="chooseUniversity" onchange="displayDegrees(this.value)">
									<option value="">-- Select a University --</option>
									<option value="custom">-- University Not Listed --</option>
									<?
										foreach($universities as $university){
											?><option value="<?=$university->id?>"><?=htmlentities($university->name)?></option><?
										}

									?>
									</select>

								</td>
								<td>
									<div id="degree">
										<select id="chooseDegree" disabled>
											<option value="">Please Choose a University</option>
										</select>
									</div>
								</td>
							</tr>
<?
							} else {
?>

							<tr>
								<td>
									<?
										if($university->name)
											echo $university->name;
										else
											echo "No University Selected";
									?>
								</td>
								<td>
									<?
										if($degree->name)
											echo $degree->name;
										else
											echo "Custom Degree";
									?>
								</td>
							</tr>
<?
							}
?>
						</table>
					</td>
					<td>
						<div id="addCurriculumClassDiv"></div>
						<div id="addCurriculumClassTemp"></div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<div id="curriculumClassList"></div>
		</td>
	</tr>
	<tr>
		<td>
			<table>
				<tr>
					<td>
<?
						if($curriculumid == 'new'){
							$saveButton = "newCurriculum('" . $curriculumid . "')";
						} else {
							$saveButton = "updateCurriculum('" . $curriculumid . "')";
						}
						button("Save", $saveButton);
?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<script>
<?
	if($curriculumid != "new"){
		echo "loadCurriculumAddSelect();";
	}
?>
	curriculumClassList();
</script>