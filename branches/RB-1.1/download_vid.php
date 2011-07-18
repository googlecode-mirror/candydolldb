<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();


$ModelID = null;
$SetID = null;
$VideoID = null;


if(array_key_exists('model_id', $_GET) && isset($_GET['model_id']) && is_numeric($_GET['model_id']))
{ $ModelID = (int)$_GET['model_id']; }

if(array_key_exists('set_id', $_GET) && isset($_GET['set_id']) && is_numeric($_GET['set_id']))
{ $SetID = (int)$_GET['set_id']; }

if(array_key_exists('video_id', $_GET) && isset($_GET['video_id']) && is_numeric($_GET['video_id']))
{ $VideoID = (int)$_GET['video_id']; }


/* @var $Video Video */
/* @var $MainVideo Video */
/* @var $Set Set */
/* @var $Model Model */
if($VideoID)
{
	$Video = Video::GetVideos(sprintf('video_id = %1$d AND mut_deleted = -1', $VideoID));

	if($Video)
	{
		$Video = $Video[0];
		$Set = $Video->getSet();
		$Model = $Set->getModel();
		
		$filename = sprintf('%1$s/%2$s/%3$s.%4$s',
			CANDYVIDEOPATH,
			$Model->GetFullName(),
			$Video->getFileName(),
			$Video->getFileExtension()
		);	

		if(file_exists($filename))
		{
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header(sprintf('Content-Disposition: attachment; filename=%1$s', basename($filename)));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header(sprintf('Content-Length: %1$d', $Video->getFileSize()));
			@ob_clean();
			flush();
			readfile($filename);
			exit;
		}
		else
		{
			HTMLstuff::RefererRedirect();
		}
	}
	else
	{
		HTMLstuff::RefererRedirect();
	}
}
else if($SetID)
{
	$Videos = Video::GetVideos(sprintf('set_id = %1$d AND mut_deleted = -1', $SetID));
	if($Videos)
	{
		$MainVideo = $Videos[0];
		$ReturnURL = sprintf('%1$s?video_id=%2$d', basename($_SERVER['PHP_SELF']), $MainVideo->getID()); 
		header('location:'.$ReturnURL);
	}
	else
	{
		HTMLstuff::RefererRedirect();
	}
}
else
{
	HTMLstuff::RefererRedirect();
}

?>