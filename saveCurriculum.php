<?

$isajax=1;
include_once("inc/common.inc.php");
include_once("obj/Curriculum.obj.php");
include_once("obj/Classes.obj.php");

$curriculumid = 0;
$curriculum = null;
//exit immediately if no curriculum id
if(!isset($_POST['curriculumid'])){
	error_log("saveCurriculum called without curriculum id");
	exit();
}
$success = true;
$error = "";

//update curriculum based on id
//if already exists, update curriculum name
//else create new curriculum
//ensure user owns curriculum if passed an int
if(get_POST('curriculumid') == 'new'){
	if(!isset($_POST['curriculumname'], $_POST['universityid'], $_POST['degreeid'])){
		error_log("New curriculum attempted to save without name, universityid, and degree id");
		exit();
	} else if(get_POST('curriculumname') == ""){
		$error = "You cannot have a blank curriculum name";
		$success=false;
	} else if(QuickQuery("select count(*) from curriculum where name = '" . DBSafe(get_POST('curriculumname')) . "' and userid is not null ")){
		$error = "That curriculum name is aleady used, please choose another";
		$success = false;
	} else if((get_POST('universityid') != "custom") && ((get_POST('universityid')+0) == 0)){
		$error = "Please choose a university";
		$success = false;
	} else {
		$curriculum = new Curriculum();
		$curriculum->name = get_POST('curriculumname');
		if(get_POST('universityid') == "custom")
			$curriculum->universityid = null;
		else
			$curriculum->universityid = get_POST('universityid')+0;

		if(get_POST('degreeid') == 'custom')
			$curriculum->degreeid = null;
		else
			$curriculum->degreeid = get_POST('degreeid')+0;
			
		$curriculum->userid = $_SESSION['userid'];

		$curriculum->create();
		$_SESSION['curriculummanager']['curriculumid'] = $curriculum->id;
		if($curriculum->universityid && $curriculum->degreeid){
			$copyCurriculumID = QuickQuery("select id from curriculum where universityid = " . $curriculum->universityid . " and degreeid = " . $curriculum->degreeid . " and userid is null");
			QuickUpdate("insert into curriculumassociation select '" . $curriculum->id . "', classid from curriculumassociation where curriculumid = " . $copyCurriculumID);
		}
	}
} else {
	if(!isset($_POST['curriculumname'])){
		error_log("saveCurriculum called without curriculum name");
		exit();
	}
	$curriculum = new Curriculum(get_POST('curriculumid') + 0);
	if($curriculum->userid != $_SESSION['userid']){
		error_log("Attempt to load curriculum that user does not own");
		exit();
	}
	if(get_POST('curriculumname') != $curriculum->name){
		if(QuickQuery("select count(*) from curriculum where name = '" . DBSafe(get_POST('curriculumname')) . "' and userid is not null ")){
			$error = "That curriculum name is aleady used, please choose another";
			$success = false;
		} else {
			$curriculum->name = get_POST('curriculumname');
			$curriculum->update();
			$_SESSION['curriculummanager']['curriculumid'] = $curriculum->id;
		}
	}
}
if($success){
	if(isset($_POST['defaultcurriculum'])){
		if(get_POST('defaultcurriculum')=="true"){
			if(QuickQuery("select count(*) from usersetting where name = 'defaultcurriculum' and userid = " . $_SESSION['userid'])){
				QuickUpdate("update usersetting set value = '" . $curriculum->id . "' where name = 'defaultcurriculum' and userid = " . $_SESSION['userid']);
			} else {
				QuickUpdate("insert into usersetting values (" . $_SESSION['userid'] . ", 'defaultcurriculum', '" . $curriculum->id . "')");
			}
		} else if(get_POST('defaultcurriculum')=="false"){
			QuickUpdate("delete from usersetting where name = 'defaultcurriculum' and value = '" . $curriculum->id . "' and userid = " . $_SESSION['userid']);
		}
	}
}


if($success){
?>
	<script>
		loadCurriculumSelect();
	</script>
<?
} else {
?>
	<script>
		alert("<?=$error?>")
	</script>
<?
}
?>