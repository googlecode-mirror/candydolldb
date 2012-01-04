<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();


$ImageID = null;
$VideoID = null;
$SetID = null;
$ModelIndexID = null;
$ModelID = null;

/* @var $CacheImage CacheImage */
$CacheImage = null;


if(array_key_exists('image_id', $_GET) && isset($_GET['image_id']) && is_numeric($_GET['image_id']))
{ $ImageID = (int)$_GET['image_id']; }

if(array_key_exists('video_id', $_GET) && isset($_GET['video_id']) && is_numeric($_GET['video_id']))
{ $VideoID = (int)$_GET['video_id']; }

if(array_key_exists('set_id', $_GET) && isset($_GET['set_id']) && is_numeric($_GET['set_id']))
{ $SetID = (int)$_GET['set_id']; }

if(array_key_exists('index_id', $_GET) && isset($_GET['index_id']) && is_numeric($_GET['index_id']))
{ $ModelIndexID = (int)$_GET['index_id']; }

if(array_key_exists('model_id', $_GET) && isset($_GET['model_id']) && is_numeric($_GET['model_id']))
{ $ModelID = (int)$_GET['model_id']; }


if(isset($ImageID))
{
	$CacheImage = CacheImage::GetCacheImages(sprintf('image_id = %1$d', $ImageID));
}
else if(isset($VideoID))
{
	$CacheImage = CacheImage::GetCacheImages(sprintf('video_id = %1$d', $VideoID));
}
else if(isset($SetID))
{
	$CacheImage = CacheImage::GetCacheImages(sprintf('set_id = %1$d', $SetID));
}
else if(isset($ModelIndexID))
{
	$CacheImage = CacheImage::GetCacheImages(sprintf('index_id = %1$d', $ModelIndexID));
}
else if(isset($ModelID))
{
	$CacheImage = CacheImage::GetCacheImages(sprintf('model_id = %1$d', $ModelID));
}

if(!is_null($CacheImage))
{
	foreach($CacheImage as $CI)
	{ CacheImage::DeleteImage($CI, $CurrentUser); }
}

HTMLstuff::RefererRedirect();

?>