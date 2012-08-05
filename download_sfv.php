<?php

include('cd.php');
ini_set('max_execution_time', '3600');
ob_start();
$CurrentUser = Authentication::Authenticate();

$ModelID = Utils::SafeIntFromQS('model_id');
$includepath = Utils::SafeIntFromQS('includepath');
$includepath = in_array($includepath, array(SFV_PATH_OPTION_NONE, SFV_PATH_OPTION_RELATIVE, SFV_PATH_OPTION_FULL)) ? $includepath : SFV_PATH_OPTION_NONE; 

$Models = Model::GetModels(new ModelSearchParameters(
	is_null($ModelID) ? FALSE : $ModelID));
$Sets = Set::GetSets(new SetSearchParameters(
	FALSE,
	FALSE,
	is_null($ModelID) ? FALSE : $ModelID));
$Dates = Date::GetDates();

$SFVHeader = <<<HfjdUd8Bfhsdb8389BudfhnJUfsklsfsfw
; Generated by CandyDollDB v%1\$s on %2\$s at %3\$s
; Project website: https://code.google.com/p/candydolldb/
;

HfjdUd8Bfhsdb8389BudfhnJUfsklsfsfw;

$outfile = 'CandyDollDB.sfv';
if($ModelID && count($Models) > 0){
	$Model = $Models[0];
	$outfile = sprintf('CandyDollDB %1$s.sfv', $Model->GetFullName());
}

header('Content-Type: text/x-sfv');
header(sprintf('Content-Disposition: attachment; filename="%1$s"', $outfile));

printf($SFVHeader, CANDYDOLLDB_VERSION, date('Y-m-d'), date('H:i;s'));

/* @var $Model Model */
foreach ($Models as $Model)
{
	if($ModelID && $Model->getID() !== $ModelID)
	{ continue; }
	
	$SetsThisModel = Set::Filter($Sets, $Model->getID());
	if($SetsThisModel)
	{
		/* @var $Set Set */
		foreach ($SetsThisModel as $Set)
		{
			$ImagesThisSet = Image::GetImages(new ImageSearchParameters(FALSE, FALSE, $Set->getID(), FALSE, $Model->getID()));
			
			if($ImagesThisSet)
			{
				/* @var $Image Image */
				foreach($ImagesThisSet as $Image)
				{
					switch ($includepath)
					{
						case SFV_PATH_OPTION_NONE:
						default:
							printf('%1$s.%2$s %3$s%4$s',
								$Image->getFileName(),
								$Image->getFileExtension(),
								$Image->getFileCRC32(),
								PHP_EOL);
							break;
						
						case SFV_PATH_OPTION_RELATIVE:
							printf('%1$s%8$s%2$s%3$s%8$s%4$s.%5$s %6$s%7$s',
								$Model->GetFullName(),
								$Set->getPrefix(),
								$Set->getName(),
								$Image->getFileName(),
								$Image->getFileExtension(),
								$Image->getFileCRC32(),
								PHP_EOL,
								DIRECTORY_SEPARATOR);
							break;
							
						case SFV_PATH_OPTION_FULL:
							printf('%1$s %2$s%3$s',
								$Image->getFilenameOnDisk(),
								$Image->getFileCRC32(),
								PHP_EOL);
							break;
					}
				}
			}
			
			$VideosThisSet = Video::GetVideos(new VideoSearchParameters(FALSE, FALSE, $Set->getID(), FALSE, $Model->getID()));
			
			if($VideosThisSet)
			{
				/* @var $Video Video */
				foreach($VideosThisSet as $Video)
				{
					switch ($includepath)
					{
						case SFV_PATH_OPTION_NONE:
						default:
							printf('%1$s.%2$s %3$s%4$s',
								$Video->getFileName(),
								$Video->getFileExtension(),
								$Video->getFileCRC32(),
								PHP_EOL);
							break;
						
						case SFV_PATH_OPTION_RELATIVE:
							printf('%1$s%6$s%2$s.%3$s %4$s%5$s',
								$Model->GetFullName(),
								$Video->getFileName(),
								$Video->getFileExtension(),
								$Video->getFileCRC32(),
								PHP_EOL,
								DIRECTORY_SEPARATOR);
							break;
							
						case SFV_PATH_OPTION_FULL:
							printf('%1$s %2$s%3$s',
								$Video->getFilenameOnDisk(),
								$Video->getFileCRC32(),
								PHP_EOL);
							break;
					}
				}
			}
		}
	}
}

ob_end_flush();
flush();

exit;

?>