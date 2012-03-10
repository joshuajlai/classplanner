<?
//top nav

$MAINNAV = array(
			array("My Plan", "main.php"),
			array("Class Manager", "classes.php"),
			array("Curriculum Manager", "curriculum.php")
			);

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">


<html>
<head>
	<title>PlanMYClasses: <?=$TITLE?></title>
	<link rel=stylesheet href="css/style.css" type="text/css">
	<script src="script/scripts.js" type="text/javascript"></script>
	<script src="script/jquery-1.3.1.js" type="text/javascript"></script>
	<script src="script/jquery-ui-personalized-1.6rc6.js" type="text/javascript"></script>
	
</head>

<body>
<table width="100%">
	<tr>
		<td align="left"><img src="img/beta_logo.gif" alt="..."></td>
		<td align="right" valign="bottom"><a href="index.php?logout=1">Logout</a></td>
	</tr>

</table>
<table class="nav">
	<tr>
		<td>
			<table>
				<tr>
<?
		foreach($MAINNAV as $NAV){
			?><td><a href="<?=$NAV[1]?>"><?=$NAV[0]?></a></td><?
		}
?>
				</tr>
			</table>
		</td>
		<td align="right">
			<table>
				<tr>
					<td>
						<a href="news.php">News</a>
					</td>
					<td align="right">
						<a href="account.php">My Account</a>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<br>
<table width="100%">
	<tr>
		<td>
			<div id="pagetitle" class="pagetitle"><?=$PAGETITLE?></div>
		</td>
	</tr>
	<tr>
		<td>
			<div>