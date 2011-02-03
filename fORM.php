<?php

	/**
	 * LICENSE: Copyright (c) 2011 François 'cahnory' Germain
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
	 * @copyright  2011 François Germain
	 * @license    http://www.opensource.org/licenses/mit-license.php
	 */

	class fORM implements Iterator, ArrayAccess
	{
		/**
	     *	Additional datas (ex:label,placeHolder)
	     *
	     *	@var array
	     *	@access	private
	     */
		private		$_data		=	array();
		
		/**
	     *	Max number of values.
	     *
	     *  Use a limit < 0 to allow
	     *	unlimited number of values
	     *
	     *	@var int
	     *	@access	private
	     */
		private	$_limit	=	1;
		
		/**
	     *	Children
	     *
	     *	@var array
	     *	@access	private
	     */
		private	$_children	=	array();
		
		/**
	     *	Element name
	     *
	     *	@var string
	     *	@access	private
	     */
		private	$_name;
		
		/**
	     *	Children offsets
	     *
	     *	@var array
	     *	@access	private
	     */
		private	$_offsets	=	array();
		
		/**
	     *	The parent element
	     *
	     *	@var Form_Element
	     *	@access	protected
	     */
		protected	$_parent;
		
		/**
	     *	Value
	     *
	     *	@var array
	     *	@access	private
	     */
		private	$_values	=	array();
		
		public	function	__construct()
		{
			$this->setDefinition();
			$this->fill(array(NULL));
		}
		
		/**
		 *	Add a new child
		 *
		 *	@param string $offset the child offset
		 *	@param fORM   $child  the child object
		 *
		 *	@return fORM the child object
		 *
		 *	@access private
		 */
		private	function	_hasChild($offset, $child)
		{
			$this->_children[]	=	$child;
			$this->_offsets[]	=	$offset;
			$this->_prepareChild($offset, $child);
			return	$child;
		}
		
		/**
		 *	Prepare child
		 *
		 *	@param string $offset the child offset
		 *	@param fORM   $child  the child object
		 *
		 *	@return fORM the child object
		 *
		 *	@access private
		 */
		private	function	_prepareChild($offset, $child)
		{
			$child->_name	=	$offset;
			$child->_parent	=	$this;
			return	$child;
		}
		
		/**
		 *	Clear values
		 *
		 *	@access public
		 */
		public	function	clear()
		{
			$this->fill(array(NULL));
		}
		
		/**
		 *	Return data values
		 *
		 *	@param string $name  the data name
		 *
		 *	@return mixed the data or null
		 *
		 *	@access public
		 */
		public	function	data($name = NULL)
		{
			if($name === NULL)
				return	$this->_data;
			
			return	array_key_exists($name, $this->_data) ? $this->_data[$name] : NULL;
		}
		
		/**
		 *	Fill the values
		 *
		 *	@param mixed $value the values
		 *
		 *	@access public
		 */
		public	function	fill($value)
		{
			$this->_values	=	array();
			if($this->_limit !== 1) {
			//	Multiple elements
				if(!is_array($value) || empty($value))
					$value	=	array(NULL);
				$i	=	sizeof($this->_values);
				//	Clone the multiple element into single elements
				foreach($value as $v) {
					if($i === $this->_limit)	break;
					$clone	=	$this->_prepareChild($i, clone($this));
					$clone->hasLimit(1);
					$clone->fill($v);
					$this->_values[]	=	$clone;
					$i++;
				}
			} elseif(sizeof($this->_children)) {
			//	Single node
				if(!is_array($value))
					$value	=	array();
				//	Clone the multiple element into single elements
				foreach($this->_children as $key => $child) {
					$offset	=	$this->_offsets[$key];
					$clone	=	$this->_prepareChild($offset, clone($child));
					$clone->_parent	=	$this;
					$clone->fill(array_key_exists($offset, $value) ? $value[$offset] : array());
					$this->_values[]	=	$clone;
				}
			} else {
			//	Single field
				if(is_array($value))
					$value	=	NULL;
				$this->_values	=	array($value);
			}
		}
		
		/**
		 *	Set data value
		 *
		 *	@param string $name  the data name
		 *	@param string $value the data value
		 *
		 *	@access protected
		 */
		protected	function	hasData($name, $value = NULL)
		{
			if($value === NULL && is_array($name)) {
				$this->_data	=	$name;
			} elseif($value !== NULL) {
				$this->_data[$name]	=	$value;
			}
		}
		
		/**
		 *	Set values limit
		 *
		 *	If limit < 1, unlimited
		 *
		 *	@param string $limit the limit
		 *
		 *	@access protected
		 */
		protected	function	hasLimit($limit)
		{
			$this->_limit	=	(int)$limit;
		}
		
		/**
		 *	Add a single child (limit = 1)
		 *
		 *	@return mixed the child offset
		 *	@return mixed the child object
		 *
		 *	@access public
		 */
		protected	function	hasOne($offset, fORM $child = NULL)
		{
			if($child === NULL)
				$child	=	new fORM;
			return	$this->_hasChild($offset, $child);
		}
		
		/**
		 *	Add a multiple child (limit = -1)
		 *
		 *	@return mixed the child offset
		 *	@return mixed the child object
		 *
		 *	@access public
		 */
		protected	function	hasMany($offset, fORM $child = NULL)
		{
			if($child === NULL)
				$child	=	new fORM;
			$child->hasLimit(-1);
			return	$this->_hasChild($offset, $child);
		}
		
		/**
		 *	Check if the fORM has child
		 *
		 *	@return boolean if the fORM has child
		 *
		 *	@access public
		 */
		public	function	isParent()
		{
			return	sizeof($this->_children) > 0;
		}
		
		/**
		 *	Return the number of values
		 *
		 *	@return int nb values
		 *
		 *	@access public
		 */
		public	function	length()
		{
			return	sizeof($this->_values);
		}
		
		/**
		 *	Return the values limit
		 *
		 *	@return int Nb values limit
		 *
		 *	@access public
		 */
		public	function	limit()
		{
			return	$this->_limit;
		}
		
		/**
		 *	Return the field name
		 *
		 *	@param boolean $full If name start from root
		 *	@param string  $prefix
		 *	@param string  $glue
		 *	@param string  $suffix
		 *
		 *	@return string the element name
		 *
		 *	@access public
		 */
		public	function	name($full = true, $prefix = '', $glue = '[', $suffix = ']')
		{
			if(!$full) {
				$name	=	$this->_name;
			} elseif($this->_parent === NULL || ($pname = $this->_parent->name($full, $prefix, $glue, $suffix)) === NULL) {
				$name	=	$this->_name;
			} else {
				$name	=	$prefix.$pname.$glue.$this->_name.$suffix;
			}
			return	$name;
		}
		
		/**
		 *	Return the fORM parent
		 *
		 *	@return fORM the fORM parent or NULL
		 *
		 *	@access public
		 */
		public	function	parent()
		{
			return	$this->_parent;
		}
		
		/**
		 *	Object definition (to overide)
		 *
		 *	@access protected
		 */
		protected	function	setDefinition() {}
		
		/**
		 *	Validate values and child values
		 *
		 *	@return boolean true/false if valid or not
		 *
		 *	@access public
		 */
		public	function	validate()
		{
			$output	=	true;
			if($this->_limit !== 1 || sizeof($this->_children)) {
				foreach($this->_values as $value) {
					if(!$value->validate()) {
						$output	=	false;
						break;
					}
				}
			} else {
				if(sizeof($this->_values))
					$output	=	$this->validateValue($this->_values[0]);
			}
			return	$output;
		}
		
		/**
		 *	Validate a value (to overide)
		 *
		 *	@return boolean true/false if valid or not
		 *
		 *	@access protected
		 */
		protected	function	validateValue($value)
		{
			return	true;
		}
		
		/**
		 *	Return the values
		 *
		 *	@return mixed the values
		 *
		 *	@access public
		 */
		public	function	value()
		{
			$output	=	NULL;
			if($this->_limit !== 1 || sizeof($this->_children)) {
				$output	=	array();
				foreach($this->_values as $key => $value) {
					$output[$value->_name]	=	$value->value();
				}
			} else {
				if(sizeof($this->_values))
					$output	=	$this->_values[0];
			}
			return	$output;
		}
		
	/* !Interfaces */
		
		/**
		 *	Interface: Iterator
		 *
		 *	Traversing element's values
		 */
		public	function	rewind()
		{
			$this->_offset	=	0;
		}
		
		public	function	current()
		{
			return	$this->_values[$this->_offset];
		}
		
		public	function	key()
		{
			return	$this->_limit !== 1 ? $this->_offset : $this->_offsets[$this->_offset];
		}
		
		public	function	next()
		{
			++$this->_offset;
		}
		
		public	function	valid()
		{
			return	array_key_exists($this->_offset, $this->_values);
		}		
		
		/**
		 *	Interface: ArrayAccess
		 *
		 *	Traversing element's values
		 */
		public function offsetSet($offset, $value)
		{
			if($this->_limit !== 1) {
			//	Multiple elements
				$i = sizeof($this->_values);
				if($offset === NULL) {
					$offset	=	$i;
				}
				if($offset < $i) {
					$this->_values[$offset]->fill($value);
				} else {
					for($i; $offset >= $i; $i++) {
						if($i === $this->_limit)	break;	
						$clone	=	clone($this);
						$clone->hasLimit(1);
						$clone->_name	=	$i;							
						$clone->fill($i === $offset ? $value : NULL);
						$this->_values[]	=	$clone;
					}
				}
			} elseif(sizeof($this->_children)) {
			//	Single node
				if($offset !== NULL && ($key = array_search($offset, $this->_offsets)) !== false) {
					$clone	=	clone($this->_children[$key]);
					$clone->fill($value);
					$this->_values[$key]	=	$clone;
				}
			}
		}
		
		public function offsetExists($offset)
		{
			if($this->_limit === 1)	{
				return	($offset = array_search($offset, $this->_offsets)) !== false && array_key_exists($offset, $this->_values);
			} else {
				return	array_key_exists($offset, $this->_values);
			}
		}
		
		public function offsetUnset($offset)
		{
			if($this->_limit === 1)	{
				if(($offset = array_search($offset, $this->_offsets)) !== false && array_key_exists($offset, $this->_values)) {
					$this->_values[$offset]->fill(NULL);
				}
			} else {
				array_splice($this->_values, $offset, 1);
				for($offset; array_key_exists($offset, $this->_values); $offset++)
					$this->_values[$offset]->_name	=	$offset;
			}
		}
		
		public function offsetGet($offset)
		{
			if($this->_limit === 1) {
				return	($offset = array_search($offset, $this->_offsets)) !== false && array_key_exists($offset, $this->_values)
					?	$this->_values[$offset] : null;
			} else {
				return	array_key_exists($offset, $this->_values) ? $this->_values[$offset] : null;
			}
		}
    }

?>