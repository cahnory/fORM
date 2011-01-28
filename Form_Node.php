<?php

	class Form_Node extends Form_Element
	{
		protected	$_model;
		
		public	function	__construct()
		{
		}
		
		private	function	_getNode($offset)
		{
			if($offset < $this->_limit || $this->_limit < 1) {
				if(!array_key_exists($offset, $this->_values)) {					
					$this->_model	=	new Form_NodeValue;
					$this->setDefinition();
					$this->_values[$offset]	=	$this->_model;
				}													
				return	$this->_values[$offset];
			}
		}
		
		public	function	fill($values)
		{
			foreach($values as $offset => $value) {
				if($el = $this->offsetGet($offset)) {
					$el->fill($value);
				}
			}
		}
		
		protected	function	hasField($name, $options = array())
		{
			return	$this->_model->hasField($name, $options);
		}
		
		protected	function	hasNode($name, $options = array())
		{
			return	$this->_model->hasNode($name, $options);
		}
		
		public	function	value()
		{
			if($this->_limit === 1) {
				return	$this->_getNode(0)->value();
			} else {				
				$output	=	array();
				foreach($this->_values as $key => $value) {
					$output[$key]	=	$value->value();
				}
				return	$output ? $output : NULL;
			}
		}
		
		/* !Interface: Iterator */
		public	function	key()
		{
			if($this->_limit === 1) {
				return	$this->_getNode(0)->key();
			}
			return $this->_offset;
		}
	
		public	function	current()
		{
			return	$this->offsetGet($this->_offset);
		}
		
		/* !Interface: ArrayAccess */
		public	function	offsetSet($offset, $value)
		{
			if($this->_limit === 1) {
				$this->_getNode(0)->offsetSet($offset, $value);
			} else {
				if($node = $this->_getNode($offset)) {
					$node->fill($value);
				}
			}
		}
		
		public	function	offsetExists($offset)
		{
			if($this->_limit === 1) {
				$this->_getNode(0)->offsetExists($offset);
			} else {
				return	(int)$offset < $this->_limit || $this->_limit < 1;
			}
		}
		
		public	function	offsetUnset($offset)
		{
			if($this->_limit === 1) {
				$this->_getNode(0)->offsetUnset($offset);
			} else {
				if($node = $this->_getNode($offset))
					$node->clear();
			}
		}
		
		public	function	offsetGet($offset)
		{
			if($this->_limit === 1) {
				return	$this->_getNode(0)->offsetGet($offset);				
			} else {
				return	$this->_getNode((int)$offset);
			}
		}
	}

?>