<?php

include('cd.php');
ini_set('max_execution_time', '3600');
$CurrentUser = Authentication::Authenticate();

$ImageID = Utils::SafeIntFromQS('image_id');
$VideoID = Utils::SafeIntFromQS('video_id');
$SetID = Utils::SafeIntFromQS('set_id');
$ModelIndexID = Utils::SafeIntFromQS('index_id');
$ModelID = Utils::SafeIntFromQS('model_id');
$Width = Utils::SafeIntFromQS('width');
$Height = Utils::SafeIntFromQS('height');;
$PromptDownload = Utils::SafeBoolFromQS('download');
$PortraitOnly = Utils::SafeBoolFromQS('portrait_only');
$LandscapeOnly = Utils::SafeBoolFromQS('landscape_only');

$CacheImage = null;

/* @var $CacheImage CacheImage */
/* @var $Video Video */
/* @var $Image Image */
/* @var $Set Set */
/* @var $Model Model */

if($ModelIndexID)
{
	$CacheImage = CacheImage::GetCacheImages(new CacheImageSearchParameters(
		null, null,
		$ModelIndexID, null,
		null, null,
		null, null,
		null, null,
		null, null,
		$Width, $Height
	));
	
	if($CacheImage)
	{
		$CacheImage = $CacheImage[0];
		$Model = Model::GetModels(new ModelSearchParameters($ModelIndexID));
		$Model = $Model[0];
		
		Image::OutputImage(
			$CacheImage->getFilenameOnDisk(),
			$CacheImage->getImageWidth(),
			$CacheImage->getImageHeight(),
			true,
			null,
			$PromptDownload ? sprintf('%1$s.jpg', $Model->GetFullName()) : null
		);
	}
	else
	{
		header(sprintf(
			'location:download_index.php?model_id=%1$d&width=%2$d&height=%3$d&download=%4$s',
			$ModelIndexID,
			$Width,
			$Height,
			$PromptDownload ? 'true':'false'
		));
	}
}
else if($ModelID)
{
	$CacheImage = CacheImage::GetCacheImages(new CacheImageSearchParameters(
		null, null,
		null, null,
		$ModelID, null,
		null, null,
		null, null,
		null, null,
		$Width, $Height
	));
	
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
		$Model = Model::GetModels(new ModelSearchParameters($ModelID));
		if($Model)
		{
			$Model = $Model[0];
			
			$imagefileondisk = $Model->GetFileFromDisk(
				$PortraitOnly,
				$LandscapeOnly
			);
			
			if($imagefileondisk)
			{
				$CacheImage = new CacheImage();
					
				$CacheImage->setModelID($ModelID);
				$CacheImage->setKind(CACHEIMAGE_KIND_MODEL);
				$CacheImage->setImageWidth($Width);
				$CacheImage->setImageHeight($Height);
					
				CacheImage::Insert($CacheImage, $CurrentUser);
			}
			
			Image::OutputImage(
				$imagefileondisk,			
				$Width,
				$Height,
				true,
				($imagefileondisk ? $CacheImage->getFilenameOnDisk() : null)
			);
		}
		else
		{
			Image::OutputImage();
		}
	}
}
else if($SetID)
{
	$CacheImage = CacheImage::GetCacheImages(new CacheImageSearchParameters(
		null, null,
		null, null,
		null, null,
		$SetID, null,
		null, null,
		null, null,
		$Width, $Height
	));
	
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
		$Set = Set::GetSets(new SetSearchParameters($SetID));
		if($Set)
		{
			$Set = $Set[0];
			
			if($Set->getModel()->getFirstName() == 'VIP' && preg_match('/^[0-9]{2,3}$/i', $Set->getName()))
			{ Image::OutputImage(); }
			
			$imagefileondisk = 	$Set->getModel()->GetFileFromDisk(
				$PortraitOnly,
				$LandscapeOnly,
				$Set->getID()
			);

			if($imagefileondisk)
			{
				$CacheImage = new CacheImage();
				
				$CacheImage->setSetID($SetID);
				$CacheImage->setKind(CACHEIMAGE_KIND_SET);
				$CacheImage->setImageWidth($Width);
				$CacheImage->setImageHeight($Height);
				
				CacheImage::Insert($CacheImage, $CurrentUser);
			}

			Image::OutputImage(
				$imagefileondisk,
				$Width,
				$Height,
				true,
				($imagefileondisk ? $CacheImage->getFilenameOnDisk() : null)
			);
		}
	}
}
else if($VideoID)
{
	$CacheImage = CacheImage::GetCacheImages(new CacheImageSearchParameters(
		null, null,
		null, null,
		null, null,
		null, null,
		null, null,
		$VideoID, null,
		$Width, $Height
	));
	
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
		$Video = Video::GetVideos(new VideoSearchParameters($VideoID));
		
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
				$CacheImage = new CacheImage();
					
				$CacheImage->setVideoID($VideoID);
				$CacheImage->setKind(CACHEIMAGE_KIND_VIDEO);
				$CacheImage->setImageWidth($Width);
				$CacheImage->setImageHeight($Height);
					
				CacheImage::Insert($CacheImage, $CurrentUser);
				
				Image::OutputImage(
					$filename,
					800,
					600,
					true,
					$CacheImage->getFilenameOnDisk()
				);
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
}
else if($ImageID)
{                                                                                            
	$CacheImage = CacheImage::GetCacheImages(new CacheImageSearchParameters(
		null, null,
		null, null,
		null, null,
		null, null,
		$ImageID, null,
		null, null,
		$Width, $Height
	));
	
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
		$Image = Image::GetImages(new ImageSearchParameters($ImageID));
	        
		if($Image)
		{
			$Image = $Image[0];
			$Set = $Image->getSet();
			$Model = $Set->getModel();
	
			if(file_exists($Image->getFilenameOnDisk()))
			{
				$CacheImage = new CacheImage();
					
				$CacheImage->setImageID($ImageID);
				$CacheImage->setKind(CACHEIMAGE_KIND_IMAGE);
				$CacheImage->setImageWidth($Width);
				$CacheImage->setImageHeight($Height);
					
				CacheImage::Insert($CacheImage, $CurrentUser);
				
				Image::OutputImage(
					$Image->getFilenameOnDisk(),
					$Width,
					$Height,
					true,
					$CacheImage->getFilenameOnDisk()
				);
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
}
else
{
	HTMLstuff::RefererRedirect();
}

?>