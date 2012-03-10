<?
include_once("inc/common.inc.php");
include_once("obj/Classes.obj.php");


//when this page is reloaded, the session data should be cleared out
unset($_SESSION['classlist']);

$universities = QuickQueryList("select id, name from university", true);

$TITLE="Class Manager";
$PAGETITLE="Class Manager";
include_once("nav.inc.php");
?>
<script src="script/classesScripts.js" 
	language="javascript" 
	type="text/javascript">
</script>
<table class="tableMenu" width="100%">
	<tr>
		<td>
			<table>
				<tr>
					<td>
						<select onchange="loadClassList()" id="chooseSchool">
							<option value="custom">-- University Not Listed --</option>
<?
							foreach($universities as $universityid => $university){
								?><option value="<?=$universityid?>"><?=$university?></option><?
							}
?>
						</select>
					</td>
					<td><?=button("Add Class", "deHighlightRow(); addClass();");?></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table>
				<tr>
					<td valign="top"><div id="classList"></div><td>
					<td valign="top"><div id="classDetails" class="classDetails"></div></td>
				</tr>
			</table>
		</td>
	</tr>
</table>




<?
include_once("navbottom.inc.php");
?>
<script type="text/javascript">
	loadClassList();
</script>