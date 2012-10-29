<?php
/* This file is part of CandyDollDB.

CandyDollDB is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

CandyDollDB is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with CandyDollDB. If not, see <http://www.gnu.org/licenses/>.
*/

include('cd.php');
ini_set('max_execution_time', '3600');
$CurrentUser = Authentication::Authenticate();

if(!$CurrentUser->hasPermission(RIGHT_EXPORT_ZIP))
{
	$e = new Error(RIGHTS_ERR_USERNOTALLOWED);
	Error::AddError($e);
	HTMLstuff::RefererRedirect();
}

$ModelID = Utils::SafeIntFromQS('model_id');
$SetID = Utils::SafeIntFromQS('set_id');
$ImageID = Utils::SafeIntFromQS('image_id');
$UseSubfolders = Utils::SafeBoolFromQS('usesub');
$ImageIDs = array();

if(array_key_exists('image_ids', $_GET) && isset($_GET['image_ids']))
{ $ImageIDs = Utils::SafeInts(explode(',', $_GET['image_ids'])); }

$tmpFile = sprintf('%1$s/%2$s.zip', sys_get_temp_dir(), Utils::UUID());
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
if($resource === TRUE)
{
	ini_set('max_execution_time', '3600');
	$zip->setArchiveComment('Downloaded from CandyDoll DB'."\nhttps://code.google.com/p/candydolldb/");

	if($ImageID)
	{
		$Image = Image::GetImages(new ImageSearchParameters($ImageID));

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
				
				$finalFile = $Image->getFileName().'.zip';
			}
		}
	}
	else if($SetID)
	{
		$Images = Image::GetImages(new ImageSearchParameters(FALSE, FALSE, $SetID));
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
		$Sets = Set::GetSets(new SetSearchParameters(FALSE, FALSE, $ModelID));
		$Images = Image::GetImages(new ImageSearchParameters(FALSE, FALSE, FALSE, FALSE, $ModelID));

		if($Sets && $Images)
		{
			$MainImage = $Images[0];
			$Model = $MainImage->getSet()->getModel();

			foreach($Sets as $Set)
			{
				$zip->addEmptyDir(sprintf('%1$s/%2$s%3$s', $Model->GetFullName(), $Set->getPrefix(), $Set->getName()));
				
				foreach(Image::Filter($Images, NULL, $Set->getID()) as $Image)
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
		$Images = Image::GetImages(new ImageSearchParameters(FALSE, $ImageIDs));
		
		foreach($Images as $Image)
		{
			if(!file_exists($Image->getFilenameOnDisk()))
			{ continue; }
		
			$zip->addFile(
				$Image->getFilenameOnDisk(),
				sprintf(
					$UseSubfolders ? '%1$s/%2$s%3$s/%4$s.%5$s' : '%4$s.%5$s',
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
	header(sprintf('Content-Type: %1$s', Utils::GetMime('zip')));
	header('Content-Disposition: attachment; filename="'.$finalFile.'"');
	header('Content-Transfer-Encoding: binary');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	header('Content-Length: '.filesize($tmpFile));
	@ob_clean();
	
	$f = fopen($tmpFile, "r");
	if($f !== FALSE)
	{
		while($chunk = fread($f, 16777216))
		{ echo $chunk; }
		
		fclose($f);
	}

	unlink($tmpFile);
	exit;
}
else
{
	HTMLstuff::RefererRedirect();
}

?>