<?
$isajax=1;
include_once("inc/common.inc.php");
include_once("obj/Classes.obj.php");

$classId = "";

if(isset($_POST['classidnum'])){
	$classId = get_POST('classidnum');
}

if($classId == ""){
	exit();
}

$class = new Classes($classId);
$_SESSION['classlist']['classid'] = $class->id;
if($classId == "new"){
	$class->userid=$_SESSION['userid'];
}

//fetch term information based on the class
//if class has a university id, base term names off main terms table
//else use user terms

//fetch max terms and starting term based on class settings
//fetch all user terms and fill term array
//if term array count doesn't match max terms, fill missing slots with system values
//if system values are missing, assume not offered
$terms = array();
$startingTerm = 0;
$maxTerms = 0;
$termNames = array();
if($class->id != "new"){
	if($class->universityid){
		$maxTerms = QuickQuery("select value from universitysetting where name = 'terms'");
		$startingTerm = QuickQuery("select value from universitysetting where name = 'startingterm'");
		$termNames = QuickQueryList("select sequence, name from term where universityid = " . $class->universityid, true);
	} else {
		$maxTerms = QuickQuery("select value from usersetting where name = 'terms' and userid = " . $_SESSION['userid']);
		$startingTerm = QuickQuery("select value from usersetting where name = 'startingterm' and userid = " . $_SESSION['userid']);
		$termNames = QuickQueryList("select sequence, name from userterm where userid = " . $_SESSION['userid'], true);
	}	
	$systemTerms = array();
	$userTerms = QuickQueryList("select termsequence, available from userclassoffering where classid = " . $class->id . " and userid = " . $_SESSION['userid'], true);
	if(count($userTerms) < $maxTerms){
		$systemTerms = QuickQueryList("select termsequence, available from classoffering where classid = " . $class->id, true);
	}
	for($x=0; $x<$maxTerms; $x++){
		if(isset($userTerms[$x]) && $userTerms[$x]){
			$terms[$x] = true;
		} else if(isset($systemTerms[$x]) && $systemTerms[$x]){
			$terms[$x] = true;
		}
	}
}

?>
<table class="classDetails" title="Class Details">
	<tr>
		<td>
			<table class="subMenu" width="100%">
				<tr>
					<td>Number:</td>
					<td>
						<?
						if($class->userid == $_SESSION['userid']){
							?><input type="text" id="classSymbol" value="<?=$class->symbol?>" size="30" maxlength="100"><?
						} else {
							echo htmlentities($class->symbol);
						}
						?>
					</td>
				</tr>
				<tr>
					<td>Name:</td>
					<td>
						<?
						if($class->userid == $_SESSION['userid']){
							?><input type="text" id="className" value="<?=$class->name?>" size="30" maxlength="100"><?
						} else {
							echo htmlentities($class->name);
						}
						?>
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<table>
							<tr>
								<td>
					<?
						if($class->userid == $_SESSION['userid']){
							button("Save", "saveClass()");
						}
					?>
								</td>
								<td>
					<?
						if(($class->id != "new") && ($class->userid == $_SESSION['userid'])){
							button("Delete", "deleteClass()");
						}
					?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center"><div id="errors" class="errors"></div></td>
				</tr>
			</table>
		</td>
	</tr>
<?
	if($class->id != "new"){
?>
	<tr>
		<td>
			<table class="classOffering">
				<tr>
				<?
					$count = 0;
					while($count < $maxTerms){
						$term = ($count+$startingTerm)%$maxTerms;
						$name = $termNames[$term];
						?><th align="left"><?=ucfirst($name)?></th><?
						$count++;
					}
				?>
				</tr>
				<tr>
				<?
					$count = 0;
					while($count < $maxTerms){
						$term = ($count+$startingTerm)%$maxTerms;
						
						$checked="";
						if(isset($terms[$term]))
							$checked = " CHECKED";
							
						?><td><input type="checkbox" id="classSequence<?=$term?>" <?=$checked?> onclick="updateClassOffering(<?=$class->id?>, <?=$term?>, this.checked);"></td><?
						
						$count++;
					}
				?>
				</tr>
			</table>
			<div id="classOffering"></div>
		</td>
	</tr>
	<tr>
		<td>
			<div id="classPrerequisites"></div>
		</td>
	</tr>
<?
	}
?>
</table>
<script>
<?
	if($class->id != "new"){
?>
		loadPrerequisites(<?=$class->id?>);
<?
	}
?>
</script>