Bonjour tout le monde, si je poste aujourd'hui c'est pour vous présenter fORM.

fORM est une class php5 qui a pour but de faciliter la gestion de formulaires pour le web. En définissant à l'avance la structure des données à récupérer, il devient facile d'automatiser les tâches plus ou moins courrantes et récurantes (validation, affichage,...).

Création et définition d'un formulaire

Pour créer un nouveau formulaire il suffit de créer une nouvelle class qui étend fORM. Prenons l'exemple d'un formulaire de contact :

class Form_Contact extends fORM
{
    protected function setDefinition()
    {
        //	Définition du formulaire ici
    }
}

La méthode protected setDefinition vous permet de définir la structure des données du formulaire. Nous allons donc nous en servir en commençant par ajouter les champs lastname, firstname, email :

class Form_Contact extends fORM
{
    protected function setDefinition()
    {
        $this->hasOne('lastname');
        $this->hasOne('firstname');
        $this->hasOne('email');
    }
}

Nous avons donc ajouté nos trois champs à l'aide de la méthode hasOne. Un contact pourraît avoir plusieurs adresse email, nous alons modifier notre formulaire pour permettre la récupération de plusieurs adresses :

class Form_Contact extends fORM
{
    protected function setDefinition()
    {
        $this->hasOne('lastname');
        $this->hasOne('firstname');
        $this->hasMany('email');
    }
}

Peut-être serait-il judicieux de limiter le nombre d'adresses à... disons 5 :

class Form_Contact extends fORM
{
    protected function setDefinition()
    {
        $this->hasOne('lastname');
        $this->hasOne('firstname');
        $email	=	$this->hasMany('email');
        $email->hasLimit(5);
    }
}

Vous noterez que si nous définissons la limite manuellement nous pouvons aussi bien utiliser hasOne que hasMany.

Compliquons un peu les choses avec un formulaire d'envois à des amis :

class Form_Send2Friends extends fORM
{
    protected function setDefinition()
    {
        $this->hasOne('lastname');
        $this->hasOne('firstname');
        $this->hasMany('email');
        $this->hasOne('message');
        $friend	=	$this->hasMany('friend');
        $friend->hasOne('lastname');
        $friend->hasOne('firstname');
        $friend->hasMany('email');
    }
}

Ce qui saute tout de suite aux yeux c'est que notre formulaire est composé des même champs que le formulaire de contact. Nous y avons simplement ajouté un champs message ainsi qu'un "noeud" friend ayant lui aussi la même composition que le formulaire de contact. Nous allons pouvoir simplifier tout ça :

class Form_Send2Friends extends Form_Contact
{
    protected function setDefinition()
    {
    	parent::setDefinition();
        $this->hasOne('message');
        $this->hasMany('friend', new Form_Contact);
    }
}

C'est quand même bien pratique est nettement plus clair ainsi. En étandant la class Form_Contact tout en appelant sa définition (parent::setDefinition()), nous héritons de toutes ses propriétés auxquels nous pouvons ajouter de nouvelles.
Vous remarquerez que nous pouvons aussi passer un objet fORM en argument des fonction hasOne et hasMany afin de combiner et réutiliser facilement des formulaires existant.


Utilisation d'un formulaire

Pour remplir notre formulaire nous alons utiliser la méthode fill :

$form  =   new Form_Send2Friend;
$form->fill($_POST);

pour en récupérer les valeurs nous utiliserons la méthode value :

$form->value();

fORM implémente les interface Iterator et ArrayAccess permettant de parcourir notre formulaire comme un simple array. Nous récupérons toujours un objet fORM ce qui nous permet de modifier, supprimer, ajouter, valider... tout ou partie de notre formulaire :

//	retournera toutes les valeurs de notre formulaire
$form->value();

//	retournera la valeur du champs friend[0][email]
$form['friend'][0]['email']->validate();

// ajoute une adresse email au friend 0
$form['friend'][0]['email'][]	=	'cahnory@gmail.com';

//	affichera tous les valeurs de friend
foreach($form['friend'] as $friend) {
	echo $friend->value().'<br />';
}

Chaque objet fORM dispose de plusieurs méthodes publiques :
clear
data
fill
isParent
length
limit
name
parent
validate
value

La description de chaqu'une d'entre elle est présente dans la source de la class mais il est, pour la plupart, facil d'imaginer à quoi elles sont destinées.

Une de ces méthodes qui doit attiser les curiosité est sans doute la méthode validate. Chaque fORM peut décider de comment il sera validé à l'aide de la méthode validateValue qui doit retourner une valeur boolean.
Prenons le cas courant de l'adresse email. Nous allons créer un objet Field_Email comme ceci :

class Field_Email extends fORM
{
    protected	function	validateValue($value)
    {
        return	filter_var($value, FILTER_VALIDATE_EMAIL);
    }
}

Je ne reviens pas sur la méthode de validation, c'est un problème générique qui ne manque pas d'explication sur le net. Nous alons maintenant utiliser ce nouvel objet dans notre formulaire de contact :

class Form_Contact extends fORM
{
    protected function setDefinition()
    {
        $this->hasOne('lastname');
        $this->hasOne('firstname');
        $this->hasMany('email', new Field_Email);
    }
}

Notre champs email n'est plus un simple objet fORM mais un objet Field_Email et utilisera donc la fonction de validation de Field_Email.

Et l'affichage dans tout ça ?

L'affichage n'est pas quelque chose de propre aux formulaires. En grande majorité réalisé en html, l'affichage pourraît très bien se faire dans d'autre format et pour cette raison il n'est pas pris en charge par fORM... c'est là qu'intervient les méthodes hasData et data.
La méthode protected hasData va vous permettre de définir des données qui ne sont pas spécifiques aux formulaires mais qui n'en sont pas moins utiles :

class Form_Send2Friends extends Form_Contact
{
    protected function setDefinition()
    {
    	parent::setDefinition();
        $message	=	$this->hasOne('message');
        
        //	Définition de toutes nos data
        $message->hasData(array(
        	'type'	=>	'textarea',
        	'label'	=>	'Message to your friend(s)'
        ));
        
        //	Définition d'une seule valeur
        $message->hasData('placeHolder', 'Type your message here...');
        
        $this->hasMany('friend', new Form_Contact);
    }
}

Ces informations couplées aux implémentations d'Iterator et ArrayAccess nous permet de générer facilement et automatiquement n'importe quel formulaire à l'aide de fichier de template par exemple. Un exemple (sommaire) tournant sur un framework personnel est en ligne : http://cahnory.fr/fORMDemo/

N'hésitez pas à me faire part de vos remarques, elles seront appréciées. Je vous laisse avec quelques liens :
Le projet sur github (code source) : https://github.com/cahnory/fORM
La démo en ligne : http://cahnory.fr/fORMDemo/
La démo à télécharger : http://cahnory.fr/fORMDemo/files/fORM-demo.zip