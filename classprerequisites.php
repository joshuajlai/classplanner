<?
$isajax=1;
include_once("inc/common.inc.php");
include_once("obj/Classes.obj.php");
include_once("obj/ClassTree.obj.php");


$classid = 0;
if(isset($_POST['classidnum'])){
	$classid = get_POST('classidnum') + 0;
}

//always expect a class id in the post
if(!$classid)
	exit();

$class = new Classes($classid);
if(!$class)
	exit();

//only accept a remove or an add request, not both
if(isset($_POST['remove'])){
	$removeid = get_POST('remove') + 0;
	if($class->userid == $_SESSION['userid']){
		QuickUpdate("delete from prerequisite where classid = " . $class->id . " and prerequisite = " . DBSafe($removeid));
	} else {
		error_log("attempt to alter class not owned: " . $class->id . " user: " . $user->id);
		exit();
	}
} else if(isset($_POST['add'])){
	$addid = get_POST('add') + 0;
	if($addid == 0){
		exit();
	}
	if($class->userid == $_SESSION['userid']){
	
		//use classtree to build a temporary prerequisite tree to check for cyclical prereqs.
		//turn of fetching of term data since it is not necessary
		$prerequisitelist = QuickQueryList("select classid from prerequisite where prerequisite = " . $class->id);
		$rootNode = new ClassTree();
		$rootNode->root=true;
		foreach($prerequisitelist as $prerequisite){
			$rootNode = ClassTree::addTreeNode($rootNode, $prerequisite, false);
		}
		list($level, $node) = ClassTree::search($rootNode, $addid);
		if( $node == false){
			QuickUpdate("insert into prerequisite values (" . $class->id . ", ". DBSafe($addid) .")");
		} else {
			?><script>alert("The prerequisite you want to add is not valid");</script><?
		}
	} else {
		error_log("attempt to alter class not owned: " . $class->id . " user: " . $user->id);
		exit();
	}
}

$prerequisitelist = QuickQueryList("select prerequisite from prerequisite where classid = '" . $class->id . "'");
$prerequisites = fetchObjects("Classes", "from class where id in ('" . implode("','", $prerequisitelist) . "')");
if($_SESSION['classlist']['school'] != "custom"){
	$schoolquery = " and universityid = " . $_SESSION['classlist']['school'];
} else {
	$schoolquery = " and universityid is null and userid = " . $_SESSION['userid'];
}

//make a new list of class not available as prereqs
//don't use algorithm to check for circular prereqs until they try to add a class

//disallowing the option will take too much computation time to iterate through every class in the system
//TODO: make sure loading up a lot of classes from a university will not break the page
$unavailablePrerequisites = $prerequisitelist;
$unavailablePrerequisites[] = $class->id;
$classes = fetchObjects("Classes", "from class where 1 " . $schoolquery . " and id not in ('". implode("','", $unavailablePrerequisites) . "')");


?>
<table class="prerequisites">
	<tr>
		<th>Prerequisites:</th>
	</tr>
<?
	if($prerequisites){
		foreach($prerequisites as $prerequisite){
			?>
			<tr>
				<td><?=$prerequisite->symbol . ": " . $prerequisite->name?></td>
				<td>
					<?
					if($class->userid)
						button("Remove", "removePrerequisite($class->id,$prerequisite->id)");
					?>
				</td>
			</tr>
			<?
		}
	} else {
		?>
			<tr><td><?=htmlentities("N/A")?></td></tr>
		<?
	}
	if($class->userid){
	?>
		<tr>
			<td>
				<select id='newPrerequisite'>
					<option value="">-- Add a Prerequisite -- </option>
					<?
					foreach($classes as $newPrereq){
						?><option value="<?=$newPrereq->id?>"><?=htmlentities($newPrereq->symbol . ": " . $newPrereq->name)?></option><?
					}
					?>
				</select>
			</td>
			<td>
				<?
				button("Add Prereq", "addPrerequisite($class->id,$('#newPrerequisite').val())");
				?>
			</td>
		</tr>
	<?
	}
?>
</table>