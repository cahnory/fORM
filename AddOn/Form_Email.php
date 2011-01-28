<?php

	class Form_Email extends Form_Field
	{		
		public	function	validate()
		{
			foreach($this->_values as $value) { 
				if(!filter_var($value, FILTER_VALIDATE_EMAIL))
					return	false;
			}
			return	true;
		}
	}

?>