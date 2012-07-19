<?php

include('cd.php');
ini_set('max_execution_time', '3600');
$CurrentUser = Authentication::Authenticate();
$FileToFind = '';

$CacheFolder = null;
$CacheImages = CacheImage::GetCacheImages();
$CacheImagesToFilter = array();

$CacheImagesModel = CacheImage::FilterCacheImages($CacheImages, CACHEIMAGE_KIND_MODEL, null, null, null, null, null, null);
$CacheImagesIndex = CacheImage::FilterCacheImages($CacheImages, CACHEIMAGE_KIND_INDEX, null, null, null, null, null, null);
$CacheImagesSet = CacheImage::FilterCacheImages($CacheImages, CACHEIMAGE_KIND_SET, null, null, null, null, null, null);
$CacheImagesImage = CacheImage::FilterCacheImages($CacheImages, CACHEIMAGE_KIND_IMAGE, null, null, null, null, null, null);
$CacheImagesVideo = CacheImage::FilterCacheImages($CacheImages, CACHEIMAGE_KIND_VIDEO, null, null, null, null, null, null);


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

	if(preg_match_all('/(?<Prefix>[MXSIV]-)?[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/i', $idToFind, $matches) > 0)
	{ 
		$CacheImagesToFilter = $CacheImages;
		
		switch($matches['Prefix']){
			case 'M-': $CacheImagesToFilter = $CacheImagesModel; break;
			case 'X-': $CacheImagesToFilter = $CacheImagesIndex; break;
			case 'S-': $CacheImagesToFilter = $CacheImagesSet; break;
			case 'I-': $CacheImagesToFilter = $CacheImagesImage; break;
			case 'V-': $CacheImagesToFilter = $CacheImagesVideo; break;
		}

		$CacheImageInDB = CacheImage::FilterCacheImages(
			$CacheImagesToFilter,
			null,
			null,
			null,
			null,
			null,
			null,
			str_ireplace($matches['Prefix'], '', $idToFind)
		);
	
		if(!$CacheImageInDB)
		{ unlink($file->getRealPath()); }
	}
}

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