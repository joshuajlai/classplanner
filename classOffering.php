<?
$isajax=1;
include_once("inc/common.inc.php");

if(!isset($_POST['classidnum'], $_POST['sequence'], $_POST['value'])){
	error_log("classOffering called with invalid post data");
	exit();
}


$classid = get_POST('classidnum') + 0;
$sequence = get_POST('sequence') + 0;
$value = get_POST('value');

QuickUpdate("delete from userclassoffering where classid = " . $classid . " and userid = " . $_SESSION['userid'] . " and termsequence = " . $sequence);
if($value == "true"){
	QuickUpdate("insert into userclassoffering (classid, termsequence, available, userid) values (" . $classid . ", " . $sequence . ", 1, " . $_SESSION['userid'] . ")");
} else {
	QuickUpdate("insert into userclassoffering (classid, termsequence, available, userid) values (" . $classid . ", " . $sequence . ", 0, " . $_SESSION['userid'] . ")");
}

?>