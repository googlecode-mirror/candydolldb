<?php

include('cd.php');
ini_set('max_execution_time', '3600');
$CurrentUser = Authentication::Authenticate();
$ModelID = Utils::SafeIntFromQS('model_id');

$XmlFromFile = NULL;
$fileToProcess = 'setup_data.xml';
$Tag2AllsInDB = Tag2All::GetTag2Alls();
$TagsInDB = Tag::GetTags();

if(array_key_exists('file', $_GET) && isset($_GET['file']))
{ $fileToProcess = $_GET['file']; }

if(isset($argv) && $argc > 0)
{
	if(file_exists(sprintf('%1$s/%2$s', dirname($_SERVER['PHP_SELF']), $fileToProcess)))
	{ $XmlFromFile = new SimpleXMLElement(file_get_contents(sprintf('%1$s/%2$s', dirname($_SERVER['PHP_SELF']), $fileToProcess))); }
}
else
{
	if(file_exists($fileToProcess))
	{ $XmlFromFile = new SimpleXMLElement(file_get_contents($fileToProcess)); }
}

if($XmlFromFile)
{
	$ModelsInDb = Model::GetModels();
	$SetsInDb = Set::GetSets();
	$DatesInDb = Date::GetDates();
	$VideosInDb = Video::GetVideos();
	
	if(isset($argv) && $argc > 0)
	{ $bi = new BusyIndicator(count($XmlFromFile->Model), 0); }

	foreach ($XmlFromFile->Model as $Model)
	{
		if(isset($argv) && $argc > 0)
		{ $bi->Next(); }
		
		$ModelInDb = Model::Filter(
			$ModelsInDb,
			NULL,
			(string)$Model->attributes()->firstname,
			(string)$Model->attributes()->lastname
		);
		
		if($ModelInDb)
		{ $ModelInDb = $ModelInDb[0]; }

		/* @var $Model2Process Model */
		$Model2Process = $ModelInDb ? $ModelInDb : new Model();
		
		// Provide a one-model-only import for impatient developers
		if($Model2Process->getID() && $ModelID && $Model2Process->getID() !== $ModelID)
		{ continue; }
		
		$Model2Process->setFirstName((string)$Model->attributes()->firstname);
		$Model2Process->setLastName((string)$Model->attributes()->lastname);

		$birthDate = strtotime((string)$Model->attributes()->birthdate); 
		if($birthDate !== FALSE) { $Model2Process->setBirthdate($birthDate); }
		else { $Model2Process->setBirthdate(-1); }
		
		if($Model->Remarks){
			$Model2Process->setRemarks($Model->Remarks);
		}

		if($Model2Process->getID())
		{
			Model::Update($Model2Process, $CurrentUser);
		}
		else
		{
			Model::Insert($Model2Process, $CurrentUser);
		}
		
		$modeltags = Tag::GetTagArray((string)$Model->attributes()->tags);
		$Tag2AllThisModel = Tag2All::Filter($Tag2AllsInDB, NULL, $Model2Process->getID(), FALSE, FALSE, FALSE);
		$Tag2AllThisModelOnly = Tag2All::Filter($Tag2AllThisModel, NULL, $Model2Process->getID(), NULL, NULL, NULL);
		Tag2All::HandleTags($modeltags, $Tag2AllThisModelOnly, $TagsInDB, $CurrentUser, $Model2Process->getID(), NULL, NULL, NULL, FALSE);
		
		if(!$Model->Sets)
		{ continue; }
		
		$DatesThisModel = Date::FilterDates($DatesInDb, NULL, $Model2Process->getID(), NULL, NULL, NULL);

		foreach($Model->Sets->Set as $Set)
		{
			$SetInDb = Set::Filter(
				$SetsInDb,
				$Model2Process->getID(),
				NULL,
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
			}
			else if($Set2Process->getModel()->getFirstName() == 'Interviews')
			{
				$Set2Process->setPrefix('In_');
				$Set2Process->setName((string)$Set->attributes()->name);
			}
			else if($Set2Process->getModel()->getFirstName() == 'Promotions')
			{
				$Set2Process->setPrefix(NULL);
				$Set2Process->setName((string)$Set->attributes()->name);
			}
			else
			{
				$Set2Process->setPrefix('set_');
				$Set2Process->setName((string)$Set->attributes()->name);
			}
			
			if($Set2Process->getID())
			{
				Set::Update($Set2Process, $CurrentUser);
			}
			else
			{
				Set::Insert($Set2Process, $CurrentUser);
				
				$CacheImages = CacheImage::GetCacheImages(new CacheImageSearchParameters(FALSE, FALSE, $Model2Process->getID()));
				CacheImage::DeleteMulti($CacheImages, $CurrentUser);
			}
			
			$settags = Tag::GetTagArray((string)$Set->attributes()->tags);
			$Tag2AllThisSet = Tag2All::Filter($Tag2AllThisModel, NULL, $Model2Process->getID(), $Set2Process->getID(), NULL, NULL);
			Tag2All::HandleTags($settags, $Tag2AllThisSet, $TagsInDB, $CurrentUser, $Model2Process->getID(), $Set2Process->getID(), NULL, NULL, FALSE);
			
			$datesPic = array();
			$datesVid = array();
			
			preg_match_all('/[0-9]{4}-[01][0-9]-[0123][0-9]/ix', (string)$Set->attributes()->date_pic, $datesPic);
			$Set2Process->setDatesPic(
				Date::ParseDates($datesPic[0], DATE_KIND_IMAGE, $Set2Process)
			);

			preg_match_all('/[0-9]{4}-[01][0-9]-[0123][0-9]/ix', (string)$Set->attributes()->date_vid, $datesVid);
			$Set2Process->setDatesVid(
				Date::ParseDates($datesVid[0], DATE_KIND_VIDEO, $Set2Process)
			);
			
			// Reset the Set's CONTAINS_WHAT
			$Set2Process->setContainsWhat(SET_CONTENT_NONE);
			
			if($Set2Process->getDatesPic())
			{
				$Set2Process->setContainsWhat(
					$Set2Process->getContainsWhat() | SET_CONTENT_IMAGE
				);
			}
			
			if($Set2Process->getDatesVid())
			{
				$Set2Process->setContainsWhat(
					$Set2Process->getContainsWhat() | SET_CONTENT_VIDEO
				);
			}

			if($Set2Process->getModel()->getFirstName() == 'Promotions')
			{
				$Set2Process->setContainsWhat(
					$Set2Process->getContainsWhat() | SET_CONTENT_VIDEO
				);
			}

			// Update the Set's CONTAINS_WHAT
			Set::Update($Set2Process, $CurrentUser);
			
			/* @var $Date Date */
			/* @var $dateInDb Date */
			foreach ($Set2Process->getDatesPic() as $Date)
			{
				$dateInDb = Date::FilterDates($DatesThisModel, NULL, NULL, $Set2Process->getID(), DATE_KIND_IMAGE, $Date->getTimeStamp());
				
				if($dateInDb)
				{
					$dateInDb = $dateInDb[0];
					$Date->setID($dateInDb->getID());
				}
				
				if(!$Date->getID())
				{ Date::Insert($Date, $CurrentUser); }
			}
			
			foreach ($Set2Process->getDatesVid() as $Date)
			{
				$dateInDb = Date::FilterDates($DatesThisModel, NULL, NULL, $Set2Process->getID(), DATE_KIND_VIDEO, $Date->getTimeStamp());
				
				if($dateInDb)
				{
					$dateInDb = $dateInDb[0];
					$Date->setID($dateInDb->getID());
				}
				
				if(!$Date->getID())
				{ Date::Insert($Date, $CurrentUser); }
			}
			
			if($Set->Images)
			{
				$ImagesInDb = Image::GetImages(new ImageSearchParameters(
					FALSE, FALSE,
					$Set2Process->getID()
				));
				
				foreach($Set->Images->Image as $Image)
				{
					$ImageInDb = Image::Filter(
						$ImagesInDb,
						$Model2Process->getID(),
						$Set2Process->getID(),
						(string)$Image->attributes()->name
					);
				
					if($ImageInDb)
					{ $ImageInDb = $ImageInDb[0]; }
				
					/* @var $Image2Process Image */
					$Image2Process = $ImageInDb ? $ImageInDb : new Image();
					$Image2Process->setSet($Set2Process);
					$Image2Process->setFileName((string)$Image->attributes()->name);
					$Image2Process->setFileExtension((string)$Image->attributes()->extension);
					$Image2Process->setImageHeight((int)$Image->attributes()->height);
					$Image2Process->setImageWidth((int)$Image->attributes()->width);
					$Image2Process->setFileSize((int)$Image->attributes()->filesize);
					$Image2Process->setFileCheckSum((string)$Image->attributes()->checksum);
					$Image2Process->setFileCRC32((string)$Image->attributes()->crc32);
					
					if($Image2Process->getID())
					{
						Image::Update($Image2Process, $CurrentUser);
					}
					else
					{
						Image::Insert($Image2Process, $CurrentUser);
					}
					
					$imagetags = Tag::GetTagArray((string)$Image->attributes()->tags);
					$Tag2AllThisImage = Tag2All::Filter($Tag2AllThisSet, NULL, $Model2Process->getID(), $Set2Process->getID(), $Image2Process->getID(), NULL);
					Tag2All::HandleTags($imagetags, $Tag2AllThisImage, $TagsInDB, $CurrentUser, $Model2Process->getID(), $Set2Process->getID(), $Image2Process->getID(), NULL, FALSE);
				}
			}
			
			if($Set->Videos)
			{
				foreach($Set->Videos->Video as $Video)
				{
					$VideoInDb = Video::Filter(
						$VideosInDb,
						$Model2Process->getID(),
						$Set2Process->getID(),
						(string)$Video->attributes()->name
					);
				
					if($VideoInDb)
					{ $VideoInDb = $VideoInDb[0]; }
				
					/* @var $Video2Process Video */
					$Video2Process = $VideoInDb ? $VideoInDb : new Video();
					$Video2Process->setSet($Set2Process);
					$Video2Process->setFileName((string)$Video->attributes()->name);
					$Video2Process->setFileExtension((string)$Video->attributes()->extension);
					$Video2Process->setFileSize((int)$Video->attributes()->filesize);
					$Video2Process->setFileCheckSum((string)$Video->attributes()->checksum);
					$Video2Process->setFileCRC32((string)$Video->attributes()->crc32);
					
					if($Video2Process->getID())
					{
						Video::Update($Video2Process, $CurrentUser);
					}
					else
					{
						Video::Insert($Video2Process, $CurrentUser);
					}
					
					$videotags = Tag::GetTagArray((string)$Video->attributes()->tags);
					$Tag2AllThisVideo = Tag2All::Filter($Tag2AllThisSet, NULL, $Model2Process->getID(), $Set2Process->getID(), NULL, $Video2Process->getID());
					Tag2All::HandleTags($videotags, $Tag2AllThisVideo, $TagsInDB, $CurrentUser, $Model2Process->getID(), $Set2Process->getID(), NULL, $Video2Process->getID(), FALSE);
				}
			}
		}
	}
	
	if($fileToProcess != 'setup_data.xml')
	{ unlink(realpath($fileToProcess)); }
	
	$infoSuccess = new Info($lang->g('MessageXMLImported'));
	Info::AddInfo($infoSuccess);
	
	if(isset($argv) && $argc > 0)
	{ $bi->Finish(); }
}

HTMLstuff::RefererRedirect();

?>