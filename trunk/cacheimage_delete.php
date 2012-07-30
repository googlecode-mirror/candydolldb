<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();

if($CurrentUser->hasPermission(RIGHT_CACHE_DELETE))
{
	$ImageID = Utils::SafeIntFromQS('image_id');
	$VideoID = Utils::SafeIntFromQS('video_id');
	$SetID = Utils::SafeIntFromQS('set_id');
	$ModelIndexID = Utils::SafeIntFromQS('index_id');
	$ModelID = Utils::SafeIntFromQS('model_id');
	$Width = Utils::SafeIntFromQS('width');
	$Height = Utils::SafeIntFromQS('height');;
	
	/* @var $CacheImage CacheImage */
	$CacheImage = null;

	$cisp = new CacheImageSearchParameters(
		null, null,
		$ModelIndexID, null,
		$ModelID, null,
		$SetID, null,
		$ImageID, null,
		$VideoID, null,
		$Width, $Height
	);
	
	if($cisp->getValues())
	{
		$CacheImage = CacheImage::GetCacheImages($cisp);
	}	
	
	if($CacheImage)
	{
		foreach($CacheImage as $CI)
		{
			CacheImage::DeleteImage($CI, $CurrentUser);
		}
	}
}
else
{
	$e = new Error(RIGHTS_ERR_USERNOTALLOWED);
	Error::AddError($e);
}

HTMLstuff::RefererRedirect();

?>