<?
$isajax=1;
include_once("inc/common.inc.php");
include_once("obj/Classes.obj.php");

//if class remove successful, display success
//if class is part of a curriculum or a prerequisite to another class, show error
$classid=0;
if(isset($_SESSION['classlist']['classid'])){
	$classid = $_SESSION['classlist']['classid']+0;
}

if($classid == 0){
	error_log("deleteClass called with invalid class id");
	exit();
}

$class = new Classes($classid);
if($class->userid == null){
	echo "You do not have permission to delete this class";
} else if(QuickQuery("select count(*) from prerequisite where prerequisite = " . $class->id)){
	echo "This class is a prerequisite for another class and cannot be deleted";
} else if(QuickQuery("select count(*) from curriculumassociation where classid = " . $class->id)){
	echo "This class is part of a curriculum.  Please remove it from the curriculum before preceding";
} else {
	QuickUpdate("begin");
	QuickUpdate("delete from classoffering where classid = " . $class->id);
	QuickUpdate("delete from class where id = " . $class->id);
	QuickUpdate("delete from prerequisite where classid = " . $class->id);
	QuickUpdate("commit");
	?>
	<script>
		changeAlpha('<?=$_SESSION['classlist']['alphabet']?>');
	</script>
	<?
}
?>
