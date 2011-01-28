<?php

	header('Content-type: text/html; charset=utf-8');	// Just for my firstname "ç" :p
	
	require_once	'Form_Element.php';
	require_once	'Form_Node.php';
	require_once	'Form_NodeValue.php';
	require_once	'Form_Field.php';
	require_once	'AddOn/Form_Email.php';

	// Form objects definition
	class Form_Contact extends Form_Node
	{
		protected	function	setDefinition()
		{
			$this->hasField('firstname');
			$this->hasField('lastname');
			$this->hasField('email', new Form_Email);
		}
	}
	
	class Form_Send2Friend extends Form_Contact
	{
		protected function	setDefinition()
		{
			parent::setDefinition();
			$this->hasNode('friend', new Form_Contact)->_limit	=	5;
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
				'lastname'	=>	'Carette'
			),
			array(
				'firstname'	=>	'Chantal',
				'lastname'	=>	'Lauby'
			),
			array(
				'firstname'	=>	'Alain',
				'email'	=>	'alain@lesnuls.com'
			)
		)
	);
	
	$f	=	new Form_Send2Friend;
	$f->fill($post);
	var_dump($f->validate());
	$f['friend'][1]['email']	=	'chantal[at]lesnuls.com';
	var_dump($f->validate());
	var_dump($f->value());
	
?>