<?
// Object class used for building class curriculum trees
class ClassTree{
	
	// root is a boolean to signify root node
	var $root;
	var $class;
	var $leaves;
	var $displayed;

	function ClassTree(){
		$this->root = false;
		$this->class = null;
		$this->leaves = array();
		$this->displayed = false;
	}
	
	
	static function addTreeNode($rootNode, $classid, $fetchTerms = true){
		if(!$rootNode->root){
			error_log("addTreeNode called with invalid root node");
			exit();
		}
		$class = new Classes($classid);
		if(!$class->id){
			error_log("addTreeNode called with invalid class id: " . $classid);
			exit();
		}
		
		$prerequisiteList = QuickQueryList("select prerequisite from prerequisite where classid = " . $class->id);
		$newNode = new ClassTree();
		$newClass = new Classes($classid);
		
		
		//fetch term information
		if($fetchTerms){
			$terms = array();
			if($class->universityid){
				$maxTerms = QuickQuery("select value from universitysetting where name = 'terms'");
				$startingTerm = QuickQuery("select value from universitysetting where name = 'startingterm'");
				$termNames = QuickQueryList("select sequence, name from term where universityid = " . $class->universityid, true);
			} else {
				$maxTerms = QuickQuery("select value from usersetting where name = 'terms' and userid = " . $_SESSION['userid']);
				$startingTerm = QuickQuery("select value from usersetting where name = 'startingterm' and userid = " . $_SESSION['userid']);
				$termNames = QuickQueryList("select sequence, name from userterm where userid = " . $_SESSION['userid'], true);
			}	
			$systemTerms = array();
			$userTerms = QuickQueryList("select termsequence, available from userclassoffering where classid = " . $class->id . " and userid = " . $_SESSION['userid'], true);
			if(count($userTerms) < $maxTerms){
				$systemTerms = QuickQueryList("select termsequence, available from classoffering where classid = " . $class->id, true);
			}
			for($x=0; $x<$maxTerms; $x++){
				if(isset($userTerms[$x]) && $userTerms[$x]){
					$terms[$x] = true;
				} else if(isset($systemTerms[$x]) && $systemTerms[$x]){
					$terms[$x] = true;
				}
			}

			$newClass->terms = $terms;
		}
		$newNode->class = $newClass;
		
	
		if($prerequisiteList){
			$currentLevel = 0;
			$currentNode = null;
			foreach($prerequisiteList as $prerequisite){
				ClassTree::addTreeNode($rootNode, $prerequisite);
				list($level, $node) = ClassTree::search($rootNode, $prerequisite);
				if($node == false){
					error_log("tree built incorrectly - classid: " . $prerequisite);
					exit();
				}
				if($level > $currentLevel){
					$currentLevel = $level;
					$currentNode = $node;
				}
			}
			if($currentNode == null){
				error_log("error in setting parent node");
				exit();
			}
			$leaves = $currentNode->leaves;
			if(!isset($leaves[$newNode->class->id])){
				$leaves[$newNode->class->id] = $newNode;
			}
			$currentNode->leaves = $leaves;
			
		} else {
			list($level, $oldNode) = ClassTree::search($rootNode, $classid);
			if($oldNode == false){
				$leaves = $rootNode->leaves;
				$leaves[$newNode->class->id] = $newNode;
				$rootNode->leaves = $leaves;
			}
		}
		return $rootNode;
	}
	
	static function search($treeNode, $classid){
		$level = 1;
		if($treeNode->class && ($treeNode->class->id == $classid)){
			return array($level, $treeNode);
		}
		$node = false;
		
		foreach($treeNode->leaves as $class){
			list($returnedLevel, $node) = ClassTree::search($class, $classid);
			if($node != false){
				$level += $returnedLevel;
				break;
			}
		}
		return array($level, $node);
	}
	
	static function fetchCurriculum($curriculumid){
	
		$rootNode = new ClassTree();
		$rootNode->root=true;
		
		$curriculum = new Curriculum($curriculumid);
		if(!$curriculum->id){
			error_log("fetchCurriculum called with bad curriculum id");
			return $rootNode;
		}
		$_SESSION['classtree']['curriculumid'] = $curriculumid;
		$classList=QuickQueryList("select classid from curriculumassociation where curriculumid = " . $curriculum->id);
		foreach($classList as $classid){
			ClassTree::addTreeNode($rootNode, $classid);
		}
	
		return $rootNode;
	}

	static function displayCurriculum($rootNode){
		?>
		<table class="subMenu" width="100%">
			<?
				ClassTree::drawClassTR($rootNode);
			?>
		</table>
		<?
	}
	
	/* 
		getShallowestClass:
		Desc:
			Function to fetch next shallowest node in curriculum tree.
			Function is responsible for keeping track of node depth during search.
			Calls itself recursively
		Params:
			rootNode: root node of curriculum tree
			currDepth: current deptch
		Returns:
			array($foundNode, $foundDepth)
	*/
		
	static function getShallowestClass($rootNode, $currDepth){
	
		//if rootNode is null,
		if($rootNode == null){
			return array(null, $currDepth);
		}
		
		$foundDepth = false;
		$foundNode = null;
		
		//if the node has not been displayed yet and its not the root of a tree
		if(!$rootNode->displayed && !$rootNode->root){
			$foundNode = $rootNode;
			$foundDepth = $currDepth;
		} else {
			foreach($rootNode->leaves as $classNode){
				list($newNode, $newDepth) = ClassTree::getShallowestClass($classNode,$currDepth+1);
				if($newNode != false){
					if($foundDepth == false){
						$foundDepth = $newDepth;
						$foundNode = $newNode;
					} else if($foundDepth >= $newDepth){
						$foundDepth = $newDepth;
						$foundNode = $newNode;
					}
				}
			}
		}
		return array($foundNode, $foundDepth);
	}
	
	static function drawClassTR($rootNode){
		
		$currDepth = 1;

		echo "<tr><td align='center'><table><tr><td>\n";
		do {
			$nodeFound = false;
			list($currNode, $foundDepth) = ClassTree::getShallowestClass($rootNode, 0);
			//echo $foundDepth . " ";
			if($currNode != null){
				if($foundDepth > $currDepth){
					echo "</td></tr></table></td></tr>\n";
					echo "<tr><td align='center'><table><tr><td>";
					$currDepth = $foundDepth;
				}
				ClassTree::drawClassTD($currNode->class);
				
				$currNode->displayed = true;
				$nodeFound=true;
			}
		} while($nodeFound);
		
		echo "</td></tr></table></td></tr>";
	}
	
	static function drawClassTD($class){
		$prerequisitelist = QuickQueryList("select prerequisite from prerequisite where classid = '" . $class->id . "'");
		$classlist = QuickQueryList("select classid from curriculumassociation where curriculumid = " . $_SESSION['classtree']['curriculumid']);
		//$prerequisites = fetchObjects("Classes", "from class where id in ('" . implode("','", $prerequisitelist) . "')");
		?>
			<td alight="center">
			<div float="right" class="normalClassTree" id="class<?=$class->id?>" onmouseover='highlightClassTree(<?=$class->id?>, ["<?=implode('","',$prerequisitelist)?>"])' onmouseout='dehighlightClassTree(<?=$class->id?>, ["<?=implode('","',$prerequisitelist)?>"])'>
				<table>
					<tr>
						<th align="left">Number:</th><td><?=$class->symbol?></td>
					</tr>
					<tr>
						<th align="left">Name:</th><td><?=$class->name?></td>
					</tr>
					<tr>
						<td colspan="2">
							<?
								if($_SESSION['curriculummanager']['curriculumid'] != "new"){
									if(!QuickQuery("select count(*) from prerequisite where prerequisite = " . $class->id . " and classid in (" . implode(", ", $classlist) . ")"))
										button("Remove", "removeClass($class->id)");
								}
							?>
							<div id="class<?=$class->id?>RemoveDiv"></div>
						</td>
					</tr>
				</table>
			</div>
			</td>
		<?
	}

	static function resetDisplay($rootNode){
		foreach($rootNode->leaves as $leaf){
			ClassTree::resetDisplay($leaf);
		}
		$rootNode->displayed=false;
	}

}

?>