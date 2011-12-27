<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();

$ImageID = null;
$VideoID = null;
$SetID = null;
$ModelIndexID = null;
$ModelID = null;
$RandomPic = false;
$CacheImage = null;
$Width = null;
$Height = null;

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
{ $Width = abs((int)$_GET['width']); }

if(array_key_exists('height', $_GET) && isset($_GET['height']) && is_numeric($_GET['height']))
{ $Height = abs((int)$_GET['height']); }

$RandomPic = $ModelID && array_key_exists('random_pic', $_GET) && isset($_GET['random_pic']) && $_GET['random_pic'] == 'true';
$PortraitOnly = array_key_exists('portrait_only', $_GET) && isset($_GET['portrait_only']) && $_GET['portrait_only'] == 'true';
$LandscapeOnly = array_key_exists('landscape_only', $_GET) && isset($_GET['landscape_only']) && $_GET['landscape_only'] == 'true';

/* @var $CacheImage CacheImage */
/* @var $Video Video */
/* @var $Image Image */
/* @var $Set Set */
/* @var $Model Model */

if($ModelIndexID)
{
	//$WhereClause = sprintf('index_id = %1$d', $ModelIndexID);
	//$CacheImage = CacheImage::GetCacheImages($WhereClause, null, null);
	
}
else if($ModelID)
{
	$CacheImage = CacheImage::GetCacheImages(
		sprintf('model_id = %1$d AND cache_imagewidth = %2$d AND cache_imageheight = %3$d',
			$ModelID,
			$Width,
			$Height
		)
	);
	
	if($CacheImage)
	{
		$CacheImage = $CacheImage[0];
		Image::OutputImage(
			$CacheImage->getFilenameOnDisk(),
			$CacheImage->getImageWidth(),
			$CacheImage->getImageHeight(),
			true
		);
	}
	else
	{
		$Model = Model::GetModels(sprintf('model_id = %1$d AND mut_deleted = -1', $ModelID));
		if($Model)
		{
			$Model = $Model[0];
			
			$CacheImage = new CacheImage();
				
			$CacheImage->setModelID($ModelID);
			$CacheImage->setKind(CACHEIMAGE_KIND_MODEL);
			$CacheImage->setImageWidth($Width);
			$CacheImage->setImageHeight($Height);
				
			CacheImage::InsertCacheImage($CacheImage, $CurrentUser);
				
			Image::OutputImage(
				$Model->GetFileFromDisk(
					$PortraitOnly,
					$LandscapeOnly
				),
				$Width,
				$Height,
				true,
				$CacheImage->getFilenameOnDisk()
			);
			
			//header('location:download_index.php?model_id='.$ModelID.'&width=400&height=600');
		}
		else
		{
			Image::OutputImage();
		}
	}
}
else if($SetID)
{
	$CacheImage = CacheImage::GetCacheImages(
		sprintf('set_id = %1$d AND cache_imagewidth = %2$d AND cache_imageheight = %3$d',
		$SetID,
		$Width,
		$Height)
	);
	
	if($CacheImage)
	{
		$CacheImage = $CacheImage[0];
		Image::OutputImage(
			$CacheImage->getFilenameOnDisk(),
			$CacheImage->getImageWidth(),
			$CacheImage->getImageHeight(),
			true
		);
	}
	else
	{
		$Set = Set::GetSets(sprintf('set_id = %1$d AND mut_deleted = -1', $SetID));
		if($Set)
		{
			$Set = $Set[0];
			
			$CacheImage = new CacheImage();
			
			$CacheImage->setSetID($SetID);
			$CacheImage->setKind(CACHEIMAGE_KIND_SET);
			$CacheImage->setImageWidth($Width);
			$CacheImage->setImageHeight($Height);
			
			CacheImage::InsertCacheImage($CacheImage, $CurrentUser);
	
			Image::OutputImage(
				$Set->getModel()->GetFileFromDisk(
					$PortraitOnly,
					$LandscapeOnly,
					sprintf('%1$s%2$s', $Set->getPrefix(), $Set->getName())
				),
				$Width,
				$Height,
				true,
				$CacheImage->getFilenameOnDisk()
			);
		}
	}
}
else if($VideoID)
{
	$Video = Video::GetVideos(sprintf('video_id = %1$d AND mut_deleted = -1', $VideoID));
	
	if($Video)
	{
		$Video = $Video[0];
		
		$filename = sprintf('%1$s/%2$s/%3$s.jpg',
			CANDYIMAGEPATH,
			CANDYVIDEOTHUMBPATH,
			$Video->getFileName()
		);
		
		if(file_exists($filename))
		{
			Image::OutputImage($filename, 800, 600);
		}
		else
		{
			Image::OutputImage();
		}
	}
	else
	{
		Image::OutputImage();
	}
}
else if($ImageID)
{                                                                                            
	$Image = Image::GetImages(sprintf('image_id = %1$d AND mut_deleted = -1', $ImageID));
        
	if($Image)
	{
		$Image = $Image[0];
		$Set = $Image->getSet();
		$Model = $Set->getModel();

		if(file_exists($Image->getFilenameOnDisk()))
		{
			Image::OutputImage($Image->getFilenameOnDisk(), 800, 600);
		}
		else
		{
			Image::OutputImage();
		}
	}
	else
	{
		Image::OutputImage();
	}
}
else
{
	HTMLstuff::RefererRedirect();
}

?>