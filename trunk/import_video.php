<?php

include('cd.php');
ini_set('max_execution_time', '3600');
$CurrentUser = Authentication::Authenticate();

if(!$CurrentUser->hasPermission(RIGHT_VIDEO_ADD) && !$CurrentUser->hasPermission(RIGHT_VIDEO_EDIT))
{
	$e = new Error(RIGHTS_ERR_USERNOTALLOWED);
	Error::AddError($e);
	HTMLstuff::RefererRedirect();
}

$ModelID = Utils::SafeIntFromQS('model_id');
$SetID = Utils::SafeIntFromQS('set_id');

$Models = Model::GetModels(new ModelSearchParameters(
	is_null($ModelID) ? FALSE : $ModelID));
$Sets = Set::GetSets(new SetSearchParameters(
	is_null($SetID) ? FALSE : $SetID));
$Videos = Video::GetVideos(new VideoSearchParameters(
		FALSE,
		FALSE,
		(is_null($SetID) ? FALSE : $SetID),
		FALSE,
		is_null($ModelID) ? FALSE : $ModelID));
$CacheImages = CacheImage::GetCacheImages();

if($SetID){
	$Set = $Sets[0];
	$Models = array($Set->getModel());
}

/* @var $Model Model */
for($i = 0; $i < count($Models); $i++)
{
	$Model = $Models[$i];

	$VideoFolder = sprintf('%1$s/%2$s',
		CANDYPATH,
		$Model->GetFullName()
	);

	if(!file_exists($VideoFolder)) { continue; }
	
	/* @var $it RecursiveIteratorIterator */
	$it = new RecursiveDirectoryIterator(
			$VideoFolder,	
		 	FileSystemIterator::SKIP_DOTS | FileSystemIterator::CURRENT_AS_FILEINFO);

	$itArray = array();
	foreach($it as $file)
	{ $itArray[] = $file; }
	
	if(isset($argv) && $argc > 0)
	{ $bi = new BusyIndicator(count($itArray), 0, sprintf('%1$2d/%2$2d %3$s', ($i + 1), count($Models), $Model->GetShortName())); }


	/* @var $FileInfo SplFileInfo */
	foreach($itArray as $FileInfo)
	{
		if(isset($argv) && $argc > 0)
		{ $bi->Next(); }
		
		if($FileInfo->isFile() && $FileInfo->isReadable())
		{
			$setnamematch = preg_match('/(?P<Prefix>[A-Z]+[_ -])?(?P<Name>[A-Z0-9]+)(?P<Number>\d\d)(?P<Suffix>[a-z])?\.(?P<Extension>[^.]+)$/i', $FileInfo->getFilename(), $matches);
			
			if(isset($matches) && count($matches) > 0)
			{
				$Set = Set::Filter($Sets, $Model->getID(), NULL, ($matches['Name'].$matches['Number']), $matches['Prefix']);

				if(!$Set)
				{ continue; }
				else
				{ $Set = $Set[0]; }
			
				/* @var $VideoInDB Video */
				$VideosInDB = Video::Filter($Videos, $ModelID, $Set->getID(), $matches['Name'].$matches['Number'].$matches['Suffix']);

				if($VideosInDB)
				{
					$VideoInDB = $VideosInDB[0];
					
					$cis = CacheImage::Filter($CacheImages, NULL, NULL, NULL, NULL, NULL, $VideoInDB->getID());
					CacheImage::DeleteMulti($cis, $CurrentUser);
				}
				else
				{
					$VideoInDB = new Video();
					$VideoInDB->setSet($Set);
				}
				
				$VideoInDB->setFileName($matches['Prefix'].$matches['Name'].$matches['Number'].$matches['Suffix']);
				$VideoInDB->setFileExtension($matches['Extension']);
				$VideoInDB->setFileSize($FileInfo->getSize());
				$VideoInDB->setFileCheckSum(Utils::CalculateMD5($FileInfo->getRealPath()));
				$VideoInDB->setFileCRC32(Utils::CalculateCRC32($FileInfo->getRealPath()));
					
				if(!$VideoInDB->getID() && $CurrentUser->hasPermission(RIGHT_VIDEO_ADD))
				{
					Video::Insert($VideoInDB, $CurrentUser);
				}
				else if($CurrentUser->hasPermission(RIGHT_VIDEO_EDIT))
				{
					Video::Update($VideoInDB, $CurrentUser);
				}
			}
		}
	}
	
	$infoSuccess = new Info($lang->g('MessageVideosImported'));
	Info::AddInfo($infoSuccess);
	
	if(isset($argv) && $argc > 0)
	{ $bi->Finish(); }
}

HTMLstuff::RefererRedirect();

?>