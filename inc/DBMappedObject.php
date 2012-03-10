<?
/*
phpMyORM - Simple Object Relational Mapping for PHP and MySQL

Copyright (C) 2004-2005  Reliance Communications, Inc.

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

Please send any questions or comments to
Ben Hencke <bhencke@schoolmessenger.com>


//modified to handle nullable fields
//modified to use custom query function to log queries and errors
//by joshua j lai
*/


class DBMappedObject {
	var $id; //always has an id
		
	var $_tablename = ''; //name of our table
	var $_fieldlist = array(); //list of our fields (excluding id) 	
	var $_childobjects = array();
	var $_childclasses = array();
	var $_relations = array();
	//handle nullable fields
	var $_nullablefields = array();
	
	function DBMappedObject ($id = NULL) {
		if ($this->_tablename == '')
			$this->_tablename = get_class($this);
		
		$this->id = $id;
		$this->refresh();
		
		//now create children
		foreach ($this->_childobjects as $index => $name) {
			$classname = $this->_childclasses[$index];
			$nameid = $name . "id";
			if (in_array($nameid, $this->_fieldlist))
				$this->$name = new $classname($this->$nameid);
			else
				$this->$name = new $classname();
		}
		
		//create links
		foreach (array_keys($this->_relations) as $index) {
			$this->_relations[$index]->refresh();
		}
	}
	
	//returns values (excluding id or not) optonally escaped and quoted 
	function getValueArray ($prepfordb = true, $specificfields = NULL, $includeid = false) {
		$fieldlist = ($specificfields == NULL) ? $this->_fieldlist : $specificfields;
		
		if ($includeid) {
			$fieldlist = array_merge(array("id"),$fieldlist);
		}
		
		$values = array();
		foreach ($fieldlist as $name) {
			//check add this name if we have no specificfields
			//or it is in the specific fields
			if ($prepfordb){
				//Handle nullable fields
				if(isset($this->_nullablefields[$name]) && ($this->$name === null)){
					$values[] = "NULL";
				} else {
					$values[] = "'" . mysql_escape_string($this->$name) . "'";
				}
			} else
				$values[] = $this->$name;
		}
		
		return $values;
	}
	
	function getValueList ($specificfields = NULL) {
		return implode(",", $this->getValueArray(true, $specificfields));
	}
	
	function getFieldList ($includeid = false, $specificfields = NULL) {
		$fieldlist = ($specificfields == NULL) ? $this->_fieldlist : $specificfields;
		
		if ($includeid) {
			$list = array("id");
			$list = array_merge($list,$fieldlist);
		} else {
			$list = $fieldlist;
		}

		return implode(",", $list);
	}
	
	function create ($specificfields = NULL, $createchildren = false) {
		if ($specificfields == NULL) {
			$specificfields = $this->_fieldlist;
		}
		
		//create children first
		if ($createchildren) {
			foreach ($this->_childobjects as $name) {
				//create this child
				if ($this->$name->create(NULL,true)) {
					//should we set our id of it?
					if (in_array($name."id", $this->_fieldlist)) {
						$nameid = $name . "id";
						$this->$nameid = $this->$name->id;
						if (!in_array($name."id", $specificfields)) {
							$specificfields[] = $name."id";
						}
					}
				}
			}
		}
		
		
		$query = "insert into " . $this->_tablename . " ("
				. $this->getFieldList(false, $specificfields) . ") "
				."values (" . $this->getValueList($specificfields) . ")";
		if ($result = Query($query)) {
			$this->id = mysql_insert_id();
		} else {
			echo "fail: $query\n";
			return false;
		}
		
		if ($createchildren) {
			//update links
			foreach (array_keys($this->_relations) as $index) {
				$this->_relations[$index]->update();
			}
		}

		return $this->id;
	}
	
	function refresh ($specificfields = NULL, $refreshchildren = false) {
		$isrefreshed = false;
				
		if (!isset($this->id))
			return false;
						
		$query = "select " . $this->getFieldList(false, $specificfields) 
				." from " . $this->_tablename
				." where id='" . $this->id . "'";
		if ($result = Query($query)) {
			if ($row = mysql_fetch_row($result)) {
				foreach ($this->_fieldlist as $index => $name) {
					$this->$name = stripslashes($row[$index]);
				}
				$isrefreshed = true;
			}
			mysql_free_result($result);
		}
		
		//refresh children
		if ($refreshchildren) {
			foreach ($this->_childobjects as $name) {
				//should we update its id?
				if (in_array($name."id", $this->_fieldlist)) {
					$nameid = $name . "id";
					$this->$name->id = $this->$nameid;
				}
				//refresh this child
				$this->$name->refresh(NULL, true);
			}
			
			//refresh links
			foreach (array_keys($this->_relations) as $index) {
				$this->_relations[$index]->refresh();
			}
		}
		
		return $isrefreshed;		
	}
	
	function update ($specificfields = NULL, $updatechildren = false) {
		$isupdated = false;
		
		if ($specificfields == NULL) {
			$specificfields = $this->_fieldlist;
		}
		
		//update children
		if ($updatechildren) {
			foreach ($this->_childobjects as $name) {
				//update this child
				$childupdated = $this->$name->update(NULL, true);
				//if this child was updated
				//check to see if we are keeping track of its id
				if ($childupdated && in_array($name."id", $this->_fieldlist)) {
					//then update our id of it
					$nameid = $name . "id";
					$this->$nameid = $this->$name->id;
					//should we add this to the list of fields to update?
					if (!in_array($name."id", $specificfields)) {
						$specificfields[] = $name."id";
					}
				}
			}
		}
		
		//does this object already exist?
		if ($this->id) {
			$query = "update " . $this->_tablename . " set ";

			//make an array of name=value pairs
			$list = array();
			foreach ($specificfields as $name) {
				//handle null fields
				if(isset($this->_nullablefields[$name]) && ($this->$name === null)){
					$list[] = "$name= NULL";
				} else {
					$list[] = "$name='" . mysql_escape_string($this->$name) . "'";
				}
			}

			//put them into an update list
			$query .= implode(",", $list);
			$query .= " where id=$this->id";

			if ($result = Query($query)) {
				if (mysql_affected_rows()) 
					$isupdated = true;
			}
		} else {
			//then we should create the object in the db and update the id field
			if ($this->create($specificfields, false))
				$isupdated = true;
		}
		
		if ($updatechildren) {
			//update links
			foreach (array_keys($this->_relations) as $index) {
				$this->_relations[$index]->update();
			}
		}
		
		return $isupdated;
	}
	
	function destroy ($destroychildren = false) {
		
		if ($destroychildren) {
			foreach ($this->_childobjects as $name) {
				//update this child
				$childupdated = $this->$name->destroy(NULL, true);
			}
			
			//destroy links
			foreach (array_keys($this->_relations) as $index) {
				$this->_relations[$index]->destroy();
			}
		}
		
		if ($this->id) {
			$query = "delete from " . $this->_tablename
					." where id=" . $this->id;
			QuickUpdate($query);
			$this->id = 0;
		}
	}
	
	//must override this function
	function getLinkedChildren ($link) {
		return array();
	}
	
}


?>