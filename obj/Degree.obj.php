<?

class Degree extends DBMappedObject{
	
	var $universityid;
	var $name;

	function Degree($id=null){

		$this->_tablename = 'degree'; //name of our table
		$this->_fieldlist = array("universityid", "name"); //list of our fields (excluding id) 	
		$this->_nullablefields = array();
		//call super's constructor
		DBMappedObject::DBMappedObject($id);
	}

}

?>