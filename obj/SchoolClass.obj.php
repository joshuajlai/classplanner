<?

class SchoolClass{

	var $className;
	//quarters offerred will be an array of schedule objects
	var $quartersOfferred;
	//prerequisites will be an array of school class objects
	var $prerequisites;
	var $moved;
	var $previousOffering;
	var $nextOffering;

	function SchoolClass(){
		$this->moved=false;
		$this->prerequisites = array();
		$this->quartersOfferred = array();
	}


}




?>