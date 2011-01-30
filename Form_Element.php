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
		/**
	     *	Additional datas (ex:placeHolder)
	     *
	     *	@var array
	     *	@access	private
	     */
		private		$_data		=	array();
		
		/**
	     *	Max number of values.
	     *
	     *  Use a limit < 1 to allow
	     *	unlimited number of values
	     *
	     *	@var int
	     *	@access	private
	     */
		private		$_limit		=	1;
		
		/**
	     *	The element name
	     *
	     *	@var string
	     *	@access	protected
	     */
		protected	$_name;
		
		/**
	     *	The current offset position
	     *
	     *	@var int
	     *	@access	protected
	     */
		protected	$_offset	=	0;
		
		/**
	     *	Values offsets
	     *
	     *	@var array
	     *	@access	protected
	     */
		protected	$_offsets	=	array();
		
		/**
	     *	The parent element
	     *
	     *	@var Form_Element
	     *	@access	protected
	     */
		protected	$_parent;
		
		/**
	     *	The values
	     *
	     *	@var array
	     *	@access	protected
	     */
		protected	$_values	=	array();
		
		public	function	__construct()
		{
			$this->setDefinition();
		}
		
		/**
		 *	Return data value or null
		 *
		 *	@param string $name the data name
		 *
		 *	@return mixed
		 *
		 *	@access public
		 */
		public	function	__get($name)
		{
			return	$this->data($name);
		}
		
		/**
		 *	Set data value
		 *
		 *	@param string $name  the data name
		 *	@param string $value the data value
		 *
		 *	@access protected
		 */
		protected	function	__set($name, $value)
		{
			$this->hasData($name, $value);
		}
		
		/**
		 *	Add a new child element
		 *
		 *	@param string       $name    the element name
		 *	@param Form_Element $element the element
		 *
		 *	@return Form_Element
		 *
		 *	@access private
		 */
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
		
		/**
		 *	Object definition (to overide)
		 *
		 *	@access protected
		 */
		protected	function	setDefinition() {}
		
		/**
		 *	Clear values
		 *
		 *	@access public
		 */
		public	function	clear()
		{
			$this->_values	=	array();
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
			if(!is_array($value)) {
				$this->_values	=	array($value);
			} else {
				$this->_values	=	array_slice(array_values($value), 0, $this->_limit);
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
		 *	Add a new field
		 *
		 *	@param string     $name  the field name
		 *	@param Form_Field $field the field
		 *
		 *	@return Form_Field the new field
		 *
		 *	@access protected
		 */
		protected	function	hasField($name, Form_Field $field = NULL)
		{
			if(!is_a($field, 'Form_Field')) {
				$field	=	new Form_Field;
			}
			return	$this->_hasElement($name, $field);
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
		 *	Add a new node
		 *
		 *	@param string    $name the node name
		 *	@param Form_Node $node the node
		 *
		 *	@return Form_Node the new node
		 *
		 *	@access protected
		 */
		protected	function	hasNode($name, Form_Node $node = NULL)
		{
			if(!is_a($node, 'Form_Node')) {
				$node	=	new Form_Node;
			}
			return	$this->_hasElement($name, $node);
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
			} elseif($this->_parent === NULL || ($pname = $this->_parent->name($full)) === NULL) {
				if($this->_limit === 1) {
					$name	=	NULL;
				} else {
					$name	=	$this->_name;
				}
			} else {
				$name	=	$prefix.$pname.$glue.$this->_name.$suffix;
			}
			return	$name;
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
			return	$this->_limit === 1
				?	(array_key_exists(0, $this->_values) ? $this->_values[0] : NULL)
				:	$this->_values;
		}
		
		/**
		 *	Validate the values
		 *
		 *	@return boolean true/false if valid or not
		 *
		 *	@access public
		 */
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