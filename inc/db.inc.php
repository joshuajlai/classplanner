<?
/*
A simple library for database connection functions


By Joshua Lai
*/

function db_query_master($query, $newdbcon = false, $return = false){
	global $dbcon, $SETTINGS;
	if($SETTINGS['db']['log'] == true){
		$fp = fopen($SETTINGS['db']['logfile'], "a");
		fwrite($fp, "-----" . date("M d Y, g:i:s a", strtotime("now")) . "-----\n");
		fwrite($fp, "mysql query: " . $query . "\n");
		fwrite($fp, "----- END -----\n");
		fclose($fp);
	}
	
	if($newdbcon)
		$dbconnect = $newdbcon;
	else
		$dbconnect = $dbcon;
		
	$result = mysql_query($query, $dbconnect);
	if(mysql_error()){
		if($SETTINGS['db']['log'] == true){
			$fp = fopen($SETTINGS['db']['logfile'], "a");
			fwrite($fp, "-----" . date("M d Y, g:i:s a", strtotime("now")) . "-----\n");
			fwrite($fp, "mysql error: " . mysql_error() . "\n");
			fwrite($fp, "----- END -----\n");
			fclose($fp);
		}
	}
	if($return){
		return $result;
	}
}

function Query($query, $newdbcon = false){
	$result = db_query_master($query, $newdbcon, true);
	return $result;
}

function QuickQuery($query, $newdbcon = false){
	$result = Query($query, $newdbcon);
	if($result){
		$row = DBGetRow($result);
		return $row[0];
	} else {
		return false;
	}
}

function QuickUpdate($query, $newdbcon = false){
	db_query_master($query, $newdbcon);
}

function QuickQueryRow($query, $assoc=false, $newdbcon = false){
	$result = Query($query, $newdbcon);
	return DBGetRow($result, $assoc);
}

function QuickQueryList($query, $pair = false, $newdbcon = false){
	$result = Query($query, $newdbcon);
	$list = array();
	while($row = DBGetRow($result)){
		if($pair){
			$list[$row[0]] = $row[1];
		} else {
			$list[] = $row[0];
		}
	}
	return $list;
}

function DBSafe($string){
	return mysql_escape_string($string);
}

function DBGetRow($result, $assoc=false){
	if($result){
		if($assoc)
			return mysql_fetch_assoc($result);	
		else
			return mysql_fetch_row($result);		
	} else
		return false;
}

//returns an array of objects with their index as their ID
function fetchObjects($class, $query){
	$classObj = new $class();
	$result = Query("select " . $classObj->getFieldList(true) . " " . $query);
	$objects = array();
	while($row = DBGetRow($result, true)){
		$newObj = new $class();
		foreach($newObj->_fieldlist as $element){
			$newObj->$element = $row[$element];
		}
		$newObj->id = $row['id'];
		$objects[$newObj->id] = $newObj;
	}
	return $objects;
}

function fetchObject($class, $query){
	$newObj = new $class();
	$result = Query("select " . $newObj->getFieldList(true) . " " . $query);
	$row = DBGetRow($result, true);
	foreach($newObj->_fieldlist as $element){
		$newObj->$element = $row[$element];
	}
	$newObj->id = $row['id'];

	return $newObj;

}


//Function to convert an array of objects into an array of arrays
//for use in generateTable
//objects must have ids
function objectsToArray($objs){
	$data = array();
	foreach($objs as $obj){
		$row=array();
		$row["id"] = $obj->id;
		foreach($obj->_fieldlist as $element){
			$row[$element] = $obj->$element;
		}
		$data[] = $row;
	}
	return $data;
}

?>
