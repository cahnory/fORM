<?php

	class Form_INT extends Form_Field
	{		
		public	function	validate()
		{
			foreach($this->_values as $value) { 
				if(!preg_match('#^[0-9]+$#', $this->_value))
					return	false;
			}
			return	true;
		}
	}

?>