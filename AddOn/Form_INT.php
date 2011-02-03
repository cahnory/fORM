<?php

	class fORM_INT extends fORM
	{
		protected	function	validateValue($value)
		{
			return	preg_match('#^[0-9]+$#', $value);
		}
	}

?>