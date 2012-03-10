<?
include_once("inc/common.inc.php");

if(is_readable("updatenews.txt")){
	$updatenews = file_get_contents("updatenews.txt");
} else {
	$updatenews = "";
}


$TITLE="News";
$PAGETITLE="News";

include_once("nav.inc.php");

?>
<table>
	<tr>
		<td>
			<div><?=nl2br($updatenews)?></div>
		</td>
	</tr>
</table>
<?

include_once("navbottom.inc.php");

?>