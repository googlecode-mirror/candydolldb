<?php

include('cd.php');
ini_set('max_execution_time', '3600');
$CurrentUser = Authentication::Authenticate();

if($argv && $argc > 0)
{
	// On the commandline, use absolute path
	if(file_exists(sprintf('%1$s/setup_data.xml', dirname($_SERVER['PHP_SELF']))))
	{ $XmlFromFile = new SimpleXMLElement(file_get_contents(sprintf('%1$s/setup_data.xml', dirname($_SERVER['PHP_SELF'])))); }
}
else
{
	// During a HTTP-request, use relative path
	if(file_exists('setup_data.xml'))
	{ $XmlFromFile = new SimpleXMLElement(file_get_contents('setup_data.xml')); }
}

if($XmlFromFile)
{
	$ModelsInDb = Model::GetModels();
	$SetsInDb = Set::GetSets();
	
	if($argv && $argc > 0)
	{ $bi = new BusyIndicator(count($XmlFromFile->Model), 0); }

	foreach ($XmlFromFile->Model as $Model)
	{
		if($argv && $argc > 0)
		{ $bi->Next(); }
		
		$ModelInDb = Model::FilterModels($ModelsInDb, null, $Model['firstname'], $Model['lastname']);
		if($ModelInDb){ $ModelInDb = $ModelInDb[0]; }

		/* @var $Model2Process Model */
		$Model2Process = $ModelInDb ? $ModelInDb : new Model();
		$Model2Process->setFirstName((string)$Model->attributes()->firstname);
		$Model2Process->setLastName((string)$Model->attributes()->lastname);

		$birthDate = strtotime((string)$Model->attributes()->birthdate); 
		if($birthDate !== false) { $Model2Process->setBirthdate($birthDate); }
		else { $Model2Process->setBirthdate(-1); }

		if($Model2Process->getID())
		{
			Model::UpdateModel($Model2Process, $CurrentUser);
		}
		else
		{
			Model::InsertModel($Model2Process, $CurrentUser);
			$modelid = $db->GetLatestID();
			if($modelid) { $Model2Process->setID($modelid); }
		}

		
		foreach($Model->Sets->Set as $Set)
		{
			$SetInDb = Set::FilterSets(
				$SetsInDb,
				$Model2Process->getID(),
				null,
				preg_replace('/^SP_/i', '', (string)$Set->attributes()->name)
			);
			
			if($SetInDb){ $SetInDb = $SetInDb[0]; }

			/* @var $Set2Process Set */
			$Set2Process = $SetInDb ? $SetInDb : new Set();
			$Set2Process->setModel($Model2Process);
			
			if($Set2Process->getModel()->getFirstName() == 'VIP')
			{
				$Set2Process->setPrefix('SP_');
				$Set2Process->setName(preg_replace('/^SP_/i', '', (string)$Set->attributes()->name));
				$Set2Process->setContainsWhat(SET_CONTENT_IMAGE | SET_CONTENT_VIDEO);
			}
			else if($Set2Process->getModel()->getFirstName() == 'Interviews')
			{
				$Set2Process->setPrefix('In_');
				$Set2Process->setName((string)$Set->attributes()->name);
				$Set2Process->setContainsWhat(SET_CONTENT_VIDEO);
			}
			else if($Set2Process->getModel()->getFirstName() == 'Promotions')
			{
				$Set2Process->setPrefix(null);
				$Set2Process->setName((string)$Set->attributes()->name);
				$Set2Process->setContainsWhat(SET_CONTENT_VIDEO);
			}
			else
			{
				$Set2Process->setPrefix('set_');
				$Set2Process->setName((string)$Set->attributes()->name);
				$Set2Process->setContainsWhat(SET_CONTENT_IMAGE | SET_CONTENT_VIDEO);
			}
			
			$datePic = strtotime((string)$Set->attributes()->date_pic);
			if($datePic !== false) { $Set2Process->setDatePic($datePic); }
			else { $Set2Process->setDatePic(-1); }
			
			$dateVid = strtotime((string)$Set->attributes()->date_vid);
			if($dateVid !== false) { $Set2Process->setDateVid($dateVid); }
			else { $Set2Process->setDateVid(-1); }
			

			if($Set2Process->getID())
			{
				Set::UpdateSet($Set2Process, $CurrentUser);
			}
			else
			{
				Set::InsertSet($Set2Process, $CurrentUser);
			}
		}
	}
}

if(!$argv || !$argc)
{ HTMLstuff::RefererRedirect(); }

?>