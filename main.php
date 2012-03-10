<?

include("inc/common.inc.php");
/*

author: Joshua J. Lai

Main Class Manager page to accept input from users.


*/


$TITLE="Start";
$PAGETITLE="My Plan";
include("nav.inc.php");
?>
<script src="script/plannerScripts.js" language="javascript" type="text/javascript"></script>

<p>
Welcome to the Curriculum Class Planner.
</p>
<form>
<table>
	<tr>
		<td>Input the maximum amount of classes you would like to take from this curriculum:</td>
		<td><input type = "text" size="1" maxlength="1" name="maxClasses" id="maxClasses" onkeyup="calculateScheduleHandler()"></td>
	</tr>
</table>
</form>
<table class="tableMenu" width="100%">
	<tr><td></td></tr>
	<tr>
		<td>
			<div id="scheduleTable">No Curriculum Plan Calculated Yet</div>
		</td>
	</tr>
	<tr><td></td></tr>
</table>
<script>

function calculateScheduleHandler(){
	if(this.timer)
		clearTimeout(this.timer);
	
	this.timer = setTimeout("calculateSchedule()", 500);
}

</script>

<?
include("navbottom.inc.php");
?>