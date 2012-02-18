<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();


$ImageID = null;
$VideoID = null;
$SetID = null;
$ModelIndexID = null;
$ModelID = null;
$Width = null;
$Height = null;
$WhereClause = null;


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

if(array_key_exists('width', $_GET) && isset($_GET['width']) && is_numeric($_GET['width']))
{ $Width = (int)$_GET['width']; }

if(array_key_exists('height', $_GET) && isset($_GET['height']) && is_numeric($_GET['height']))
{ $Height = (int)$_GET['height']; }


if(isset($ImageID))
{
	$WhereClause = sprintf('image_id = %1$d', $ImageID);
}
else if(isset($VideoID))
{
	$WhereClause = sprintf('video_id = %1$d', $VideoID);
}
else if(isset($SetID))
{
	$WhereClause = sprintf('set_id = %1$d', $SetID);
}
else if(isset($ModelIndexID))
{
	$WhereClause = sprintf('index_id = %1$d', $ModelIndexID);
}
else if(isset($ModelID))
{
	$WhereClause = sprintf('model_id = %1$d', $ModelID);
}

if(!is_null($WhereClause) && isset($Width))
{
	$WhereClause .= sprintf(' AND cache_imagewidth = %1$d', $Width);
}

if(!is_null($WhereClause) && isset($Height))
{
	$WhereClause .= sprintf(' AND cache_imageheight = %1$d', $Height);
}

if(!is_null($WhereClause))
{
	$CacheImage = CacheImage::GetCacheImages($WhereClause);
}

if(!is_null($CacheImage))
{
	foreach($CacheImage as $CI)
	{ CacheImage::DeleteImage($CI, $CurrentUser); }
}

HTMLstuff::RefererRedirect();

?>