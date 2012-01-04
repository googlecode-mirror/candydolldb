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

class Image
{
	private $ID;
	private $Set;
	private $FileName;
	private $FileExtension;
	private $FileSize = 0;
	private $FileCheckSum;
	private $ImageWidth = 0;
	private $ImageHeight = 0;
	
	/**
	 * @param int $ID
	 * @param string $FileName
	 * @param string $FileExtension
	 */
	public function Image($ID = null, $FileName = null, $FileExtension = null)
	{
		$this->ID = $ID;
		$this->FileName = $FileName;
		$this->FileExtension = $FileExtension;
	}
	
	/**	 * @return int
	 */
	public function getID()
	{ return $this->ID; }
	
	/**
	 * @param int $ID
	 */
	public function setID($ID)
	{ $this->ID = $ID; }
	
	/**
	 * @return Set
	 */
	public function getSet()
	{ return $this->Set; }
	
	/**
	 * @param Set $Set
	 */
	public function setSet($Set)
	{ $this->Set = $Set; }
	
	/**
	 * @return string
	 */
	public function getFileName()
	{ return $this->FileName; }
	
	/**
	 * @param string $FileName
	 */
	public function setFileName($FileName)
	{ $this->FileName = $FileName; }
	
	/**
	 * @return string
	 */
	public function getFileExtension()
	{ return $this->FileExtension; }
	
	/**
	 * @param string $FileExtension
	 */
	public function setFileExtension($FileExtension)
	{ $this->FileExtension = $FileExtension; }

	/**
	 * @return int
	 */
	public function getFileSize()
	{ return $this->FileSize; }
	
	/**
	 * @param int $FileSize
	 */
	public function setFileSize($FileSize)
	{ $this->FileSize = $FileSize; }
	
	/**
	 * @return int
	 */
	public function getImageWidth()
	{ return $this->ImageWidth; }
	
	/**
	 * @param int $ImageWidth
	 */
	public function setImageWidth($ImageWidth)
	{ $this->ImageWidth = $ImageWidth; }
	
	/**
	 * @return int
	 */
	public function getImageHeight()
	{ return $this->ImageHeight; }
	
	/**
	 * @param int $ImageHeight
	 */
	public function setImageHeight($ImageHeight)
	{ $this->ImageHeight = $ImageHeight; }
	
	/**
	 * @return string
	 */
	public function getFileCheckSum()
	{ return $this->FileCheckSum; }
	
	/**
	 * @param string $FileCheckSum
	 */
	public function setFileCheckSum($FileCheckSum)
	{ $this->FileCheckSum = $FileCheckSum; }

	
	/**
	 * @return string
	 */
	public function getFilenameOnDisk()
	{
		return sprintf('%1$s/%2$s/%3$s%4$s/%5$s.%6$s',
			CANDYIMAGEPATH,
			$this->getSet()->getModel()->GetFullName(),
			$this->getSet()->getPrefix(),
			$this->getSet()->getName(),
			$this->getFileName(),
			$this->getFileExtension()
		);
	}
	
	/**
	 * Calculates a resize factor for aspect-ratio scaling
	 * @param int $width
	 * @param int $height
	 * @return float
	 */
	private function CalculateResizeFactor($width, $height)
	{
		$FactorX = $this->getImageWidth() / $width;
		$FactorY = $this->getImageHeight() / $height;
		$FactorToUse = $FactorX >= $FactorY ? $FactorX : $FactorY;
		return $FactorToUse;
	}
	
	/**
	 * Calculates this Image's height, when scaled to fit the supplied dimensions
	 * @param int $width
	 * @param int $height
	 * @return int
	 */
	public function getImageHeightToppedOff($width, $height)
	{
		if($this->getImageHeight() > $height)
		{
			$FactorToUse = $this->CalculateResizeFactor($width, $height);
			$NewHeight = (int)($this->getImageHeight() / $FactorToUse);
			return $NewHeight;
		}
		else
		{
			return $this->getImageHeight();
		}
	}
	
	/**
	* Calculates this Image's width, when scaled to fit the supplied dimensions
	* @param int $width
	* @param int $height
	* @return int
	*/
	public function getImageWidthToppedOff($width, $height)
	{
		if($this->getImageWidth() > $width)
		{
			$FactorToUse = $this->CalculateResizeFactor($width, $height);
			$NewWidth = (int)($this->getImageWidth() / $FactorToUse);
			return $NewWidth;
		}
		else
		{
			return $this->getImageWidth();
		}
	}
	
	
	/**
	 * @param string $WhereClause
	 * @param string $OrderClause
	 * @param string $LimitClause
	 * @return Array(Image) | NULL
	 */
	public static function GetImages($WhereClause = 'mut_deleted = -1', $OrderClause = 'model_firstname ASC, model_lastname ASC, set_prefix ASC, set_name ASC, image_filename ASC', $LimitClause = null)
	{
		global $db;
			
		if($db->Select('vw_Image', '*', $WhereClause, $OrderClause, $LimitClause))
		{
			$OutArray = array();
			
			if($db->getResult())
			{
				foreach($db->getResult() as $ImageItem)
				{
					$ImageObject = new Image();
					$SetObject = new Set();
					$ModelObject = new Model();
					
					foreach($ImageItem as $ColumnKey => $ColumnValue)
					{
						switch($ColumnKey)
						{
							case 'image_id'				: $ImageObject->setID($ColumnValue); 			break;
							case 'image_filename'		: $ImageObject->setFileName($ColumnValue); 		break;
							case 'image_fileextension'	: $ImageObject->setFileExtension($ColumnValue); break;
							case 'image_filesize'		: $ImageObject->setFileSize($ColumnValue); 		break;
							case 'image_filechecksum'	: $ImageObject->setFileCheckSum($ColumnValue); 	break;
							case 'image_width'			: $ImageObject->setImageWidth($ColumnValue); 	break;
							case 'image_height'			: $ImageObject->setImageHeight($ColumnValue); 	break;
							
							case 'set_id'			: $SetObject->setID($ColumnValue);				break;
							case 'set_prefix'		: $SetObject->setPrefix($ColumnValue);			break;
							case 'set_name'			: $SetObject->setName($ColumnValue);			break;
							case 'set_date_pic'		: $SetObject->setDatePic($ColumnValue);			break;
							case 'set_date_vid'		: $SetObject->setDateVid($ColumnValue);			break;
							case 'set_containswhat'	: $SetObject->setContainsWhat($ColumnValue);	break;
							
							case 'model_id'			: $ModelObject->setID($ColumnValue);			break;
							case 'model_firstname'	: $ModelObject->setFirstName($ColumnValue);		break;
							case 'model_lastname'	: $ModelObject->setLastName($ColumnValue);		break;
						}
					}
					
					$SetObject->setModel($ModelObject);
					$ImageObject->setSet($SetObject);
					
					$OutArray[] = $ImageObject;
				}
			}
			return $OutArray;
		}
		else
		{ return null; }
	}
	
	/**
	 * Inserts the given image into the database.
	 * @param Image $Image
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function InsertImage($Image, $CurrentUser)
	{
	    global $db;
	    
	    return $db->Insert(
		'Image',
		array(
		    $Image->getSet()->getID(),
			mysql_real_escape_string($Image->getFileName()),
		    mysql_real_escape_string($Image->getFileExtension()),
		    $Image->getFileSize(),
		    mysql_real_escape_string($Image->getFileCheckSum()),
		    $Image->getImageWidth(),
		    $Image->getImageHeight(),
		    $CurrentUser->getID(),
		    time()
		),
		'set_id, image_filename, image_fileextension, image_filesize, image_filechecksum, image_width, image_height, mut_id, mut_date'
	    );
	}
	
	/**
	 * Updates the databaserecord of supplied Image.
	 * 
	 * @param Image $Image
	 * @param User $CurrentUser 
	 * @return bool
	 */
	public static function UpdateImage($Image, $CurrentUser)
	{
		global $db;
		
		return $db->Update(
			'Image',
			array(
				'set_id' => $Image->getSet()->getID(),
				'image_filename' => mysql_real_escape_string($Image->getFileName()),
				'image_fileextension' => mysql_real_escape_string($Image->getFileExtension()),
				'image_filesize' => $Image->getFileSize(),
				'image_filechecksum' => $Image->getFileCheckSum(),
				'image_width' => $Image->getImageWidth(),
				'image_height' => $Image->getImageHeight(),
				'mut_id' => $CurrentUser->getID(),
				'mut_date' => time()
			),
			array(
				'image_id', $Image->getID())
		);
	}
	
	/**
	 * Removes the specified Image from the database.
	 * 
	 * @param Image $Image
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function DeleteImage($Image, $CurrentUser)
	{
		global $db;
		
		return $db->Update(
			'Image',
			array(
				'mut_id' => $CurrentUser->getID(),
				'mut_deleted' => time()),
			array(
				'image_id', $Image->getID())
		);
	}
	
	/**
	 * Filters an array of Images and returns those that match the given ModelID and SetID.
	 * @param array $ImageArray
	 * @param int $ModelID
	 * @param int $SetID
	 * @return array(Image)
	 */
	public static function FilterImages($ImageArray, $ModelID = null, $SetID = null, $Name = null)
	{
		$OutArray = array();
			
		/* @var $Image Image */
		foreach($ImageArray as $Image)
		{
			if(
				(is_null($ModelID) || $Image->getSet()->getModel()->getID() == $ModelID)	&&
				(is_null($SetID) || $Image->getSet()->getID() == $SetID) 					&&
				(is_null($Name) || $Image->getFileName() == $Name)				
			){
				$OutArray[] = $Image;
			}
		}
		return $OutArray;
	}

	/**
	 * Tries to output an image taken from filename, scaled down to the given width and height.  
	 * @param string $filename
	 * @param int $width
	 * @param int $height
	 */
	public static function OutputImage($filename = 'images/missing.jpg', $width = 800, $height = 600, $cachable = true, $cacheFilenameToBe = null)
	{
		$filename = $filename ? $filename : 'images/missing.jpg';
	
		$info = getimagesize($filename);
		
		$FactorX = $info[0] / $width;
		$FactorY = $info[1] / $height;
		$FactorToUse = $FactorX >= $FactorY ? $FactorX : $FactorY;
		
		$NewWidth = (int)($info[0] / $FactorToUse);
		$NewHeight = (int)($info[1] / $FactorToUse);
					
		$SourceImage = imagecreatefromjpeg($filename);
		$DestinationImage = imagecreatetruecolor($NewWidth, $NewHeight);
		
		imagecopyresampled($DestinationImage, $SourceImage, 0, 0, 0, 0, $NewWidth, $NewHeight, $info[0], $info[1]);
		
		imagedestroy($SourceImage);
		
		if($cacheFilenameToBe)
		{ imagejpeg($DestinationImage, $cacheFilenameToBe); }
		
		if($cachable)
		{
			header("Cache-Control: public");
			header("Expires: Tue, 19 Jan 2038 03:14:07 GMT");
		}
		else
		{
			header("Cache-Control: no-cache, must-revalidate");
			header("Expires: Thu, 15 Sep 1983 08:43:00 GMT");
		}
		
		header('Content-Type: image/jpeg');
		
		@ob_clean();
		flush();
		imagejpeg($DestinationImage, null);
		imagedestroy($DestinationImage);
		
		exit;
	}
	
	
}

?>