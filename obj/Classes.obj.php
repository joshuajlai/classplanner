<?

class Classes extends DBMappedObject{

	var $symbol;
	var $name;
	var $userid;
	var $universityid;
	//object variable array filled by outside
	var $terms;
	
	function Classes($id = null){
		$this->_tablename = 'class'; //name of our table
		$this->_fieldlist = array("symbol", "name", "userid","universityid"); //list of our fields (excluding id) 	
		$this->_nullablefields = array("universityid" => true);
		//call super's constructor
		DBMappedObject::DBMappedObject($id);
		
	}

}
?>