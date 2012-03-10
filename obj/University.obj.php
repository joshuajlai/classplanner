<?

class University extends DBMappedObject{

	var $name;

	function University($id=null){

		$this->_tablename = 'university'; //name of our table
		$this->_fieldlist = array("name"); //list of our fields (excluding id) 	
		$this->_nullablefields = array();
		//call super's constructor
		DBMappedObject::DBMappedObject($id);
	}

}

?>