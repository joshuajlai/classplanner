<?
/*
By Joshua J. Lai

Class Manager is a utility program to help college students plan their required curriculum.  A simple
MySQL database is required to contain class offering information and prerequisites.

*/

include_once("inc/common.inc.php");
include_once("obj/SchoolClass.obj.php");
include_once("obj/ClassSchedule.obj.php");

if(isset($_GET['startingYear'])){
	$_SESSION['startingYear'] = $_GET['startingYear']+0;
	if($_SESSION['startingYear'] == 0){
		echo "Invalid Date";
		exit();
	}
}

if(isset($_GET['maxClasses'])){
	$_SESSION['maxClasses'] = $_GET['maxClasses'] + 0;
	if($_SESSION['maxClasses'] == 0 && $_GET['maxClasses'] != 0){
		echo "Invalid Max Classes";
		exit();
	}
}

$quarter = array("fall", "winter", "spring", "summer");

if(isset($_GET['movedClass'])){
	if(isset($_GET['className']) && isset($_GET['quarter']) && isset($_GET['year'])){
		if(!in_array($_GET['quarter'], $quarter)){
			echo "Invalid Quarter";
			exit();
		}
		if(!QuickQuery("select count(*) from prerequisite where class='" . DBSafe($_GET['className']) . "'")){
			echo "Invalid Class";
			exit();
		}
		if(!isset($_SESSION['finalClassSchedule'])){
			$_SESSION['finalClassSchedule'] = array();
		}
		$class = new SchoolClass();
		if(!isset($_SESSION['finalClassSchedule'][$_GET['year']+0])){
			$_SESSION['finalClassSchedule'][$_GET['year']+0] = array();
		}
		if(!isset($_SESSION['finalClassSchedule'][$_GET['year']+0][$_GET['quarter']])){
			$_SESSION['finalClassSchedule'][$_GET['year']+0][$_GET['quarter']] = array();
		}
		$_SESSION['finalClassSchedule'][$_GET['year']+0][$_GET['quarter']][]=$_GET['className'];
	}
}





//Pull class curriculum data and create ordering first

$result = Query("select class, prerequisite from prerequisite");
$classReqs = array();

while($row = DBGetRow($result)){
	if(!isset($classReqs[$row[0]])){
		$classReqs[$row[0]] = new SchoolClass();
		$classReqs[$row[0]]->className = $row[0];
	}
	if($row[1])
		$classReqs[$row[0]]->prerequisites[] = $row[1];
}

$classOrder = array();
//Iterate over classes and remove classes that have prerequisites already covered
//as classes are taken, elimiate prerequisites to signify when a class can be taken
//This alg is a brute force method but it is assumed that the curriculum is not overly large
//such that this method would become too slow

while(count($classOrder) != count($classReqs)){
	foreach($classReqs as $class){
		$requirementsSatisfied = true;
		if(in_array($class->className, $classOrder))
			continue;
		foreach($class->prerequisites as $index => $prereq){
			if(!in_array($prereq, array_keys($classReqs))){
				exit("Invalid Curriculum");
			}
			if(!in_array($prereq, $classOrder)){
				$requirementsSatisfied = false;
			}
		}
		if($requirementsSatisfied){
			$classOrder[] = $class->className;
		}
	}
}



//Assume its a new school year and plan for the next 4 years
$classOfferingResult = Query("select class, quarter from classoffering");
while($row = DBGetRow($classOfferingResult)){
	if(!isset($classReqs[$row[0]]))
		exit("Missing class from curriculum");
	$classReqs[$row[0]]->quartersOfferred[] = $row[1];
}


$counter = 0;

$classesTaken=array();
$finalClassSchedule = array();

if(!isset($_SESSION['maxClasses'])){
	$maxClasses = 2;
} else {
	$maxClasses = $_SESSION['maxClasses'];
}
if(!isset($_SESSION['startingYear'])){
	$currentYear = date("Y", strtotime("now"));
} else {
	$currentYear = $_SESSION['startingYear'];
}
while(count($classesTaken) != count($classOrder)){
	$year = $currentYear + ceil($counter/4);
	$currentQuarter = $quarter[$counter%4];
	
	if(!isset($finalClassSchedule[$year]))
		$finalClassSchedule[$year] = array();
	if(!isset($finalClassSchedule[$year][$currentQuarter]))
		$finalClassSchedule[$year][$currentQuarter] = array();
		
	$classCount = 0;
	if(isset($_SESSION['finalClassSchedule'][$year][$currentQuarter])){
		foreach($_SESSION['finalClassSchedule'][$year][$currentQuarter] as $classname){
			$finalClassSchedule[$year][$currentQuarter][] = $classReqs[$classname];
			$classesTaken[] = $classname;
		}
	}
	
	foreach($classOrder as $index => $classname){
		if(in_array($classname, $classesTaken)){
			continue;
		}
		if($classCount >= $maxClasses){
			if(in_array($currentQuarter, $classReqs[$classname]->quartersOfferred)){
				if(count($classReqs[$classname]->prerequisites)==0){
					$tempclass = $classReqs[$classname];
					$tempclass->previousOffering = new ClassSchedule();
					$tempclass->previousOffering->quarter = $currentQuarter;
					$tempclass->previousOffering->year = $year;
					$classReqs[$classname] = $tempclass;
				}
			}
			break;
		}
		if(in_array($currentQuarter, $classReqs[$classname]->quartersOfferred)){
			if(count($classReqs[$classname]->prerequisites)==0){
				$finalClassSchedule[$year][$currentQuarter][] = $classReqs[$classname];
				$classesTaken[] = $classname;
				$classCount++;
			}
		}
	}
	//Update requirement lists seperate from adding classes because
	//concurrent enrollment is not allowed
	foreach($classOrder as $classname){
		foreach($classReqs[$classname]->prerequisites as $index => $req){
			if(in_array($req, $classesTaken)){
				unset($classReqs[$classname]->prerequisites[$index]);
			}
		}
	}
	$counter++;
}

?>
<table border="1">
	<tr>
		<?
			foreach($finalClassSchedule as $year => $yearschedule){
				foreach($yearschedule as $quarter => $classes){
					?><th><?=$quarter . ": " . $year?></td><?
				}
			}
		?>
	</tr>
	<tr>
		<?
		foreach($finalClassSchedule as $quarter => $yearschedule){
			foreach($yearschedule as $year => $classes){
			?>
				<td>
					<table>
					<?
					foreach($classes as $class){
					?>
						<tr>
							<td>
							<?
							if($class->moved){
							?>
								<table style="border: dotted red 1px;">
							<?
							} else {
							?>
								<table>
							<?
							}
							?>
									<tr><th colspan="2"><?=$class->className?></th></tr>
									<tr>
									<?
										if(isset($class->previousOffering)){
									?>
										<td><img src="left_arrow1.gif" onmouseover="this.src='left_arrow2.gif'" onmouseout="this.src='left_arrow1.gif'"></td>
									<?
										}
									/*
										<td><img src="right_arrow1.gif" onmouseover="this.src='right_arrow2.gif'" onmouseout="this.src='right_arrow1.gif'"></td>
									*/
									?>
									</tr>
								</table>
							</td>
						</tr>
					<?
					}
					?>
					</table>
				</td>
			<?
			}
		}
		?>
	</tr>
</table>