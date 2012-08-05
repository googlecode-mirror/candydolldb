<?php

include('cd.php');
ini_set('max_execution_time', '3600');
$CurrentUser = Authentication::Authenticate();


$ModelID = Utils::SafeIntFromQS('model_id');
$SetID = Utils::SafeIntFromQS('set_id');

$CacheImages = array();
$CacheImage = NULL;


$Models = Model::GetModels(new ModelSearchParameters(
	is_null($ModelID) ? FALSE : $ModelID));
$Sets = Set::GetSets(new SetSearchParameters(
	is_null($SetID) ? FALSE : $SetID));
$Images = Image::GetImages(new ImageSearchParameters(
	FALSE,
	FALSE,
	is_null($SetID) ? FALSE : $SetID,
	FALSE,
	is_null($ModelID) ? FALSE : $ModelID));
$CacheImages = CacheImage::GetCacheImages();


if($SetID && $Sets){
	$Set = $Sets[0];
	$Models = array($Set->getModel());
}


/* @var $Model Model */
for($i = 0; $i < count($Models); $i++)
{
	$Model = $Models[$i];
	
	$CacheImage = CacheImage::Filter($CacheImages, NULL, $Model->getID());
	CacheImage::DeleteMulti($CacheImage, $CurrentUser);
	
	$CacheImage = CacheImage::Filter($CacheImages, NULL, NULL, $Model->getID());
	CacheImage::DeleteMulti($CacheImage, $CurrentUser);

	$ImageFolder = sprintf('%1$s/%2$s',
		CANDYIMAGEPATH,
		$Model->GetFullName()
	);
	
	if(!file_exists($ImageFolder)) { continue; }
	
	/* @var $it RecursiveIteratorIterator */
	$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(
			$ImageFolder,	
		 	FileSystemIterator::SKIP_DOTS | FileSystemIterator::CURRENT_AS_FILEINFO));
	

	$itArray = array();
	foreach($it as $file)
	{ $itArray[] = $file; }
	
	if(isset($argv) && $argc > 0)
	{ $bi = new BusyIndicator(count($itArray), 0, sprintf('%1$2d/%2$2d %3$s', ($i + 1), count($Models), $Model->GetShortName())); }

		 	
	/* @var $file SplFileInfo */
	foreach($itArray as $file)
	{
		if(isset($argv) && $argc > 0)
		{ $bi->Next(); }
		
		if($file->isFile() && $file->isReadable())
		{
			$imagenamematch = preg_match('/(?P<Prefix>[A-Z]+[_ -])?(?P<ModelName>[A-Z0-9]+)(?P<SetNumber>\d\d)_(?P<Number>[0-9]{3})\.(?P<Extension>[^.]+)$/i', $file->getFilename(), $matches);
			
			if($imagenamematch)
			{
				$Set = Set::Filter($Sets, $Model->getID(), NULL, ($matches['ModelName'].$matches['SetNumber']), $matches['Prefix']);

				if($Set)
				{ $Set = $Set[0]; }
				else
				{ continue; }
				
				$CacheImage = CacheImage::Filter($CacheImages, NULL, NULL, NULL, $Set->getID());
				CacheImage::DeleteMulti($CacheImage, $CurrentUser);

				/* @var $ImageInDB Image */
				$ImagesInDB = Image::FilterImages(
					$Images,
					$ModelID,
					$Set->getID(),
					sprintf('%1$s%2$s%3$s_%4$s', $matches['Prefix'], $matches['ModelName'], $matches['SetNumber'], $matches['Number'])
				);

				if($ImagesInDB)
				{
					$ImageInDB = $ImagesInDB[0];
					
					$CacheImage = CacheImage::Filter($CacheImages, NULL, NULL, NULL, NULL, $ImageInDB->getID());
					CacheImage::DeleteMulti($CacheImage, $CurrentUser);
				}
				else
				{
					$ImageInDB = new Image();
					$ImageInDB->setSet($Set);
				}
				
				$info = getimagesize($file->getRealPath());

				$ImageInDB->setFileName(sprintf('%1$s%2$s%3$s_%4$s', $matches['Prefix'], $matches['ModelName'], $matches['SetNumber'], $matches['Number']));
				$ImageInDB->setFileExtension($matches['Extension']);
				$ImageInDB->setFileSize($file->getSize());
				$ImageInDB->setFileCheckSum(md5_file($file->getRealPath()));
				$ImageInDB->setFileCRC32(Utils::CalculateCRC32($file->getRealPath()));
				$ImageInDB->setImageWidth($info[0]);
				$ImageInDB->setImageHeight($info[1]);
					
				if(!$ImageInDB->getID())
				{ Image::Insert($ImageInDB, $CurrentUser); }
				else
				{ Image::Update($ImageInDB, $CurrentUser); }
			}
		}
	}
	
	$infoSuccess = new Info($lang->g('MessageImagesImported'));
	Info::AddInfo($infoSuccess);
	
	if(isset($argv) && $argc > 0)
	{ $bi->Finish(); }
}

if(!isset($argv) || !$argc)
{ HTMLstuff::RefererRedirect(); }

?>