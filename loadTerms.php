<?
$isajax=1;

include_once("inc/common.inc.php");


$terms = QuickQueryList("select sequence, name from userterm where userid = " . $_SESSION['userid'], true);
$startingTerm = QuickQuery("select value from usersetting where userid = " . $_SESSION['userid'] . " and name = 'startingterm'");




?>
<table class="subMenu">
	<tr><th colspan="4">Terms for Custom Classes:</th></tr>
	<tr><th>Order</th><th>Name</th><th>Starting Term</th><th></th></tr>
<?
	foreach($terms as $sequence => $term){
		$checked = "";
		if($sequence == $startingTerm){
			$checked = " CHECKED ";
		}
		?>
		<tr>
			<td><?=$sequence + 1?>.</td>
			<td><div id="displayTerm<?=$sequence?>"><?=$term?></div><div id="editTerm<?=$sequence?>" style="display:none"><input type="text" id="term<?=$sequence?>" value="<?=$term?>"></div></td>
			<td><input type="radio" id="startingTerm" name="startingTerm" onclick="updateStartingTerm(<?=$sequence?>)" <?=$checked?> ></td>
			<td>
				<table>
					<tr>
						<td><div id="editButton<?=$sequence?>"><? button("Edit", "editTerm(" . $sequence . ")"); ?></div><div id="saveButton<?=$sequence?>" style="display:none"><? button("Save", "updateTerm(" . $sequence . ")"); ?></div></td>
						<td><? button("Delete", "deleteTerm(" . $sequence . ")"); ?></td>
					</tr>
				</table>
			</td>
			
		</tr>
		<?
	}
?>
	<tr>
		<td></td>
		<td><input type="text" id="newTerm" value=""></td>
		<td><? button("Add Term", "addTerm()"); ?></td>
	</tr>
</table>