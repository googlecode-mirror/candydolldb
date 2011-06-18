<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();


/* @var $folder SplFileInfo */
foreach(new DirectoryIterator(CANDYIMAGEPATH) as $folder)
{
	if($folder->isFile() || $folder->isDot()) { continue; }
	
	$NameParts = explode(' ', $folder->getFilename());
	
	/* @var $Model Model */
	$Model = new Model();
	
	$Model->setFirstName($NameParts[0]);
	
	if(count($NameParts) > 1)
	{ $Model->setLastName($NameParts[1]); }
	
	Model::InsertModel($Model, $CurrentUser);
}


if(array_key_exists('HTTP_REFERER', $_SERVER) && $_SERVER['HTTP_REFERER'])
{ header('location:'.$_SERVER['HTTP_REFERER']); }
else 
{ header('location:index.php'); }

?>