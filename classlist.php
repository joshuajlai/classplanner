<?
$isajax=1;
include("inc/common.inc.php");
include("obj/Classes.obj.php");

$query = "and 0";

if(!isset($_SESSION['classlist'])){
	$_SESSION['classlist'] = array();
	$_SESSION['classlist']['alphabet'] = "a";
	$_SESSION['classlist']['classid'] = 0;
	$_SESSION['classlist']['school'] = "";
}

if(isset($_POST['school']) && (get_POST('school') != $_SESSION['classlist']['school'])){
	$_SESSION['classlist']['school'] = get_POST('school');
	$_SESSION['classlist']['alphabet'] = 'a';
	$_SESSION['classlist']['classid'] = 0;
}

if(isset($_POST['alphabet'])){
	$_SESSION['classlist']['alphabet'] = get_POST('alphabet');
	$_SESSION['classlist']['classid']= 0;
}

if(isset($_POST['classidnum'])){
	$_SESSION['classlist']['classid']=get_POST('classidnum')+0;
}

if($_SESSION['classlist']['school']===""){
	exit();
}

if(isset($_SESSION['classlist']['school'])){
	if($_SESSION['classlist']['school'] == "custom"){
		$query = " and userid = '" . $_SESSION['userid'] . "' and universityid is null ";
	} else {
		$query = " and universityid = '" . DBSafe($_SESSION['classlist']['school']) . "' and (userid is null || userid = '" . $_SESSION['userid'] . "') ";
	}
}

$query .= " and c.symbol like '" . DBSafe($_SESSION['classlist']['alphabet']) . "%' order by symbol ASC";

$classes = fetchObjects("Classes", "from class c where 1 " . $query);
?>

<table class="classlist">
	<tr>
		<td colspan="3">
			<table class="subMenu">
				<tr>
<?
					// iterate through all ascii lower case characters
					for ($c=97; $c <= 122; $c++){
						if($_SESSION['classlist']['alphabet'] == chr($c)){
							$style = " style='background-color: #11ffff;' ";
						} else {
							$style = "";
						}
						?><td><div class="changeClass" onclick="changeAlpha('<?=chr($c)?>');" <?=$style?>><?=ucfirst(chr($c))?></div></td><?
					}
?>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<th class="tableHeader">Number</th>
		<th class="tableHeader">Name</th>
		<th class="tableHeader">Custom Class</th>
	</tr>
<?
	$alt=0;
	foreach($classes as $class){
		$cssStyle='class="classListTR"';
		$style = "";
		//if($class->id == $_SESSION['classlist']['classid']){
		//	$style = "style='background-color: 1199ff'";
		//}
		if($alt%2)
			$cssStyle = 'class="classListTRAlt"';
		?>
			<tr <?=$cssStyle?> id="<?=$class->id?>" onclick="loadClassDetails('<?=$class->id?>');" <?=$style?> >
				<td><?=$class->symbol?></td>
				<td><?=$class->name?></td>
				<td><?
					if($class->userid){
						echo "Custom";
					}
					?>
				</td>
			</tr>
		<?
		$alt++;
	}
?>
</table>

<script type="text/javascript">
<?
	if($_SESSION['classlist']['classid']){
?>
		loadClassDetails("<?=$_SESSION['classlist']['classid']?>");
<?
	}
?>
</script>