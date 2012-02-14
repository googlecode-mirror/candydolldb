<?php

include('cd.php');
ini_set('max_execution_time', '3600');
$CurrentUser = Authentication::Authenticate();


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
	
	if(!preg_match('/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/i', $idToFind))
	{ continue; }
	
	$CacheImageInDB = CacheImage::FilterCacheImages($CacheImages, null, null, null, null, null, null, $idToFind);
	
	if(!$CacheImageInDB)
	{ unlink($file->getRealPath()); }	
}

HTMLstuff::RefererRedirect();

?>