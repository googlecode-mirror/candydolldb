<?php
/*	This file is part of CandyDollDB.

CandyDollDB is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

CandyDollDB is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with CandyDollDB.  If not, see <http://www.gnu.org/licenses/>.
*/

include('cd.php');
ini_set('max_execution_time', '3600');
$CurrentUser = Authentication::Authenticate();

/**
 * Parses an array of strings into an array of Date objects.
 * @param array(string) $InArray
 * @param int $DateKind
 * @param Set $Set
 * @return array(Date)
 */
function ParseDates($InArray, $DateKind = DATE_KIND_UNKNOWN, $Set = null)
{
	$OutArray = array();
	if(is_array($InArray) && count($InArray) > 0)
	{
		for ($i = 0; $i < count($InArray); $i++)
		{
			$timestamp = strtotime($InArray[$i]);
			if($timestamp !== false)
			{
				/* @var $Date Date */
				$Date = new Date();
				
				$Date->setSet($Set);
				$Date->setDateKind($DateKind);
				$Date->setTimeStamp($timestamp);
				
				$OutArray[] = $Date;
			}
		} 
	}
	return $OutArray;	
}


if(isset($argv) && $argc > 0)
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
	$DatesInDb = Date::GetDates();
	
	if(isset($argv) && $argc > 0)
	{ $bi = new BusyIndicator(count($XmlFromFile->Model), 0); }

	foreach ($XmlFromFile->Model as $Model)
	{
		if(isset($argv) && $argc > 0)
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
		
		if(!$Model->Sets)
		{ continue; }

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
			
			if($Set2Process->getID())
			{
				Set::UpdateSet($Set2Process, $CurrentUser);
			}
			else
			{
				Set::InsertSet($Set2Process, $CurrentUser);
				$setid = $db->GetLatestID();
				if($setid) { $Set2Process->setID($setid); }
				
				$CacheImages = CacheImage::GetCacheImages(sprintf('index_id = %1$d', $Model2Process->getID()));
				CacheImage::DeleteImages($CacheImages, $CurrentUser);
			}
			
			$datesPic = array();
			$datesVid = array();
			
			preg_match_all('/[0-9]{4}-[01][0-9]-[0123][0-9]/ix', (string)$Set->attributes()->date_pic, $datesPic);
			$Set2Process->setDatesPic(
				ParseDates($datesPic[0], DATE_KIND_IMAGE, $Set2Process)
			);

			preg_match_all('/[0-9]{4}-[01][0-9]-[0123][0-9]/ix', (string)$Set->attributes()->date_vid, $datesVid);
			$Set2Process->setDatesVid(
				ParseDates($datesVid[0], DATE_KIND_VIDEO, $Set2Process)
			);
			
			/* @var $Date Date */
			/* @var $dateInDb Date */
			foreach ($Set2Process->getDatesPic() as $Date)
			{
				$dateInDb = Date::FilterDates($DatesInDb, null, $Set2Process->getModel()->getID(), $Set2Process->getID(), DATE_KIND_IMAGE, $Date->getTimeStamp());
				
				if($dateInDb)
				{
					$dateInDb = $dateInDb[0];
					$Date->setID($dateInDb->getID());
				}
				
				if($Date->getID())
				{ Date::UpdateDate($Date, $CurrentUser); }
				else
				{ Date::InsertDate($Date, $CurrentUser); }
			}
			
			foreach ($Set2Process->getDatesVid() as $Date)
			{
				$dateInDb = Date::FilterDates($DatesInDb, null, $Set2Process->getModel()->getID(), $Set2Process->getID(), DATE_KIND_VIDEO, $Date->getTimeStamp());
				
				if($dateInDb)
				{
					$dateInDb = $dateInDb[0];
					$Date->setID($dateInDb->getID());
				}
				
				if($Date->getID())
				{ Date::UpdateDate($Date, $CurrentUser); }
				else
				{ Date::InsertDate($Date, $CurrentUser); }
			}
		}
	}
	
	if(isset($argv) && $argc > 0)
	{ $bi->Finish(); }
}

if(!isset($argv) || !$argc)
{ HTMLstuff::RefererRedirect(); }

?>