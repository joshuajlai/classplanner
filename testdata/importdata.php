<?
/*
	basic data import script
	Script assumes that each class is possibly already in the system.  This will do an insert or update on duplicate key
	as well as update class prerequisites and curriculum associations.  It will remove classes as well.
	
	Script should use settings.ini file for database connection information
	
*/


$SETTINGS = parse_ini_file("inc/settings.ini.php", true);
$dbcon = mysql_connect($SETTINGS['db']['host'], $SETTINGS['db']['username'], $SETTINGS['db']['password']);
mysql_select_db($SETTINGS['db'][database], $dbcon);

include_once("inc/db.inc.php");

$datacolumns=7;
$infile = "testdata.csv";
if(!$inFilePointer = fopen($infile, "r")){
	echo "Bad In File";
	exit(-1);
}
$logfile = "import.log";
global $logfileFP;
if(!$logfileFP = fopen($logfile, "a")){
	echo "Bad log file";
	exit(-1);
}

function writeLog($string){
	global $logfileFP;
	fwrite($logfileFP, date("M d, Y g:i:s") . " - " . $string . "\n");
}


//first line is always university name
$line  = fgetcsv($inFilePointer);
$universityname = $line[0];

//find out university id if it exists, else create university record
$universityid = QuickQuery("select id from university where name = '" . DBSafe($universityname) . "'");
if($universityid == false){
	QuickUpdate("insert into university (name) values ('" . DBSafe($universityname) . "')");
	$universityid = mysql_insert_id();
	QuickUpdate("insert into universitysetting (universityid, name, value) values (" . $universityid . ", 'startingterm', '3'), (" . $universityid . ", 'terms', '4')");
	QuickUpdate("insert into term values (" . $universityid . ", 'winter', 0), (" . $universityid . ", 'spring', 1), (" . $universityid . ", 'summer', 2), (" . $universityid . ", 'fall', 3)");
}

$existingClasses = QuickQueryList("select id from class where universityid = " . $universityid);
$existingClasses = array_flip($existingClasses);

$classlist = array();

//blanket curriculum update so gather all curriculum names and onces preprocessing is completed, gather ids and delete associations
$curriculumlist = array();
while($line = fgetcsv($inFilePointer)){
	if(count($line) != $datacolumns){
	// all imports should have exactly 6 data columns
		writeLog("Line has too few columns: " . count($line));
		continue;
	}
	$classSymbol = $line[0];
	$className = $line[1];
	$classDegree = $line[2];
	$classCurriculum = $line[3];
	$curriculumlist[] = $classCurriculum;
	$prereqs = array();
	for($i=4; $i<7; $i++){
		if($line[$i])
			$prereqs[] = $line[$i];
	}
	$classlist[$classSymbol] = array($className, $classDegree, $classCurriculum, $prereqs);
}

$errors = array();
$readClasses=0;
//make sure all prerequisite classes exist either already in the database or this new list of classes
foreach($classlist as $classSymbol => $classdata){
	foreach($classdata[3] as $prereq){
		if(isset($classlist[$prereq])){
			continue;
		} else if(isset($existingClasses[$prereq])){
			continue;
		} else {
			//throw some error and remove class from classlist
			$errors[] = $classSymbol . " has a prereq: " . $prereq . " that does not exist";
		}
	}
	$readClasses++;
}

if($errors){
	foreach($errors as $error){
		writeLog($error);
	}
	echo "Bad classes found, check log file";
	exit(-1);
}

//pre-processing is finished at this point

/*
	Begin a transaction to start inserting/updating classes and updating curriculums
	Transaction is deeply necessary since curriculum associations are wiped clean, this script
	is not meant as an incremental updater, it is safer to update an entire university's information
	than to update a class or curriculum here or there.
*/

$curriculumids = QuickQueryList("select id from curriculum where name in ('" . implode("','", $curriculumlist) . "')");
QuickUpdate("Begin");
if($curriculumids){
	QuickUpdate("delete from curriculumassociation where curriculumid in (" . implode(",", $curriculumids) . ")");
}
$insertedClasses=0;
foreach($classlist as $classSymbol => $classdata){
	QuickUpdate("insert into class (symbol, name, universityid) values ('" . $classSymbol . "', '" . $classdata[0] . "', " . $universityid . ")");
	$classid = mysql_insert_id();
	//update curriculum and degree information only if the curriculum is set
	if($classdata[2] != ""){
		$degreeid = QuickQuery("select id from degree where name = '" . $classdata[1] . "'");
		if($degreeid == false){
			QuickUpdate("insert into degree (universityid, name) values (" . $universityid . ", '" . $classdata[1] . "')");
			$degreeid = mysql_insert_id();
		}
		$curriculumid = QuickQuery("select id from curriculum where name = '" . $classdata[2] . "'");
		if($curriculumid == false){
			QuickUpdate("insert into curriculum (name, universityid, degreeid) values ('" . $classdata[2] . "', " . $universityid . ", " . $degreeid . ")");
			$curriculumid = mysql_insert_id();
		}
		QuickUpdate("insert into curriculumassociation values (" . $curriculumid . ", " . $classid . ")");
	}
	$insertedClasses++;
}
//process prerequisites after all classes are inserted
$insertedPrereqs=0;
foreach($classlist as $classSymbol => $classdata){
	$classid = QuickQuery("select id from class where symbol = '" . $classSymbol . "'");
	if($classid == false){
		writeLog("Class: " . $classSymbol . " not found, previous insert must have failed, rolling back and exiting");
		QuickUpdate("Rollback");
		exit(-1);
	}
	QuickUpdate("delete from prerequisite where classid = " . $classid);
	$prereqQuery = array();
	foreach($classdata[3] as $prereq){
		$prereqid = QuickQuery("select id from class where symbol = '" . $prereq . "'");
		if($prereqid == false){
			writeLog("Prereg " . $prereq . " not found, previous insert must have failed or prereq check failed, rolling back and exiting");
			QuickUpdate("rollback");
			exit(-1);
		} else {
			$prereqQuery[] = "(" . $classid . ", " . $prereqid . ")";
		}
	}
	QuickUpdate("insert into prerequisite (classid, prerequisite) values " . implode(",", $prereqQuery));
	$insertedPrereqs++;
}
if(!(($inserterdPrereqs == $insertedClasses) && ($insertedClasses == $readClasses))){
	QuickUpdate("Commit");
} else {
	writeLog("Processed counts do not match, rolling back");
	writeLog("readClasses=" . $readClasses . ", insertedClasses=" . $insertedClasses . ", insertedPrereqs=" . $insertedPrereqs);
	QuickUpdate("Rollback");
	exit(-1);
}

echo $readClasses . " classes processed\n";
echo "Done\n";
?>