<?php

	header('Content-type: text/html; charset=utf-8');	// Just for my firstname "ç" :p

	require_once	'fORM.php';
	require_once	'AddOn/fORM_Email.php';

	// Form objects definition
	class fORM_Contact extends fORM
	{
		protected	function	setDefinition()
		{
			$this->hasOne('firstname');
			$this->hasOne('lastname');
			$this->hasOne('email', new fORM_Email);
		}
	}

	class fORM_Send2Friend extends fORM_Contact
	{
		protected function	setDefinition()
		{
			parent::setDefinition();
			$this->hasMany('friend', new fORM_Contact);
		}
	}

	// Form input values
	$post	=	array(
		'firstname'	=>	'François',
		'lastname'	=>	'Germain',
		'email'		=>	'cahnory@gmail.com',
		'friend'	=>	array(
			array(
				'firstname'	=>	'Bruno',
				'lastname'	=>	'Carette',
				'email'		=>	'bruno@lesnuls.com'
			),
			array(
				'firstname'	=>	'Chantal',
				'lastname'	=>	'Lauby',
				'email'		=>	'chantal@lesnuls.com'
			),
			array(
				'firstname'	=>	'Alain',
				'email'	=>	'alain@lesnuls.com'
			)
		)
	);

	$f	=	new Form_Send2Friend;
	$f->fill($post);
	var_dump($f->value());
	var_dump($f->validate());
	$f['friend'][1]['email']	=	'chantal[at]lesnuls.com';
	var_dump($f->validate());
	var_dump($f->value());