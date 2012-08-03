<?php

include('cd.php');
ini_set('max_execution_time', '3600');
$CurrentUser = Authentication::Authenticate();


$ModelID = Utils::SafeIntFromQS('model_id');
$SetID = Utils::SafeIntFromQS('set_id');
$VideoID = Utils::SafeIntFromQS('video_id');


/* @var $Video Video */
/* @var $MainVideo Video */
/* @var $Set Set */
/* @var $Model Model */
if($VideoID)
{
	$Video = Video::GetVideos(new VideoSearchParameters($VideoID));

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
			header(sprintf('Content-Disposition: attachment; filename="%1$s"', basename($filename)));
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
	$Videos = Video::GetVideos(new VideoSearchParameters(FALSE, FALSE, $SetID));
	if($Videos)
	{
		$MainVideo = $Videos[0];
		$ReturnURL = sprintf('%1$s?video_id=%2$d', basename($_SERVER['PHP_SELF']), $MainVideo->getID()); 
		header('location:'.$ReturnURL);
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

?>