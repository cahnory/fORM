<?php

	class fORM_URL extends fORM
	{
		protected	function	validateValue($value)
		{
			return	filter_var($value, FILTER_VALIDATE_URL);
		}
	}

?>