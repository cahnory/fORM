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

	class FORM_Node extends Form_Element
	{		
		/**
	     *	Model used on definition
	     *	of new Form_NodeValue.
	     *
	     *	@var Form_NodeValue
	     *	@access	protected
	     */
		protected	$_model;
		
		public	function	__construct() {}
		
		protected	function	setDefinition() {}
		
		/**
		 *	Return a node by it's offset
		 *	If the node doesn't exist it will be created
		 *	if possible.
		 *
		 *	@param mixed $offset node ofsset
		 *
		 *	@return mixed Form_NodeValue or NULL
		 *
		 *	@access public
		 */
		private	function	_getNode($offset)
		{
			if(is_int($offset) && ($offset < $this->limit() || $this->limit() < 1)) {				
				if(!array_key_exists($offset, $this->_values)) {					
					$this->_model			=	new Form_NodeValue;
					$this->_model->_name	=	$offset;
					$this->_model->_parent	=	$this;
					$this->setDefinition();
					$this->_values[$offset]	=	$this->_model;
				}													
				return	$this->_values[$offset];
			}
		}
		
		/**
		 *	Fill the values and children values
		 *
		 *	@param mixed $values  the values
		 *
		 *	@access public
		 */
		public	function	fill($values)
		{
			if(!is_array($values))
				$values	=	array($values);
			foreach($values as $offset => $value) {
				if($el = $this->offsetGet($offset)) {
					$el->fill($value);
				}
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
			return	$this->_model->hasField($name, $field);
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
			return	$this->_model->hasNode($name, $node);
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
			if($this->limit() === 1) {
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
			if($this->limit() === 1) {
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
			if($this->limit() === 1) {
				$this->_getNode(0)->offsetSet($offset, $value);
			} else {
				if($node = $this->_getNode($offset)) {
					$node->fill($value);
				}
			}
		}
		
		public	function	offsetExists($offset)
		{
			if($this->limit() === 1) {
				$this->_getNode(0)->offsetExists($offset);
			} else {
				return	(int)$offset < $this->limit() || $this->limit() < 1;
			}
		}
		
		public	function	offsetUnset($offset)
		{
			if($this->limit() === 1) {
				$this->_getNode(0)->offsetUnset($offset);
			} else {
				if($node = $this->_getNode($offset))
					$node->clear();
			}
		}
		
		public	function	offsetGet($offset)
		{
			if($this->limit() === 1) {
				return	$this->_getNode(0)->offsetGet($offset);				
			} else {
				return	$this->_getNode($offset);
			}
		}
	}

?>