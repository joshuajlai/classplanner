<?
/*
By Joshua J. Lai

Class Manager is a utility program to help college students plan their required curriculum.  A simple
MySQL database is required to contain class offering information and prerequisites.

*/
$isajax=1;
include_once("inc/common.inc.php");
include_once("obj/Curriculum.obj.php");
include_once("obj/ClassTree.obj.php");
include_once("obj/Classes.obj.php");

/*
if(isset($_POST['startingYear'])){
	$_SESSION['startingYear'] = get_POST('startingYear')+0;
	if($_SESSION['startingYear'] == 0){
		echo "Invalid Date";
		exit();
	}
}
*/

if(isset($_POST['maxClasses'])){
	$_SESSION['maxClasses'] = get_POST('maxClasses') + 0;
	if($_SESSION['maxClasses'] < 1){
		echo "Invalid Max Classes";
		exit();
	}
}

if(isset($_SESSION['maxClasses'])){
	$maxClasses = $_SESSION['maxClasses'];
} else {
	$maxClasses = 2;
}


// get class tree for default curriculum
// later, page will accept curriculum id as input
$curriculumid = QuickQuery("select value from usersetting where name = 'defaultcurriculum' and userid = " . $_SESSION['userid']);

if(!$curriculumid){
	echo "<div>You don't have a default curriculum yet.</div>";
	exit();
}

$curriculum = new Curriculum($curriculumid);
$curriculumTree = ClassTree::fetchCurriculum($curriculum->id);
list($possible, $class) = checkPossible($curriculumTree);
if(!$possible){
	echo "This curriculum has a class that has no offerings: " . $class->symbol . "-" . $class->name;
	exit();
}

// fetch schedule data for university associated with this curriculum
if($curriculum->universityid){
	$termNames = QuickQueryList("select sequence, name from term where universityid = " . $curriculum->universityid . " order by sequence ASC", true);
	$maxTerms = QuickQuery("select value from universitysetting where name = 'terms' and universityid = " . $curriculum->universityid);
	$startingTerm = QuickQuery("select value from universitysetting where name = 'startingterm' and universityid = " . $curriculum->universityid);
} else {
	$termNames = QuickQueryList("select sequence, name from userterm where userid = " . $_SESSION['userid'] . " order by sequence ASC", true);
	$maxTerms = QuickQuery("select value from usersetting where name = 'terms' and userid = " . $_SESSION['userid']);
	$startingTerm = QuickQuery("select value from usersetting where name = 'startingterm'  and userid = " . $_SESSION['userid']);
}

$startingYear = date("Y", strtotime("now"));
$totalClassCount = QuickQuery("select count(*) from curriculumassociation where curriculumid = " . $curriculum->id);
$curriculumPlan = array();



$count = 0;
$currentTerm = $startingTerm+0;
$currentYear = $startingYear+0;



while($count < $totalClassCount){
	if(!isset($curriculumPlan[$currentYear])){
		$curriculumPlan[$currentYear] = array();
	}
	
	if(!isset($curriculumPlan[$currentYear][$currentTerm])){
		$curriculumPlan[$currentYear][$currentTerm] = array();
	}
	$curriculumPlan[$currentYear][$currentTerm] = generatePlan($curriculumTree, $currentTerm, $maxClasses);
	$count = $count + count($curriculumPlan[$currentYear][$currentTerm]);
	if(($currentTerm+1) >= $maxTerms){
		$currentYear++;
	}
	$currentTerm = ($currentTerm + 1) % $maxTerms;
}



// each term will contain an array of class objects in no particular order
// iterate through class tree to pull out classes and append to arrays based on offering
// use greedy approach, first class/first available term, use user term before system terms

?>
<table class="classPlan"  cellspacing="0" cellpadding="3">
	<tr>
<?
	for($count=0; $count < $maxTerms; $count++){
		?><th class="tableHeader"><?=ucfirst($termNames[($count+$startingTerm)%$maxTerms])?></td><?
	}
?>
	</tr>
<?
	$year = $startingYear;
	$temp=0;
	while(isset($curriculumPlan[$year])){
		$temp++;
		?><tr><?
		for($x = 0; $x < $maxTerms; $x++){
			$quarter = ($x + $startingTerm) % $maxTerms;
			if($quarter == 0){
				$year++;
			}
			if(isset($curriculumPlan[$year])){
				$terms = $curriculumPlan[$year];
			} else {
				$terms = array();
			}
			if(isset($terms[$quarter]))
				$classes = $terms[$quarter];
			else
				$classes = array();
			?>
			<td class="classPlanTD">
				<table width="100%">
					<?
					if($classes){
						foreach($classes as $class){
							?><tr><td><?=htmlentities($class->symbol . ": " . $class->name)?></td></tr><?
						}
					} else {
						?><tr><td>&nbsp;</td></tr><?
					}
					?>
				</table>
			</td>
			<?
		}
		?></tr><?
		
		if($temp > 5){
			break;
		}
	}
?>
</table>
<?



/*
	displayPlan
	takes a root node from a curriculum tree and returns a set number of classes
	that are available for display
	
	uses a fair share algorithm to try and fetch a node from each leaf node directly off
	the root node
	
	leaf nodes off the root node usually stand as the basis for each department's prerequisite tree

*/

function generatePlan($rootNode, $term, $maxClasses = 2){

	$count = 0;
	$nodes = array();
	$classes = array();


	while($count < $maxClasses){
		$classesFound = false;
		$minDepth = null;
		$currNode = null;
		foreach($rootNode->leaves as $classNode){
			list($node, $depth) = getNextClass($classNode, $term, 1);
			if($node != false){
				if($minDepth == null){
					$minDepth = $depth;
					$currNode = $node;
				} else if ($minDepth > $depth){
					$minDepth = $depth;
					$currNode = $node;
				}
			}
		}
		if($currNode != null){
			$isLeaf = false;
			foreach($nodes as $node){
				if(in_array($currNode, $node->leaves)){
					$isLeaf = true;
					break;
				}
			}
			if(!$isLeaf){
				$nodes[] = $currNode;
				$currNode->displayed=true;
				$classesFound=true;
				$count++;
			}
		}
		if(!$classesFound)
			break;
	}
	foreach($nodes as $node){
		$classes[] = $node->class;
	}

	return $classes;

}

function getNextClass($node, $term, $depth){

	if(!$node->displayed){
		$newClass = $node->class;
		$terms = $newClass->terms;
		if(isset($terms[$term])){
			return array($node, $depth);
		}
	} else {
		foreach($node->leaves as $classNode){
			list($newNode, $newDepth) = getNextClass($classNode, $term, $depth+1);
			if($newNode != false){
				return array($newNode, $newDepth);
			}
		}
	
	}
	return array(false, $depth);
}

function checkPossible($rootNode){
	$possible = true;
	foreach($rootNode->leaves as $leaf){
		$class = $leaf->class;
		if(count($class->terms) == 0){
			return array(false, $class);
		}
	}
	foreach($rootNode->leaves as $leaf){
		list($possible, $class) = checkPossible($leaf);
		if(!$possible)
			return array(false, $class);
	}
	return array($possible, null);

}

?>