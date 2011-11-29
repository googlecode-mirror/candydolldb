<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();


$ModelID = null;
$SetID = null;
$ImageID = null;
$ImageIDs = array();


if(array_key_exists('model_id', $_GET) && isset($_GET['model_id']) && is_numeric($_GET['model_id']))
{ $ModelID = (int)$_GET['model_id']; }

if(array_key_exists('set_id', $_GET) && isset($_GET['set_id']) && is_numeric($_GET['set_id']))
{ $SetID = (int)$_GET['set_id']; }

if(array_key_exists('image_id', $_GET) && isset($_GET['image_id']) && is_numeric($_GET['image_id']))
{ $ImageID = (int)$_GET['image_id']; }

if(array_key_exists('image_ids', $_GET) && isset($_GET['image_ids']))
{ $ImageIDs = Utils::SafeInts(explode(',', $_GET['image_ids'])); }


$tmpFile = sprintf('%1$s/%2$s.zip', sys_get_temp_dir(), Utils::GUID());
$finalFile = 'CandyDollDB.zip';
$zip = new ZipArchive();

if(file_exists($tmpFile))
{ $resource = $zip->open($tmpFile, ZipArchive::OVERWRITE); }
else
{ $resource = $zip->open($tmpFile, ZipArchive::CREATE); }


/* @var $Image Image */
/* @var $MainImage Image */
/* @var $Set Set */
/* @var $Model Model */
if($resource === true)
{
	ini_set('max_execution_time', '300');
	$zip->setArchiveComment('Downloaded from CandyDoll DB'."\nhttps://code.google.com/p/candydolldb/");

	if($ImageID)
	{
		$Image = Image::GetImages(sprintf('image_id = %1$d AND mut_deleted = -1', $ImageID));

		if($Image)
		{
			$Image = $Image[0];
			$Set = $Image->getSet();
			$Model = $Set->getModel();

			if(file_exists($Image->getFilenameOnDisk()))
			{
				$zip->addFile(
					$Image->getFilenameOnDisk(),
					sprintf('%1$s.%2$s',
						$Image->getFileName(),
						$Image->getFileExtension()
					)
				);
				
				$finalFile =  $Image->getFileName().'.zip';
			}
		}
	}
	else if($SetID)
	{
		$Images = Image::GetImages(sprintf('set_id = %1$d AND mut_deleted = -1', $SetID));
		if($Images)
		{
			$MainImage = $Images[0];
			$Set = $MainImage->getSet();
			$Model = $Set->getModel();
				
			$zip->addEmptyDir(sprintf('%1$s/%2$s%3$s', $Model->GetFullName(), $Set->getPrefix(), $Set->getName()));
				
			foreach($Images as $Image)
			{
				if(!file_exists($Image->getFilenameOnDisk()))
				{ continue; }

				$zip->addFile(
					$Image->getFilenameOnDisk(),
					sprintf('%1$s/%2$s%3$s/%4$s.%5$s',
						$Model->GetFullName(),
						$Set->getPrefix(),
						$Set->getName(),
						$Image->getFileName(),
						$Image->getFileExtension()
					)
				);
				
				
			}

			$finalFile = sprintf('%1$s%2$s.zip',
				$Model->GetShortName(),
				$Set->getName()
			);
		}
	}
	else if($ModelID)
	{
		$Sets = Set::GetSets(sprintf('model_id = %1$d AND mut_deleted = -1', $ModelID));
		$Images = Image::GetImages(sprintf('model_id = %1$d AND mut_deleted = -1', $ModelID));

		if($Sets && $Images)
		{
			$MainImage = $Images[0];
			$Model = $MainImage->getSet()->getModel();

			foreach($Sets as $Set)
			{
				$zip->addEmptyDir(sprintf('%1$s/%2$s%3$s', $Model->GetFullName(), $Set->getPrefix(), $Set->getName()));
				
				foreach(Image::FilterImages($Images, null, $Set->getID()) as $Image)
				{
					if(!file_exists($Image->getFilenameOnDisk()))
					{ continue; }

					$zip->addFile(
						$Image->getFilenameOnDisk(),
						sprintf('%1$s/%2$s%3$s/%4$s.%5$s',
							$Model->GetFullName(),
							$Set->getPrefix(),
							$Set->getName(),
							$Image->getFileName(),
							$Image->getFileExtension()
						)
					);
				}
			}
			$finalFile = sprintf('%1$s.zip', $Model->GetFullName());
		}
	}
	else if($ImageIDs)
	{
		$Images = Image::GetImages(sprintf('mut_deleted = -1 AND image_id IN ( %1$s )', join(',', $ImageIDs)));
		
		foreach($Images as $Image)
		{
			if(!file_exists($Image->getFilenameOnDisk()))
			{ continue; }
		
			$zip->addFile(
				$Image->getFilenameOnDisk(),
				sprintf('%1$s/%2$s%3$s/%4$s.%5$s',
					$Image->getSet()->getModel()->GetFullName(),
					$Image->getSet()->getPrefix(),
					$Image->getSet()->getName(),
					$Image->getFileName(),
					$Image->getFileExtension()
				)
			);
		}
	}

	$zip->close();
}

if(file_exists($tmpFile))
{
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename='.$finalFile);
	header('Content-Transfer-Encoding: binary');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	header('Content-Length: '.filesize($tmpFile));
	@ob_clean();
	flush();
	readfile($tmpFile);
	unlink($tmpFile);
	exit;
}
else
{
	HTMLstuff::RefererRedirect();
}

?>