<?php

	/**
	 * LICENSE: Copyright (c) 2010 François 'cahnory' Germain
	 * Permission is hereby granted, free of charge, to any person obtaining a copy
	 * of this software and associated documentation files (the "Software"), to deal
	 * in the Software without restriction, including without limitation the rights
	 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	 * copies of the Software, and to permit persons to whom the Software is
	 * furnished to do so, subject to the following conditions:
	 * 
	 * The above copyright notice and this permission notice shall be included in
	 * all copies or substantial portions of the Software.
	 * 
	 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	 * THE SOFTWARE.
	 * that is available through the world-wide-web at the following URI:
	 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
	 * the PHP License and are unable to obtain it through the web, please
	 * send a note to license@php.net so we can mail you a copy immediately.
	 *
	 * @package    FORM
	 * @author     François 'cahnory' Germain <cahnory@gmail.com>
	 * @copyright  2010 François Germain
	 * @license    http://www.opensource.org/licenses/mit-license.php
	 */

	class FORM_Element implements ArrayAccess, Iterator
	{
		private		$_data		=	array();
		private		$_limit		=	1;
		protected	$_name;
		protected	$_offset	=	0;
		protected	$_parent;
		protected	$_values	=	array();
		
		public	function	__construct()
		{
			$this->setDefinition();
		}
		
		public	function	__get($name)
		{
			return	$this->data($name);
		}
		
		protected	function	__set($name, $value)
		{
			return	$this->hasData($name, $value);
		}
		
		private	function	_hasElement($name, Form_Element $element)
		{
			if(($key = array_search($name, $this->_offsets)) !== false) {
				array_splice($this->_values, $key, 1);
				array_splice($this->_offsets, $key, 1);
			}
			$element->_name		=	$name;
			$element->_parent	=	$this;
			$this->_values[]	=	$element;
			$this->_offsets[]	=	$name;
			return	$element;
		}
		
		protected	function	setDefinition() {}
		
		public	function	clear()
		{
			$this->_values	=	array();
		}
		
		public	function	data($name = NULL)
		{
			if($name === NULL)
				return	$this->_data;
			
			return	array_key_exists($name, $this->_data) ? $this->_data[$name] : NULL;
		}
		
		public	function	fill($value)
		{
			if(!is_array($value)) {
				$this->_values	=	array($value);
			} else {
				$this->_values	=	array_slice(array_values($value), 0, $this->_limit);
			}
		}
		
		protected	function	hasData($name, $value = NULL)
		{
			if($value === NULL && is_array($name)) {
				$this->_data	=	$name;
			} elseif($value !== NULL) {
				$this->_data[$name]	=	$value;
			}
		}
		
		protected	function	hasField($name, $options = array())
		{
			if(is_a($options, 'Form_Field')) {
				$field	=	$options;
			} else {
				$field	=	new Form_Field;
			}
			return	$this->_hasElement($name, $field);
		}
		
		protected	function	hasLimit($limit)
		{
			$this->_limit	=	(int)$limit;
		}
		
		protected	function	hasNode($name, $options = array())
		{
			if(is_a($options, 'Form_Node')) {
				$node	=	$options;
			} else {
				$node	=	new Form_Node;
			}
			return	$this->_hasElement($name, $node);
		}
		
		public	function	length()
		{
			return	sizeof($this->_values);
		}
		
		public	function	limit()
		{
			return	$this->_limit;
		}
		
		public	function	name($full = true)
		{
			if(!$full) {
				$name	=	$this->_name;
			} elseif($this->_parent === NULL || ($pname = $this->_parent->name($full)) === NULL) {
				if($this->_limit === 1) {
					$name	=	NULL;
				} else {
					$name	=	$this->_name;
				}
			} else {
				$name	=	$pname.'['.$this->_name.']';
			}
			return	$name;
		}
		
		public	function	value()
		{
			return	$this->_limit === 1
				?	(array_key_exists(0, $this->_values) ? $this->_values[0] : NULL)
				:	$this->_values;
		}
		
		public	function	validate()
		{
			foreach($this->_values as $value) {
				if(!$value->validate())
					return	false;
			}
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