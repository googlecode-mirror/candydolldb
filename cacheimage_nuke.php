<?php

include('cd.php');
ini_set('max_execution_time', '3600');
$CurrentUser = Authentication::Authenticate();
$FileToFind = '';

$CacheFolder = null;
$CacheImages = CacheImage::GetCacheImages();


if(isset($argv) && $argc > 0)
{ $CacheFolder = sprintf('%1$s/cache', dirname($_SERVER['PHP_SELF'])); }
else
{ $CacheFolder = 'cache'; }


/* @var $it RecursiveIteratorIterator */
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(
	$CacheFolder,
	FileSystemIterator::SKIP_DOTS | FileSystemIterator::CURRENT_AS_FILEINFO
));

/* @var $file SplFileInfo */
foreach($it as $file)
{
	$idToFind = $file->getBasename('.jpg');
	$matches = array();

	if(preg_match_all('/^(?<Prefix>[MXSIV]-)?[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $idToFind, $matches) > 0)
	{ 
		$where = sprintf("cache_id = '%1\$s'", str_ireplace($matches['Prefix'], '', $idToFind));
		$CacheImageInDB = CacheImage::GetCacheImages($where);
	
		if(!$CacheImageInDB)
		{ unlink($file->getRealPath()); }
	}
}

/* @var $CacheImage CacheImage */
foreach($CacheImages as $CacheImage)
{
	$FileToFind = $CacheImage->getFilenameOnDisk();
	if(!file_exists($FileToFind))
	{ CacheImage::DeleteImage($CacheImage, $CurrentUser); }
}

$infoSuccess = new Info($lang->g('MessageCacheImagesCleaned'));
Info::AddInfo($infoSuccess);

HTMLstuff::RefererRedirect();

?>