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

$finalWidth = Utils::SafeIntFromQS('width');
$finalHeight = Utils::SafeIntFromQS('height');
$perPage = Utils::SafeIntFromQS('perpage');
$promptDownload = Utils::SafeBoolFromQS('download');

$pathPrefix = (isset($argv) && $argc > 0) ? dirname($_SERVER['PHP_SELF']).'/' : '';
$indexImage = NULL;  

$Images = Image::GetImages(new ImageSearchParameters(FALSE, FALSE, FALSE, FALSE, $ModelID));
$Sets = Set::GetSets(new SetSearchParameters(FALSE, FALSE, $ModelID));

$pageIterator = 1;
$perPage = $perPage && $perPage > 0 ? $perPage : count($Sets);
$indexImages = array();

while( ($pageIterator - 1) * $perPage < count($Sets) )
{
	$Sets2Process = array_slice($Sets, ($pageIterator - 1) * $perPage, $perPage);
	
	$indexImages[] = GenerateModelIndex($Sets2Process, $Images, $pathPrefix, $finalWidth, $finalHeight); 

	$pageIterator++;
}

header(sprintf('Content-Type: %1$s', Utils::GetMime('jpg')));
foreach ($indexImages as $img)
{
	imagejpeg($img);
}

die();

/**
 * Creates a dynamic index-image from the supplied sets
 * @param array(Set) $Sets
 * @return resource
 */
function GenerateModelIndex($Sets, $Images, $pathPrefix = '', $finalWidth = NULL, $finalHeight = NULL)
{
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

if($Sets && !in_array($Sets[0]->getModel()->getFullName(), array('Promotions', 'Interviews')))
{
	
	
}

if(is_null($indexImage))
{ $indexImage = imagecreatefromjpeg($pathPrefix.'images/missing.jpg'); }

$CacheImage = new CacheImage();
$CacheImage->setModelIndexID($ModelID);
$CacheImage->setKind(CACHEIMAGE_KIND_INDEX);
$CacheImage->setImageWidth($finalWidth);
$CacheImage->setImageHeight($finalHeight);
$CacheImage->setSequenceNumber(1);
$CacheImage->setSequenceTotal(1);
CacheImage::Insert($CacheImage, $CurrentUser);

imagejpeg($indexImage, $CacheImage->getFilenameOnDisk());

header(sprintf('Content-Type: %1$s', Utils::GetMime('jpg')));

if($promptDownload){
	header(
		sprintf('Content-Disposition: attachment; filename="%1$s.jpg"',
			$Sets[0]->getModel()->getFullName()
		)
	);
}

@ob_clean();
flush();
imagejpeg($indexImage);
imagedestroy($indexImage);

exit;

?>