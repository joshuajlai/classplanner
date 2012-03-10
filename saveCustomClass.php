<?
$isajax=1;
include_once("inc/common.inc.php");
include_once("obj/Classes.obj.php");

if(!isset($_POST['classSymbol'], $_POST['className'])){
	error_log("Request to change class info with improper posts");
}
if(!isset($_SESSION['classlist']['classid']) || !$_SESSION['classlist']['classid']){
	error_log("Attempt to alter a class without class id already in session");
}

if($_SESSION['classlist']['school'] == 'custom'){
	$universityid = " and universityid is null ";
} else {
	$universityid = " and universityid = " . DBSafe($_SESSION['classlist']['school']) . " ";
}
$error="";
if(get_POST('classSymbol') == ""){
	$error="You cannot have a blank class number";
} else if(QuickQuery("select count(*) from class where symbol = '" . DBSafe(get_POST('classSymbol')) . "'". $universityid . " and id != '" . $_SESSION['classlist']['classid'] . "' and userid = " . $_SESSION['userid'])){
	$error="That class symbol is already used";
} else if(!ereg("^[A-Za-z]+.*", get_POST('classSymbol'))){
	$error="Class numbers must begin with a letter";
} else {

	$classid = $_SESSION['classlist']['classid'];
	$class = new Classes($classid);
	$class->symbol = get_POST('classSymbol');
	$class->name = get_POST('className');
	$class->userid = $_SESSION['userid'];

	if($_SESSION['classlist']['school'] == "custom")
		$class->universityid = null;
	else
		$class->universityid = $_SESSION['classlist']['school'];

	if($class->id == "new"){
		$class->create();
	} else {
		$class->update();
	}
	$_SESSION['classlist']['alphabet'] = strtolower(substr($class->symbol, 0, 1));
	?>
	<script>
		updateAlpha('<?=$_SESSION['classlist']['alphabet']?>',<?=$class->id?>);
	</script>
<?
}

if($error){
	?>
	<script>
		alert("<?=$error?>");
	</script>
	<?
}
?>