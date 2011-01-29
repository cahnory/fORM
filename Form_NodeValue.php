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

	class FORM_NodeValue extends Form_Element
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
			return	in_array($offset, $this->_offsets);
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