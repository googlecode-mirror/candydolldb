<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();

$ImageID = null;
$VideoID = null;
$SetID = null;
$ModelID = null;
$RandomPic = false;


if(array_key_exists('image_id', $_GET) && isset($_GET['image_id']) && is_numeric($_GET['image_id']))
{ $ImageID = (int)$_GET['image_id']; }

if(array_key_exists('video_id', $_GET) && isset($_GET['video_id']) && is_numeric($_GET['video_id']))
{ $VideoID = (int)$_GET['video_id']; }

if(array_key_exists('set_id', $_GET) && isset($_GET['set_id']) && is_numeric($_GET['set_id']))
{ $SetID = (int)$_GET['set_id']; }

if(array_key_exists('model_id', $_GET) && isset($_GET['model_id']) && is_numeric($_GET['model_id']))
{ $ModelID = (int)$_GET['model_id']; }

$RandomPic = $ModelID && array_key_exists('random_pic', $_GET) && isset($_GET['random_pic']) && $_GET['random_pic'] == 'true';
$PortraitOnly = array_key_exists('portrait_only', $_GET) && isset($_GET['portrait_only']) && $_GET['portrait_only'] == 'true';
$LandscapeOnly = array_key_exists('landscape_only', $_GET) && isset($_GET['landscape_only']) && $_GET['landscape_only'] == 'true';


/* @var $Image Image */
/* @var $Set Set */
/* @var $Model Model */

if($ModelID)
{
	$Model = Model::GetModels(sprintf('model_id = %1$d AND mut_deleted = -1', $ModelID));
	
	if($Model)
	{
		$Model = $Model[0];
		
		if($RandomPic)
		{
			Image::OutputImage(
				$Model->GetFileFromDisk($PortraitOnly, $LandscapeOnly),
				800,
				600,
				false
			);
		}
		
		$filename = sprintf('%1$s/%2$s/%3$s.jpg',
			CANDYIMAGEPATH,
			CANDYINDEXPATH,
			$Model->GetShortName()
		);
		
		if(file_exists($filename))
		{
			Image::OutputImage($filename, 1024, 768);
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
else if($SetID)
{
	$Set = Set::GetSets(sprintf('set_id = %1$d AND mut_deleted = -1', $SetID));
	if($Set)
	{
		$Set = $Set[0];

		Image::OutputImage(
			$Set->getModel()->GetFileFromDisk(
				$PortraitOnly,
				$LandscapeOnly,
				sprintf('%1$s%2$s', $Set->getPrefix(), $Set->getName())
			),
			800,
			600,
			false
		);
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
		
		$filename = sprintf('%1$s/%2$s/%3$s%4$s/%5$s.%6$s',
			CANDYIMAGEPATH,
			$Model->GetFullName(),
			$Set->getPrefix(),
			$Set->getName(),
			$Image->getFileName(),
			$Image->getFileExtension()
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
else
{
	HTMLstuff::RefererRedirect();
}

?>