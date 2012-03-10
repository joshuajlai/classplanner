<?
/*
	Simple form handling library.
*/



//form header
//sets internal hidden input to make sure form is valid
function startForm($form){
	echo "<form name='form[$form]' method='POST' action='" . substr($_SERVER['SCRIPT_NAME'],1, strlen($_SERVER['SCRIPT_NAME']))  . "' >";
	$_SESSION['form'][$form]['timecheck'] = strtotime("now");
	echo "<input type='hidden' name='form[" . $form . "][time]' value='" . $_SESSION['form'][$form]['timecheck'] . "'>";
}


//creates html inputs
//pre-populates with session data
function formItem($form, $name, $type, $option="", $option2="", $extrahtml=""){

	$value = $_SESSION['form'][$form][$name]['value'];
	switch($type){
		case "text":
			echo "<input type='text' name='form[" . $form . "][" . $name . "]'";
			if($option){
				echo " size='$option' ";
			}
			if($option){
				echo " max-size='$option2' ";
			}
			echo " $extrahtml ";
			if($value){
				echo " value = '$value'";
			}
			echo ">";
			break;
		case "textarea":
			echo "<textarea name='form[" . $form . "][" . $name . "]' rowspan = " . $option . " colspan= " . $option2 . ">" . $value . "</textarea>";
			break;
		case "selectstart":
			echo "<select name='form[" . $form . "][" . $name . "]' $extrahtml >";
			break;
		case "selectend";
			echo "</select>";
			break;
		case "selectoption":
			echo "<option value='$option' ";
			if($option == $value)
				echo " selected ";
			echo ">$option2</option>";
			break;
		case "checkbox":
			echo "<input  type='checkbox' name='form[" . $form . "][" . $name . "]' $extrahtml";
			if($value)
				echo " checked ";
			echo ">";
	}
	if(isset($_SESSION['form'][$form][$name]['error'])){
		echo "<div style='font-color:red'>" . $_SESSION['form'][$form][$name]['error'] . "</div>";
	}
}

//sets session data for form inputs
//if an input's session data isnt set, php error will occur
function setFormItem($form, $name, $value, $type, $min=null, $max=null, $required=false){
	if(!isset($_SESSION['form'][$form][$name])){
		$_SESSION['form'][$form][$name] = array();
	}
	$_SESSION['form'][$form][$name]["value"] = $value;
	$_SESSION['form'][$form][$name]['type'] = $type;
	$_SESSION['form'][$form][$name]["min"] = $min;
	$_SESSION['form'][$form][$name]["max"] = $max;
	$_SESSION['form'][$form][$name]["required"] = (bool)$required;

}

//Populates session data with posted data
function collectForm($form){
	foreach($_POST['form'][$form] as $index => $postitem){
		if(!isset($_SESSION['form'][$form][$index])){
			continue;
		}
		$_SESSION['form'][$form][$index]['value'] = (get_magic_quotes_gpc() ? stripslashes($postitem) : $postitem);
	}
}

function checkData($type, $value){
	switch($type){
		case 'number':
			break;
		case 'text':
			break;
	}
	return true;
}


//iterate through all posted form data to check validation
function formInvalid($form){
	$invalid = false;
	foreach($_SESSION['form'][$form] as $name => $formdata){
		if(!checkData($formdata['type'], $formdata['value'])){
			$_SESSION['form'][$form][$name]['error'] = "Invalid Data";
			$invalid = true;
		} else {
			unset($_SESSION['form'][$form][$name]['error']);
		}
	}
	if($invalid)
		return true;
	else
		return false;
}

//gets form data from session
function getFormData($form, $name){
	return $_SESSION['form'][$form][$name]['value'];
}

//resets form
function resetForm($form){
	$_SESSION['form'] = array();
	$_SESSION['form'][$form] = array();
}

//checks form submit based on post variable and time variable
function formSubmit($form){
	if(isset($_POST['form'][$form])){
		if($_SESSION['form'][$form]['timecheck'] != $_POST['form'][$form]['time']){
			return false;
		}
		return true;
	}
	return false;
}

//form footer
function endForm(){
	echo "</form>";
}

function submitForm($form){
	echo "<input name='submit" . $form . "' type='submit'>";
}

?>