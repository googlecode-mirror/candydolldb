<?php

include('cd.php');
ini_set('max_execution_time', '3600');
$CurrentUser = Authentication::Authenticate();

$OutputSql = array_key_exists('output', $_GET) && isset($_GET['output']) && is_numeric($_GET['output']) && (int)$_GET['output'] === 1;

$ModelID = null;
$SetID = null;


if(array_key_exists('model_id', $_GET) && isset($_GET['model_id']) && is_numeric($_GET['model_id']))
{ $ModelID = (int)$_GET['model_id']; }

if(array_key_exists('set_id', $_GET) && isset($_GET['set_id']) && is_numeric($_GET['set_id']))
{ $SetID = (int)$_GET['set_id']; }


$Models = Model::GetModels(
	sprintf(
		'model_id = IFNULL(%1$s, model_id) AND mut_deleted = -1',
		$ModelID ? (string)$ModelID : 'NULL'
	)
);

$Sets = Set::GetSets(
	sprintf(
		'set_id = IFNULL(%1$s, set_id) AND mut_deleted = -1',
		$SetID ? (string)$SetID : 'NULL'
	)
);

$Videos = Video::GetVideos(
	sprintf(
		'model_id = IFNULL(%1$s, model_id) AND set_id = IFNULL(%2$s, set_id) AND mut_deleted = -1',
		$ModelID ? (string)$ModelID : 'NULL',
		$SetID ? (string)$SetID : 'NULL'
	)
);


if($SetID){
	$Set = $Sets[0];
	$Models = array($Set->getModel());
}


/* @var $Model Model */
for($i = 0; $i < count($Models); $i++)
{
	$Model = $Models[$i];
	
	$VideoFolder = sprintf('%1$s/%2$s',
		CANDYVIDEOPATH,
		$Model->GetFullName()
	);
	
	if(!file_exists($VideoFolder)) { continue; }
	
	
	/* @var $FileInfo SplFileInfo */
	foreach(new DirectoryIterator($VideoFolder) as $FileInfo)
	{
		if($FileInfo->isFile() && $FileInfo->isReadable())
		{
			$name = $FileInfo->getFilename();
			$setnamematch = preg_match('/(\d\d)'.VIDEO_EXTENSION.'$/i', $name, $matches);
			$setname = $matches && $matches > 1 ? $matches[1] : null;
			
			$Set = Set::FilterSets($Sets, $Model->getID());
			$SetFiltered = Set::FilterSets($Set, null, null, substr($FileInfo->getBasename(VIDEO_EXTENSION), 3)); 
			
			if(!$SetFiltered)
			{ $SetFiltered = Set::FilterSets($Set, null, null, $setname); }
			
			if($SetFiltered)
			{ $Set = $SetFiltered[0]; }
			else
			{ continue; }
			
			if(strlen($Set->getName()) == 2 && $Set->getName() != $setname)
			{ continue; }

			
			/* @var $VideoInDB Video */
			$VideosInDB = Video::FilterVideos($Videos, $ModelID, $Set->getID());
		
			if($VideosInDB)
			{
				$VideoInDB = $VideosInDB[0];
			}
			else
			{
				$VideoInDB = new Video();
				$VideoInDB->setSet($Set);
			}
			
			$VideoInDB->setFileName($FileInfo->getBasename(VIDEO_EXTENSION));
			$VideoInDB->setFileExtension(trim(VIDEO_EXTENSION, '.'));
			$VideoInDB->setFileSize($FileInfo->getSize());
			$VideoInDB->setFileCheckSum(md5_file($FileInfo->getRealPath()));
			$VideoInDB->setDateTaken($Set->getDateVid());
			
			if(!$VideoInDB->getID())
			{ Video::InsertVideo($VideoInDB, $CurrentUser); }
			else
			{ Video::UpdateVideo($VideoInDB, $CurrentUser); }
		}
	}
}

HTMLstuff::RefererRedirect();

?>