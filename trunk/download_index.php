<?php

include('cd.php');
ini_set('max_execution_time', '3600');
$CurrentUser = Authentication::Authenticate();

if(!$CurrentUser->hasPermission(RIGHT_EXPORT_INDEX))
{
	$e = new Error(RIGHTS_ERR_USERNOTALLOWED);
	Error::AddError($e);
	HTMLstuff::RefererRedirect();
}

$ModelID = Utils::SafeIntFromQS('model_id');
$IndexID = Utils::SafeIntFromQS('index_id');
$ModelID = isset($IndexID) ? $IndexID : $ModelID;

$finalWidth = Utils::SafeIntFromQS('width'); $finalWidth = $finalWidth ? $finalWidth : 1200;
$finalHeight = Utils::SafeIntFromQS('height'); $finalHeight = $finalHeight ? $finalHeight : 1800;
$perPage = Utils::SafeIntFromQS('perpage');
$promptDownload = Utils::SafeBoolFromQS('download');

$outputMode = EXPORT_OPTION_IMAGE;
if(array_key_exists('output', $_GET) && in_array($_GET['output'], array(EXPORT_OPTION_SERIALIZE, EXPORT_OPTION_IMAGE, EXPORT_OPTION_ZIP)))
{ $outputMode = intval($_GET['output']); }

$Images = Image::GetImages(new ImageSearchParameters(FALSE, FALSE, FALSE, FALSE, $ModelID));
$Sets = Set::GetSets(new SetSearchParameters(FALSE, FALSE, $ModelID));
$Model = $Sets ? $Sets[0]->getModel() : NULL;

if(!$Sets || in_array($Sets[0]->getModel()->getFullName(), array('Promotions', 'Interviews')))
{ Image::OutputImage(); }

$pageIterator = 1;
$perPage = $perPage && $perPage > 0 ? $perPage : count($Sets);
$uuid = Utils::UUID();
$cacheImages = array();

while( ($pageIterator - 1) * $perPage < count($Sets) )
{
	$Sets2Process = array_slice($Sets, ($pageIterator - 1) * $perPage, $perPage);
	
	$img = GenerateModelIndex($Sets2Process, $Images, $finalWidth, $finalHeight);
	
	if(is_null($img))
	{ $img = imagecreatefromjpeg($pathPrefix.'images/missing.jpg'); }
	
	$CacheImage = new CacheImage($uuid);
	$CacheImage->setModelIndexID($ModelID);
	$CacheImage->setKind(CACHEIMAGE_KIND_INDEX);
	$CacheImage->setImageWidth($finalWidth);
	$CacheImage->setImageHeight($finalHeight);
	$CacheImage->setSequenceNumber($pageIterator);
	$CacheImage->setSequenceTotal(ceil(count($Sets) / $perPage));
	CacheImage::Insert($CacheImage, $CurrentUser);
	
	imagejpeg($img, $CacheImage->getFilenameOnDisk() );
	imagedestroy($img);

	$cacheImages[] = $CacheImage;
	$pageIterator++;
}

if($outputMode == EXPORT_OPTION_IMAGE && count($cacheImages) == 1)
{
	$CacheImage = $cacheImages[0];
	
	Image::OutputImage(
		$CacheImage->getFilenameOnDisk(),
		$CacheImage->getImageWidth(),
		$CacheImage->getImageHeight(),
		TRUE,
		NULL,
		$PromptDownload ? sprintf('%1$s.jpg', $Model->GetFullName()) : NULL
	);
}

if($outputMode == EXPORT_OPTION_ZIP && count($cacheImages) > 0)
{
	$tmpFile = sprintf('%1$s/%2$s.zip', sys_get_temp_dir(), Utils::UUID());
	$finalFile = sprintf('Index %1$s.zip', $Model->GetFullName());
	$zip = new ZipArchive();
	
	if(file_exists($tmpFile))
	{ $resource = $zip->open($tmpFile, ZipArchive::OVERWRITE); }
	else
	{ $resource = $zip->open($tmpFile, ZipArchive::CREATE); }
	
	if($resource === TRUE)
	{
		ini_set('max_execution_time', '3600');
		$zip->setArchiveComment('Downloaded from CandyDoll DB'."\nhttps://code.google.com/p/candydolldb/");
	
		foreach($cacheImages as $CacheImage)
		{
			if(!file_exists($CacheImage->getFilenameOnDisk()))
			{ continue; }
		
			$zip->addFile(
				$CacheImage->getFilenameOnDisk(),
				str_replace(
					$CacheImage->getID(),
					$Model->GetFullName(),
					basename($CacheImage->getFilenameOnDisk())
				)
			);
		}
		$zip->close();
	}
	Utils::DownloadZip($tmpFile, $finalFile, TRUE);
}

if($outputMode == EXPORT_OPTION_SERIALIZE)
{
	// TODO Figure out a way of returning multiple CacheImages
	// Perhaps a JSON array with serialized images?
	// Or a string-array of on-disk filenames?
	var_dump($cacheImages);	
}



exit;

/**
 * Creates a dynamic index-image from the supplied sets
 * @param array(Set) $Sets
 * @return resource
 */
function GenerateModelIndex($Sets, $Images, $finalWidth = NULL, $finalHeight = NULL)
{
	$pathPrefix = (isset($argv) && $argc > 0) ? dirname($_SERVER['PHP_SELF']).'/' : '';
	$indexImage = imagecreatefrompng($pathPrefix.'images/index_background.png');
	$candyColor = imagecolorallocate($indexImage, 255, 246, 195);
	$font = $pathPrefix.'images/FreeSerifBoldItalic.ttf';
	$fontSizeTitle = 76;
	$fontSizeSubTitle = 30;
	
	$pics = array();

	$textTitle = $Sets[0]->getModel()->getFullName();
	$isVIP = $textTitle == 'VIP';
	$titleCoords = imagettfbbox($fontSizeTitle, 0, $font, $textTitle);
	$titleWidth = $titleCoords[2] - $titleCoords[0];
	imagettftext($indexImage, $fontSizeTitle, 0, (1160-$titleWidth), 90, $candyColor, $font, $textTitle);
	
	if(!$isVIP)
	{
		$textSubTitle = Set::RangeString($Sets);
		$subTitleCoords = imagettfbbox($fontSizeSubTitle, 0, $font, $textSubTitle);
		$subTitleWidth = $subTitleCoords[2] - $subTitleCoords[0];
		imagettftext($indexImage, $fontSizeSubTitle, 0, (1160-$subTitleWidth), 150, $candyColor, $font, $textSubTitle);
	}

	/* @var $Set Set */
	foreach ($Sets as $Set)
	{
		if($isVIP && is_numeric($Set->getName()))
		{ continue; }
	
		$ModelID = $Set->getModelID();
		$picCount = count($pics);
	
		/* @var $Image Image */
		foreach(Image::Filter($Images, $ModelID, $Set->getID()) as $Image)
		{
			if($Image->getImageWidth() > $Image->getImageHeight())
			{ continue; }
				
			if(!file_exists($Image->getFilenameOnDisk()))
			{ continue; }
	
			$pics[] = $Image;
			break;
		}
	
		if(count($pics) == $picCount)
		{
			$noImage = new Image();
			$noImage->setSet($Set);
			$noImage->setImageWidth(300);
			$noImage->setImageHeight(450);
			$pics[] = $noImage;
		}
	}
	
	$th = ThumbnailSettings::FromAmount(count($pics));
	
	if(!is_null($th))
	{
		// Row-loop
		for ($rowCount = 1; $rowCount <= $th->numberOfRows; $rowCount++)
		{
			// Column-loop
			for ($columnCount = 1; $columnCount <= $th->numberOfColumns; $columnCount++)
			{
				$thumbX = $th->startX + (($columnCount -1) * ($th->width + $th->marginX));
				$thumbY = $th->startY + (($rowCount -1) * ($th->height + $th->marginY));
	
				$picIndex = ($rowCount-1) * $th->numberOfColumns + $columnCount -1;
	
				if($picIndex > count($pics)-1)
				{ continue; }
	
				$Image = $pics[$picIndex];
	
				if($Image->getID())
				{ $srcImage = imagecreatefromjpeg($Image->getFilenameOnDisk() ); }
				else
				{ $srcImage = imagecreatefromjpeg($pathPrefix.'images/missing.jpg'); }
	
				if($srcImage)
				{
					imagecopyresampled(
						$indexImage,
						$srcImage,
						$thumbX,
						$thumbY,
						0,
						0,
						$th->width,
						$th->height,
						$Image->getImageWidth(),
						$Image->getImageHeight()
					);
	
					imagedestroy($srcImage);
			
					$textCaption = sprintf('%2$s%1$s', $Image->getSet()->getName(), $isVIP ? '' : 'Set ' );
					$captionCoords = imagettfbbox($th->fontSizeCaption, 0, $font, $textCaption);
					$captionWidth = $captionCoords[2] - $captionCoords[0];
	
					imagettftext(
						$indexImage,
						$th->fontSizeCaption,
						0,
						$thumbX + ($th->width/2) - ($captionWidth/2),
						$thumbY + $th->height + $th->captionMarginTop,
						$candyColor,
						$font,
						$textCaption
					);
				}
			}
		}
	}
	
	if(!is_null($finalWidth) && !is_null($finalHeight))
	{
		$oldIndexImage = $indexImage;
		$indexImage = imagecreatetruecolor($finalWidth, $finalHeight);
	
		imagecopyresampled(
			$indexImage,
			$oldIndexImage,
			0,
			0,
			0,
			0,
			$finalWidth,
			$finalHeight,
			1200,
			1800
		);
	
		imagedestroy($oldIndexImage);
	}

	return $indexImage;
}

?>