<?php

	class Form_Field extends Form_Element
	{
		protected	$_limit		=	1;
		protected	$_offset	=	0;
		protected	$_values	=	array();
		
		public	function	__construct()
		{
			$this->setDefinition();
		}
		
		protected	function	setDefinition() {}
		
		public	function	clear()
		{
			$this->_values	=	array();
		}
		
		public	function	fill($value)
		{
			if(!is_array($value)) {
				$this->_values	=	array($value);
			} else {
				$this->_values	=	array_slice(array_values($value), 0, $this->_limit);
			}
		}
		
		protected	function	hasField() {}
		
		protected	function	hasNode() {}
		
		public	function	length()
		{
			return	sizeof($this->_values);
		}
		
		public	function	value()
		{
			return	$this->_limit === 1
				?	(array_key_exists(0, $this->_values) ? $this->_values[0] : NULL)
				:	$this->_values;
		}
		
		public	function	validate()
		{
			return	true;
		}
		
		/* !Interface: Iterator */
		public	function	rewind()
		{
			$this->_offset = 0;
		}
	
		public	function	current()
		{
			return $this->_values[$this->_offset];
		}
	
		public	function	key()
		{
			return $this->_offset;
		}
	
		public	function	next()
		{
			++$this->_offset;
		}
	
		public	function	valid()
		{
			return array_key_exists($this->_offset, $this->_values);
		}
		
		/* !Interface: ArrayAccess */
		public	function	offsetSet($offset, $value)
		{
			if($offset === NULL)
				$offset	=	sizeof($this->_values);
			else
				$offset	=	(int)$offset;
			
			if($offset < $this->_limit) {
				$this->_values	=	array_pad($this->_values, $offset, NULL);
				$this->_values[$offset]	=	$value;
			}
		}
		
		public	function	offsetExists($offset)
		{
			return	array_key_exists($offset, $this->_values);
		}
		
		public	function	offsetUnset($offset)
		{
			array_splice($this->_values, (int)$offset, 1);
		}
		
		public	function	offsetGet($offset)
		{
			return	array_key_exists($offset, $this->_values)
				?	$this->_values[$offset]
				:	NULL;
		}
	}

?>