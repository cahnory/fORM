<?php

	class Form_NodeValue extends Form_Element
	{
		protected	$_offsets	=	array();
		
		public	function	__construct() {}
		
		public	function	clear()
		{
			foreach($this->_values as $value) {
				$value->clear();
			}
		}
		
		public	function	fill($values)
		{
			if(!is_array($values)) {
				$values	=	array($values);
			}
			foreach($values as $key => $value) {
				$this->offsetSet($key, $value);
			}
		}
		
		public	function	value()
		{
			$output	=	array();
			foreach($this->_values as $key => $value) {
				$output[$this->_offsets[$key]]	=	$value->value();
			}
			return	$output ? $output : NULL;
		}
		
		/* !Interface: Iterator */
		public	function	key()
		{
			return $this->_offsets[$this->_offset];
		}
	
		public	function	current()
		{
			return	$this->offsetGet($this->_offset);
		}
		
		/* !Interface: ArrayAccess */
		public	function	offsetSet($offset, $value)
		{
			if($offset !== NULL) {
				if(($key = array_search($offset, $this->_offsets)) !== false) {
					$this->_values[$key]->fill($value);
				}
			}
		}
		
		public	function	offsetExists($offset)
		{
			return	array_search($offset, $this->_offsets) !== false;
		}
		
		public	function	offsetUnset($offset)
		{
			if($key = array_search($offset, $this->_offsets)) {
				$this->_values[$key]->clear();
			}
		}
		
		public	function	offsetGet($offset)
		{
			return	($key = array_search($offset, $this->_offsets)) !== false
				?	$this->_values[$key]
				:	NULL;
		}
	}

?>