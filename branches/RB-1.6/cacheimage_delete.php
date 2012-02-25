<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();


$ImageID = Utils::SafeIntFromQS('image_id');
$VideoID = Utils::SafeIntFromQS('video_id');
$SetID = Utils::SafeIntFromQS('set_id');
$ModelIndexID = Utils::SafeIntFromQS('index_id');
$ModelID = Utils::SafeIntFromQS('model_id');
$Width = Utils::SafeIntFromQS('width');
$Height = Utils::SafeIntFromQS('height');;
$WhereClause = null;


/* @var $CacheImage CacheImage */
$CacheImage = null;


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