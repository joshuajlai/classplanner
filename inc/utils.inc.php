<?

function redirect($url=""){
	if($url == ""){
		$url = $_SERVER['SCRIPT_NAME'];
	}
	header("location: $url");
}


//Generate simple table based on variables;
function generateTable($data, $titles, $formatters){
?>
	<table border="1">
		<tr>
<?
	foreach($titles as $title){
		?><th><?=$title?></th><?
	}
?>
		</tr>
<?
	$alt=0;
	foreach($data as $row){
		$alt++;
		if($alt)
			echo "<tr>";
		else
			echo "<tr class='listalt'>";
		
		foreach($titles as $index => $title){
			if(isset($formatters[$index])){
				$function = $formatters[$index];
				echo "<td>" . $function($row, $index) . "</td>";
			} else {
				if(isset($row[$index])){
					if($row[$index] == "")
						echo "<td>&nbsp;</td>";
					else
						echo "<td>" . htmlentities($row[$index]) . "</td>";
				}
			}
		}
		echo "</tr>";
	}
?>
	</table>
<?
}


//function to gather all erros and display them all at once
//in a javascript pop up
function error($errors){
	echo $errors;
}

function button($text, $onclick){
	?>
		<table class="clickButton" onmousedown="this.style.backgroundColor='#CCCCCC'" onmouseup="this.style.backgroundColor='white'">
			<tr>
				<td>
					<div onclick="<?=$onclick?>"><?=$text?></div>
				</td>
			</tr>
		</table>
	<?
}

function submit($text, $formname){
	?>
		<input type="submit" style="display:none">
	<?
	button($text, "document." . $formname . ".submit()");
	
}

function get_POST($variable){
	$value = false;
	if(isset($_POST[$variable])){
		$value = get_magic_quotes_gpc() ? stripslashes($_POST[$variable]) : $_POST[$variable];
	}
	return $value;
}


?>
