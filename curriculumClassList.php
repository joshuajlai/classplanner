<?
$isajax=1;
include_once("inc/common.inc.php");
include_once("obj/Curriculum.obj.php");
include_once("obj/Classes.obj.php");
include_once("obj/ClassTree.obj.php");


//Implement tree method of constructing class curriculums
//this will be a method accessible from the start page as well.

/*
Tree construction
The tree is not a binary tree, each node has x leaves, multiple nodes may share multiple leaves.
The purpose of the tree structure is to construct curriculums in a hierchy structure allowing for
various classes to be able to be taken within each hierchy.
*/


/*
Basic tree algorithm:
Starting node is empty, required to link multiple starter leaves
Steps:
	Foreach prerequisite, search tree and set prereq class' child array to contain current class
	if prereq class does not exist, recurse function to run on prereq class
	if class has no prereq, set starter node's leaf array to class
	if current class already in tree, and leaf already contains current class, do nothing

Since objects are passed by reference, recursive function doesn't need to return anything

functions needed:
Starter function: 
	fetches curriculum and all classes associated with curriculum
	foreach class fetched, run recursive function on it

recursive function:
	input: starting node, class id
*/


$curriculumid = $_SESSION['curriculummanager']['curriculumid'];

if(isset($_POST['degreeid']) && (get_POST('degreeid') == "custom")){
	$curriculumid = "new";
} else if(isset($_POST['universityid'], $_POST['degreeid'])){
	$curriculumid = QuickQuery("select id from curriculum where universityid = " . (get_POST('universityid')+0) . " and degreeid = " . (get_POST('degreeid')+0) . " and userid is null");
}


if(!$curriculumid){
	error_log("curriculumClassList called without proper curriculum id");
	exit();
}

$curriculum = ClassTree::fetchCurriculum($curriculumid);


if($curriculumid != "new")
	ClassTree::displayCurriculum($curriculum);
?>
