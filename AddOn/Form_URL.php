<?php

	class Form_URL extends Form_Field
	{		
		public	function	validate()
		{
			foreach($this->_values as $value) { 
				if(!filter_var($value, FILTER_VALIDATE_URL))
					return	false;
			}
			return	true;
		}
	}

?>