<?
$exterior=true;

include("inc/common.inc.php");

$error="";
$username="";
$success = false;

if(isset($_POST['password1'])){
	$username = get_magic_quotes_gpc() ? stripslashes($_POST['username']) : $_POST['username'];
	$password1 = get_magic_quotes_gpc() ? stripslashes($_POST['password1']) : $_POST['password1'];
	$password2 = get_magic_quotes_gpc() ? stripslashes($_POST['password2']) : $_POST['password2'];
	if($username == ""){
		$error="Login name cannot be blank";
	} else if($password1 == "" || $password2 == ""){
		$error="Password cannot be blank";
	} else if($password1 != $password2){
		$error="Passwords do not match";
	} else if(QuickQuery("select count(*) from user where username='" . DBSafe($username) . "'")){
		$error="That username is already taken";
	} else {
		QuickUpdate("insert into user (username, password) values ('" . DBSafe($username) . "', password('" . DBSafe($password1) . "'))");
		$userid = mysql_insert_id();
		QuickUpdate("insert into usersetting (userid, name, value) values ('" . $userid . "', 'terms', '4'), ('" . $userid . "', 'startingterm', '3')");
		$terms = array("Winter", "Spring", "Summer", "Fall");
		$termquery = array();
		foreach($terms as $sequence => $name){
			$termquery[] = "('" . $userid . "', '" . $name ."', " . $sequence . ")";
		}
		QuickUpdate("insert into userterm (userid, name, sequence) values " . implode(", ", $termquery));
		
		$success=true;
	}
}

?>

<html>
<head>
	<link rel=stylesheet href="css/style.css" type="text/css">
</head>
<title>Class Manager: Create Account</title>
<body>
	<table width="100%">
		<tr><td align="center"><img src="img/logo.gif" alt=""></td></tr>
		<tr>
			<td align="center">
<?
	if(!$success){
?>
	<div>Create an account for free!
	<form method="Post" name="createaccount">
		<table>
			<tr><td>Desired Login:</td><td><input type='text' name='username' id='username' value="<?=$username?>"></td></tr>
			<tr><td>Password:</td><td><input type='password' name='password1' id='password1'></td></tr>
			<tr><td>Confirm Password:</td><td><input type='password' name='password2' id='password2'></td></tr>
			<tr>
				<td colspan='2' align="right">
					<table>
						<tr>
							<td>
								<? button("Back", "window.location='index.php'")?>
							</td>
							<td>
								<? button("Create Account", "document.createaccount.submit()")?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</form>
	<?
		if($error){
			?><div style="color:red"><?=$error?></div><?
		}
	?>
		<div>Disclaimer: This is an open beta.  At the end of the open beta, accounts may or may not be deleted along with custom classes and curriculums.</div>
	<?
	} else {
?>
	<div>Account Created</div>
	<div>You will be redirected in 10 seconds to the login page.</div>
	<div>Or you can go there <a href="index.php">Now</a></div>
	<meta http-equiv="refresh" content="10;url=index.php">
<?

	}
?>
			</td>
		</tr>
	</table>
</body>
</html>