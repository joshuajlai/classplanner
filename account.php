<?
include_once("inc/common.inc.php");



$username = QuickQuery("select username from user where id = " . $_SESSION['userid']);


$TITLE="My Account";
$PAGETITLE="My Account";
include_once("nav.inc.php");
?>
<table class="tableMenu" width="100%">
	<tr>
		<td>
			<table class="submenu">
				<tr>
					<td>Login:</td>
					<td><input type="text" size="30" maxlength="100" value="<?=$username?>" id="username"></td>
				</tr>
				<tr>
					<td>Change Password:</td>
					<td><input type="password" size="30" maxlength="100" value="" id="password1"></td>
				</tr>
				<tr>
					<td>Confirm Password:</td>
					<td><input type="password" size="30" maxlength="100" value="" id="password2"></td>
				</tr>
				<tr>
					<td></td>
					<td><? button("Save", "updateAccount()");?></td>
				</tr>
				<tr>
					<td align="center" colspan="2">
						<div id="ajaxresponse"></div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<div id="terms"></div>
			<div id="termUpdate"></div>
		</td>
	</tr>
</table>
<?
include_once("navbottom.inc.php");
?>
<script>
	loadTerms();
</script>