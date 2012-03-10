<?
$isajax=1;
include_once("inc/common.inc.php");


$username = "";
$password1 = "";
$password2 = "";

if(isset($_POST['username'], $_POST['password1'], $_POST['password2'])){
	$username = get_POST('username');
	$password1 = get_POST('password1');
	$password2 = get_POST('password2');
}

if($username){
	if(QuickQuery("select count(*) from user where username = '" . DBSafe($username) . "' and id != " . $_SESSION['userid'] )){
		echo "<div float='left'>That login is already used</div>";
	} else {
		QuickUpdate("update user set username = '" . DBSafe($username) . "' where id = " . $_SESSION['userid']);
		echo "<div float='left'>Login Updated</div>";
	}
}

if($password1 != "" || $password2 != ""){
	if($password1 != $password2){
		echo "<div style='color: red' float='left'>Those password do not match</div>";
	} else {
		QuickUpdate("update user set password = password('" . DBSafe($password1) . "') where id = " . $_SESSION['userid']);
		echo "<div float='left'>Password Updated</div>";
	}
}
?>