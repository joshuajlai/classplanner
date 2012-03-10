<?
$exterior=true;

include("inc/common.inc.php");

$error="";

if(isset($_GET['logout'])){
	@session_destroy();
}

if(isset($_SESSION['userid'])){
	redirect("main.php");
}

if(isset($_POST['password'])){
	$password = get_magic_quotes_gpc() ? stripslashes($_POST['password']) : $_POST['password'];
	$username = get_magic_quotes_gpc() ? stripslashes($_POST['username']) : $_POST['username'];
	if($userid = QuickQuery("select id from user where username = '" . DBSafe($username) . "' and password=password('" . DBSafe($password) . "')")){
		@session_destroy();
		session_start();
		$_SESSION['userid'] = $userid;
		redirect("main.php");
	} else {
		$error = "Invalid Login/Password Combination";
	}
}

if(is_readable("updatenews.txt")){
	$updatenews = file_get_contents("updatenews.txt");
} else {
	$updatenews = "";
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
	<title>Class Manager Login</title>
	<link rel=stylesheet href="css/style.css" type="text/css">
</head>
<body>
<table width="100%" >
	<tr><td align="left"><img src="img/beta_logo.gif" alt=""></td><td>&nbsp;</td></tr>
	<tr>
		<td valign="top" width="70%">
			<div valign="top">Update News:</div>
			<br>
			<div><?=nl2br($updatenews)?></div>
		</td>
		<td align="right" valign="top" width="30%">
			<table style="border: blue solid 1px">
				<tr>
					<td>
						<div>Welcome to PlanMYClasses</div>
						<br>
						<div>
							<div>Sign In Here:</div>
							<div>
								<form method="Post" name="login" action="index.php">
									<table>
										<tr><td>Login:</td><td><input type='text' name='username' id='username'></td></tr>
										<tr><td>Password:</td><td><input type='password' name='password' id='password'></td></tr>
										<tr><td>&nbsp;</td><td align="right"><? submit("Sign In", "login");?></td></tr>
										<tr><td colspan="2"><div style="color:red"><?=htmlentities($error)?></div></td></tr>
									</table>
								</form>
							</div>
							<div>Don't have an account yet? <a href='createaccount.php'>Click&nbsp;Here!</a></div>
						</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<div style="float:right; font-size: 11px; color:blue">
	<div>Copyright 2008-2009 Joshua J. Lai</div>
	<div>Contact: <a href="mailto:webmaster@planmyclasses.com">webmaster@planmyclasses.com</a></div>
</div>

</body>
</html>