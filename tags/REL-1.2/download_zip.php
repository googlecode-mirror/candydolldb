<?php
/*	This file is part of CandyDollDB.

    CandyDollDB is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    CandyDollDB is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with CandyDollDB.  If not, see <http://www.gnu.org/licenses/>.
*/

include('cd.php');
$CurrentUser = Authentication::Authenticate();


$ModelID = null;
$SetID = null;
$ImageID = null;


if(array_key_exists('model_id', $_GET) && isset($_GET['model_id']) && is_numeric($_GET['model_id']))
{ $ModelID = (int)$_GET['model_id']; }

if(array_key_exists('set_id', $_GET) && isset($_GET['set_id']) && is_numeric($_GET['set_id']))
{ $SetID = (int)$_GET['set_id']; }

if(array_key_exists('image_id', $_GET) && isset($_GET['image_id']) && is_numeric($_GET['image_id']))
{ $ImageID = (int)$_GET['image_id']; }


$tmpFile = sprintf('/tmp/%1$s.zip', Utils::GUID());
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
	$zip->setArchiveComment('Downloaded from CandyDoll DB');

	if($ImageID)
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
				$zip->addFile(
					$filename,
					sprintf('%1$s.%2$s',
						$Image->getFileName(),
						$Image->getFileExtension()
					)
				);
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
				$filename = sprintf('%1$s/%2$s/%3$s%4$s/%5$s.%6$s',
					CANDYIMAGEPATH,
					$Model->GetFullName(),
					$Set->getPrefix(),
					$Set->getName(),
					$Image->getFileName(),
					$Image->getFileExtension()
				);

				if(!file_exists($filename)) { continue; }

				$zip->addFile(
					$filename,
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
					$filename = sprintf('%1$s/%2$s/%3$s%4$s/%5$s.%6$s',
						CANDYIMAGEPATH,
						$Model->GetFullName(),
						$Set->getPrefix(),
						$Set->getName(),
						$Image->getFileName(),
						$Image->getFileExtension()
					);

					if(!file_exists($filename)) { continue; }

					$zip->addFile(
						$filename,
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
		}
	}

	$zip->close();
}

if(file_exists($tmpFile))
{
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename=CandyDollDB.zip');
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