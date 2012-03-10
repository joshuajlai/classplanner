<?
$SETTINGS = parse_ini_file("settings.ini.php", true);
$dbcon = mysql_connect($SETTINGS['db']['host'], $SETTINGS['db']['username'], $SETTINGS['db']['password']);
mysql_select_db($SETTINGS['db']['database'], $dbcon);


include_once("db.inc.php");
include_once("utils.inc.php");
include_once("DBMappedObject.php");


session_name("classmanager");
session_start();

/*
if(!isset($_SERVER['HTTPS']) || !$_SERVER['HTTPS']){
	redirect("https://" . $SETTINGS['general']['servername'] . $_SERVER['REQUEST_URI']);
}

*/
if(!isset($exterior) || !$exterior){
	if(!isset($_SESSION['userid'])){
		if(isset($isajax)){
			exit();
		} else {
			redirect("index.php?logout=1");
		}
	}
}
?>
