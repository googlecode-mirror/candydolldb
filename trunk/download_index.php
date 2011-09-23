<?php

include('cd.php');
$CurrentUser = Authentication::Authenticate();

/**
 * Return a concatenated, condensed string of all the array's values,
 * For example '1,2,3,4,6,7,8,10,13' becomes '1-8, 10, 13'.
 * @param array $inArray
 */
function RangeString($inArray){
	if(!is_array($inArray) || count($inArray) == 0){
		return null;
	}
	
	sort($inArray, SORT_ASC);	
	$s = ''; 
	
	for ($i = 0; $i < count($sets); $i++)
	{	
		if($i == 0){
			$s .= $sets[$i];	
		}
		else if($sets[$i] == $sets[$i - 1] + 1 && $sets[$i] == $sets[$i + 1] - 1){
			continue;
		}
		else if($sets[$i-1] == $sets[$i] -1){
			$s .= '-'.$sets[$i];
		}
		else if($sets[$i-1] != $sets[$i] -1){
			$s .= ', '.$sets[$i];
		}
	}
	
	return $s;
}


$image = imagecreatefrompng('images/index_background.png');
$candyColor = imagecolorallocate($image, 255, 246, 195);
$font = 'images/FreeSerifBoldItalic.ttf';
$fontSizeTitle = 76;
$fontSizeSubTitle = 30;


class ThumbnailSettings
{
	public $fontSizeCaption;
	public $startX;
	public $startY;
	public $width;
	public $height;
	public $marginX;
	public $marginY;
	public $numberOfColumns;
	public $numberOfRows;
}


$pics = array(
	 CANDYIMAGEPATH.'/Vika Zadorozhnyaya/set_01/VikaZ01_015.jpg'
);


$textTitle = 'Vika Zadorozhnyaya';
$titleCoords = imagettfbbox($fontSizeTitle, 0, $font, $textTitle);
$titleWidth = $titleCoords[2] - $titleCoords[0];
imagettftext($image, $fontSizeTitle, 0, (1160-$titleWidth), 90, $candyColor, $font, $textTitle);


$textSubTitle = 'Set 1';
$subTitleCoords = imagettfbbox($fontSizeSubTitle, 0, $font, $textSubTitle);
$subTitleWidth = $subTitleCoords[2] - $subTitleCoords[0];
imagettftext($image, $fontSizeSubTitle, 0, (1160-$subTitleWidth), 150, $candyColor, $font, $textSubTitle);



$th = null;
switch ($_GET['c']){
	
	case 1:
		$th = new ThumbnailSettings();
		$th->fontSizeCaption = 30;
		$th->captionMarginTop = 40;
		$th->startX = 125;
		$th->startY = 200;
		$th->width = 950;
		$th->height = 1425;
		$th->marginX = 0;
		$th->marginY = $th->fontSizeCaption + $th->captionMarginTop;
		$th->numberOfColumns = 1;
		$th->numberOfRows = 1;
		break;
		
	case 2:
		$th = new ThumbnailSettings();
		$th->fontSizeCaption = 30;
		$th->captionMarginTop = 40;
		$th->startX = 125;
		$th->startY = 500;
		$th->width = 465;
		$th->height = 698;
		$th->marginX = 20;
		$th->marginY = $th->fontSizeCaption + $th->captionMarginTop;
		$th->numberOfColumns = 2;
		$th->numberOfRows = 1;
		break;
		
	case 3:
	case 4:
		$th = new ThumbnailSettings();
		$th->fontSizeCaption = 30;
		$th->captionMarginTop = 40;
		$th->startX = 125;
		$th->startY = 200;
		$th->width = 465;
		$th->height = 698;
		$th->marginX = 20;
		$th->marginY = $th->fontSizeCaption + $th->captionMarginTop + 10;
		$th->numberOfColumns = 2;
		$th->numberOfRows = 2;
		break;
		
	case 5:
	case 6:
		$th = new ThumbnailSettings();
		$th->fontSizeCaption = 24;
		$th->captionMarginTop = 30;
		$th->startX = 125;
		$th->startY = 350;
		$th->width = 300;
		$th->height = 450;
		$th->marginX = 25;
		$th->marginY = $th->fontSizeCaption + $th->captionMarginTop + 60;
		$th->numberOfColumns = 3;
		$th->numberOfRows = 2;
		break;
		
	case 7:
	case 8:
	case 9:
		$th = new ThumbnailSettings();
		$th->fontSizeCaption = 24;
		$th->captionMarginTop = 30;
		$th->startX = 125;
		$th->startY = 200;
		$th->width = 300;
		$th->height = 450;
		$th->marginX = 25;
		$th->marginY = $th->fontSizeCaption + $th->captionMarginTop + 20;
		$th->numberOfColumns = 3;
		$th->numberOfRows = 3;
		break;
		
	case 10:
	case 11:
	case 12:
		$th = new ThumbnailSettings();
		$th->fontSizeCaption = 24;
		$th->captionMarginTop = 30;
		$th->startX = 50;
		$th->startY = 250;
		$th->width = 250;
		$th->height = 375;
		$th->marginX = 33;
		$th->marginY = $th->fontSizeCaption + $th->captionMarginTop + 20;
		$th->numberOfColumns = 4;
		$th->numberOfRows = 3;
		break;
		
	case 13:
	case 14:
	case 15:
	case 16:
		$th = new ThumbnailSettings();
		$th->fontSizeCaption = 20;
		$th->captionMarginTop = 24;
		$th->startX = 100;
		$th->startY = 180;
		$th->width = 220;
		$th->height = 330;
		$th->marginX = 40;
		$th->marginY = $th->fontSizeCaption + $th->captionMarginTop + 20;
		$th->numberOfColumns = 4;
		$th->numberOfRows = 4;
		break;
		
	case 17:
	case 18:
	case 19:
	case 20:
		$th = new ThumbnailSettings();
		$th->fontSizeCaption = 20;
		$th->captionMarginTop = 24;
		$th->startX = 65;
		$th->startY = 230;
		$th->width = 190;
		$th->height = 285;
		$th->marginX = 30;
		$th->marginY = $th->fontSizeCaption + $th->captionMarginTop + 20;
		$th->numberOfColumns = 5;
		$th->numberOfRows = 4;
		break;
	
	case 21:
	case 22:
	case 23:
	case 24:
	case 25:
		$th = new ThumbnailSettings();
		$th->fontSizeCaption = 20;
		$th->captionMarginTop = 24;
		$th->startX = 100;
		$th->startY = 180;
		$th->width = 174;
		$th->height = 261;
		$th->marginX = 33;
		$th->marginY = $th->fontSizeCaption + $th->captionMarginTop + 10;
		$th->numberOfColumns = 5;
		$th->numberOfRows = 5;
		break;
	
	case 26:
	case 27:
	case 28:
	case 29:
	case 30:
		$th = new ThumbnailSettings();
		$th->fontSizeCaption = 20;
		$th->captionMarginTop = 24;
		$th->startX = 75;
		$th->startY = 220;
		$th->width = 147;
		$th->height = 220;
		$th->marginX = 34;
		$th->marginY = $th->fontSizeCaption + $th->captionMarginTop + 40;
		$th->numberOfColumns = 6;
		$th->numberOfRows = 5;
		break;
		
	case 31:
	case 32:
	case 33:
	case 34:
	case 35:
	case 36:
		$th = new ThumbnailSettings();
		$th->fontSizeCaption = 20;
		$th->captionMarginTop = 24;
		$th->startX = 75;
		$th->startY = 180;
		$th->width = 147;
		$th->height = 220;
		$th->marginX = 34;
		$th->marginY = $th->fontSizeCaption + $th->captionMarginTop;
		$th->numberOfColumns = 6;
		$th->numberOfRows = 6;
		break;
		
	case 37:
	case 38:
	case 39:
	case 40:
	case 41:
	case 42:
		$th = new ThumbnailSettings();
		$th->fontSizeCaption = 18;
		$th->captionMarginTop = 20;
		$th->startX = 21;
		$th->startY = 200;
		$th->width = 147;
		$th->height = 220;
		$th->marginX = 21;
		$th->marginY = $th->fontSizeCaption + $th->captionMarginTop;
		$th->numberOfColumns = 7;
		$th->numberOfRows = 6;
		break;
		
	case 43:
	case 44:
	case 45:
	case 46:
	case 47:
	case 48:
	case 49:
		$th = new ThumbnailSettings();
		$th->fontSizeCaption = 16;
		$th->captionMarginTop = 18;
		$th->startX = 50;
		$th->startY = 200;
		$th->width = 120;
		$th->height = 180;
		$th->marginX = 43;
		$th->marginY = $th->fontSizeCaption + $th->captionMarginTop + 10;
		$th->numberOfColumns = 7;
		$th->numberOfRows = 7;
		break;
	
	case 50:
	case 51:
	case 52:
	case 53:
	case 54:
	case 55:
	case 56:
		$th = new ThumbnailSettings();
		$th->fontSizeCaption = 16;
		$th->captionMarginTop = 18;
		$th->startX = 50;
		$th->startY = 200;
		$th->width = 120;
		$th->height = 180;
		$th->marginX = 20;
		$th->marginY = $th->fontSizeCaption + $th->captionMarginTop + 10;
		$th->numberOfColumns = 8;
		$th->numberOfRows = 7;
		break;
	
	case 57:
	case 58:
	case 59:
	case 60:
	case 61:
	case 62:
	case 63:
	case 64:
		$th = new ThumbnailSettings();
		$th->fontSizeCaption = 16;
		$th->captionMarginTop = 18;
		$th->startX = 75;
		$th->startY = 200;
		$th->width = 100;
		$th->height = 150;
		$th->marginX = 36;
		$th->marginY = $th->fontSizeCaption + $th->captionMarginTop + 10;
		$th->numberOfColumns = 8;
		$th->numberOfRows = 8;
		break;

	case 65:
	case 66:
	case 67:
	case 68:
	case 69:
	case 70:
	case 71:
	case 72:
		$th = new ThumbnailSettings();
		$th->fontSizeCaption = 16;
		$th->captionMarginTop = 18;
		$th->startX = 75;
		$th->startY = 200;
		$th->width = 100;
		$th->height = 150;
		$th->marginX = 18;
		$th->marginY = $th->fontSizeCaption + $th->captionMarginTop + 10;
		$th->numberOfColumns = 9;
		$th->numberOfRows = 8;
		break;
	
	case 73:
	case 74:
	case 75:
	case 76:
	case 77:
	case 78:
	case 79:
	case 80:
	case 81:
		$th = new ThumbnailSettings();
		$th->fontSizeCaption = 14;
		$th->captionMarginTop = 16;
		$th->startX = 75;
		$th->startY = 180;
		$th->width = 95;
		$th->height = 143;
		$th->marginX = 24;
		$th->marginY = $th->fontSizeCaption + $th->captionMarginTop;
		$th->numberOfColumns = 9;
		$th->numberOfRows = 9;
		break;
		
	case 82:
	case 83:
	case 84:
	case 85:
	case 86:
	case 87:
	case 88:
	case 89:
	case 90:
		$th = new ThumbnailSettings();
		$th->fontSizeCaption = 14;
		$th->captionMarginTop = 16;
		$th->startX = 25;
		$th->startY = 200;
		$th->width = 95;
		$th->height = 143;
		$th->marginX = 22;
		$th->marginY = $th->fontSizeCaption + $th->captionMarginTop;
		$th->numberOfColumns = 10;
		$th->numberOfRows = 9;
		break;
}

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
			
			$picIndex = array_rand($pics);
			$srcImage = imagecreatefromjpeg( $pics[$picIndex] );
			unset($pics[$picIndex]);
			
			if($srcImage)
			{
				imagecopyresampled(
					$image,
					$srcImage,
					$thumbX,
					$thumbY,
					0,
					0,
					$th->width,
					$th->height,
					1200,
					1800
				);
				
				imagedestroy($srcImage);
				
				$textCaption = 'Set 01';
				$captionCoords = imagettfbbox($th->fontSizeCaption, 0, $font, $textCaption);
				$captionWidth = $captionCoords[2] - $captionCoords[0];
				
				imagettftext(
					$image,
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

header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);

?>