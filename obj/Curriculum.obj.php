<?


class Curriculum extends DBMappedObject{

	var $name;
	var $universityid;
	var $degreeid;
	var $userid;
	

	function Curriculum($id = null){
	
		$this->_tablename = 'curriculum'; //name of our table
		$this->_fieldlist = array("name", "universityid", "degreeid", "userid"); //list of our fields (excluding id) 	
		$this->_nullablefields = array("universityid" => true,
										"degreeid" => true, 
										"userid" => true);
		//call super's constructor
		DBMappedObject::DBMappedObject($id);
		
	}

}

?>