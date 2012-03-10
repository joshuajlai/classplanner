<?
$isajax=1;

include_once("inc/common.inc.php");

if(isset($_POST['del'])){
	$termsequence = get_POST('del')+0;

	$terms = QuickQueryList("select sequence, name from userterm where userid = " . $_SESSION['userid'] . " order by sequence ASC", true);
	$startingTerm = QuickQuery("select value from usersetting where name = 'startingterm' and userid = " . $_SESSION['userid']);
	$maxTerms = count($terms);
	$newTerms = array();
	$startingTermAdjusted=false;
	
	$count=0;
	$oldToNew = array();
	foreach($terms as $index => $name){
		if($index == $termsequence){
			if($index == $startingTerm){
				$startingTerm = ($startingTerm + 1) % $maxTerms;
				$startingTermAdjusted=true;
			}
			$maxTerms--;
			continue;
		}
		$newTerms[$count] = $name;
		$oldToNew[$count] = $index;
		$count++;
	}
	
	$updateTermQuery = array();
	foreach($oldToNew as $newIndex => $oldIndex){
		if(!$startingTermAdjusted){
			if($startingTerm == $oldIndex){
				$startingTerm = $newIndex;
				$startingTermAdjusted = true;
			}
		}
		$updateTermQuery[] = "update userclassoffering set termsequence = " . $newIndex . " where termsequence = " . $oldIndex . " and userid = " . $_SESSION['userid'];
	}
	
	$newTermQuery = array();
	foreach($newTerms as $index => $name){
		$newTermQuery[] = "(" . $_SESSION['userid'] . ", '" . $name . "', " . $index . ")";
	}
	
	QuickUpdate("begin");
	QuickUpdate("delete from userterm where userid = " . $_SESSION['userid']);
	QuickUpdate("insert into userterm (userid, name, sequence) values " . implode(", ", $newTermQuery));
	QuickUpdate("update usersetting set value = " . $startingTerm . " where name = 'startingterm' and userid = " . $_SESSION['userid']);
	QuickUpdate("update usersetting set value = " . $maxTerms . " where name = 'terms' and userid = " . $_SESSION['userid']);
	QuickUpdate("delete from userclassoffering where termsequence = " . $termsequence . " and userid = " . $_SESSION['userid']);
	foreach($updateTermQuery as $query){
		QuickUpdate($query);
	}
	
	
	QuickUpdate("commit");

?>
	<script>
		loadTerms();
	</script>
<?
} else if(isset($_POST['startingterm'])){
	$startingTerm = get_POST('startingterm') + 0;
	QuickUpdate("update usersetting set value = " . $startingTerm . " where name = 'startingterm' and userid = " . $_SESSION['userid']);
} else if(isset($_POST['addterm'])){
	error_log($_POST['addterm']);
	$maxTerm = QuickQuery("select value from usersetting where name = 'terms' and userid = " . $_SESSION['userid']);
	QuickUpdate("insert into userterm (userid, name, sequence) values ('" . $_SESSION['userid'] . "', '" . DBSafe(get_POST('addterm')) . "', " . $maxTerm . ")");
	QuickUpdate("update usersetting set value = '" . ($maxTerm+1) . "' where name = 'terms' and userid = " . $_SESSION['userid']);
	?>
		<script>
			loadTerms();
		</script>
	<?
} else if(isset($_POST['updateTerm'])){
	$sequence = get_POST('sequence')+0;
	$termName=get_POST('updateTerm');
	QuickUpdate("update userterm set name = '" . DBSafe($termName) . "' where sequence = " . $sequence . " and userid = " . $_SESSION['userid']);
	?>
		<script>
			$('#displayTerm<?=$sequence?>').html("<?=$termName?>");
			$('#editTerm<?=$sequence?>').val("<?=$termName?>");
			$('#displayTerm<?=$sequence?>').show();
			$('#editTerm<?=$sequence?>').hide();
			$('#editButton<?=$sequence?>').show();
			$('#saveButton<?=$sequence?>').hide();
		</script>
	<?
}

?>