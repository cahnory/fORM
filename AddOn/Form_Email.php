<?php

	class fORM_Email extends fORM
	{
		protected	function	validateValue($value)
		{
			return	filter_var($value, FILTER_VALIDATE_EMAIL);
		}
	}

?>