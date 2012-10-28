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

class Image
{
	private $ID;
	private $Set;
	private $FileName;
	private $FileExtension;
	private $FileSize = 0;
	private $FileCheckSum;
	private $FileCRC32;
	private $ImageWidth = 0;
	private $ImageHeight = 0;
	
	public function __construct(
		$image_id = NULL, $image_filename = NULL, $image_fileextension = NULL, $image_filesize = 0, $image_filechecksum = NULL, $image_filecrc32 = NULL, $image_width = 0, $image_height = 0,
		$set_id = NULL, $set_prefix = NULL, $set_name = NULL, $set_containswhat = SET_CONTENT_NONE,
		$model_id = NULL, $model_firstname = NULL, $model_lastname = NULL)
	{
		$this->ID = $image_id;
		$this->FileName = $image_filename;
		$this->FileExtension = $image_fileextension;
		$this->FileSize = $image_filesize;
		$this->FileCheckSum = $image_filechecksum;
		$this->FileCRC32 = $image_filecrc32; 
		$this->ImageWidth = $image_width;
		$this->ImageHeight = $image_height;
		
		/* @var $s Set */
		$s = new Set(
			$set_id, $set_prefix, $set_name, $set_containswhat,
			$model_id, $model_firstname, $model_lastname);
		
		$this->Set = $s;
	}
	
	/**
	 * @return int
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
	 * @return int
	 */
	public function getSetID()
	{ return $this->Set ? $this->Set->getID() : NULL; }
	
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
	public function getFileCRC32()
	{ return $this->FileCRC32; }
	
	/**
	 * @param string $FileCRC32
	 */
	public function setFileCRC32($FileCRC32)
	{ $this->FileCRC32 = $FileCRC32; }
	
	/**
	 * @return string
	 */
	public function getFilenameOnDisk()
	{
		return sprintf('%1$s%7$s%2$s%7$s%3$s%4$s%7$s%5$s.%6$s',
			CANDYPATH,
			$this->getSet()->getModel()->GetFullName(),
			$this->getSet()->getPrefix(),
			$this->getSet()->getName(),
			$this->getFileName(),
			$this->getFileExtension(),
			DIRECTORY_SEPARATOR
		);
	}
	
	/**
	 * @param int $includepath
	 * @return string
	 */
	public function getExportFilename($includepath = EXPORT_PATH_OPTION_NONE)
	{
		switch ($includepath)
		{
			default:
			case EXPORT_PATH_OPTION_NONE:
				return sprintf('%1$s.%2$s',
					$this->getFileName(),
					$this->getFileExtension());
	
			case EXPORT_PATH_OPTION_RELATIVE:
				return sprintf('%1$s%6$s%2$s%3$s%6$s%4$s.%5$s',
					$this->getSet()->getModel()->GetFullName(),
					$this->getSet()->getPrefix(),
					$this->getSet()->getName(),
					$this->getFileName(),
					$this->getFileExtension(),
					DIRECTORY_SEPARATOR);
	
			case EXPORT_PATH_OPTION_FULL:
				return $this->getFilenameOnDisk();
		}
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
	 * Calculates the crc32 polynomial of the given Image's file on disk
	 * @param Image $Image
	 * @return string
	 */
	public static function CalculateCRC32($Image)
	{
		return Utils::CalculateCRC32(
			$Image->getFilenameOnDisk()
		);
	}
	
	/**
	 * Gets an array of Images from the database, or NULL on failure.
	 * @param ImageSearchParameters $SearchParameters
	 * @param string $OrderClause
	 * @param string $LimitClause
	 * @return Array(Image) | NULL
	 */
	public static function GetImages($SearchParameters = NULL, $OrderClause = 'model_firstname ASC, model_lastname ASC, set_prefix ASC, set_name ASC, image_filename ASC', $LimitClause = NULL)
	{
		global $dbi;
		$SearchParameters = $SearchParameters ? $SearchParameters : new ImageSearchParameters();		
		$OrderClause = empty($OrderClause) ? 'model_firstname ASC, model_lastname ASC, set_prefix ASC, set_name ASC, image_filename ASC' : $OrderClause;

		$q = sprintf("
			SELECT
				`image_id`, `image_filename`, `image_fileextension`, `image_filesize`, `image_filechecksum`, `image_filecrc32`, `image_width`, `image_height`,
				`set_id`, `set_prefix`, `set_name`, `set_containswhat`,
				`model_id`, `model_firstname`, `model_lastname`
			FROM
				`vw_Image`
			WHERE
				mut_deleted = -1
				%1\$s
			ORDER BY
				%2\$s
			%3\$s",
			$SearchParameters->getWhere(),
			$OrderClause,
			$LimitClause ? ' LIMIT '.$LimitClause : NULL
		);
		
		if(!($stmt = $dbi->prepare($q)))
		{
			$e = new SQLerror($dbi->errno, $dbi->error);
			Error::AddError($e);
			return NULL;
		}
		
		DBi::BindParamsToSelect($SearchParameters, $stmt);
		
		if($stmt->execute())
		{
			$OutArray = array();
			$stmt->bind_result(
					$image_id, $image_filename, $image_fileextension, $image_filesize, $image_filechecksum, $image_filecrc32, $image_width, $image_height,
					$set_id, $set_prefix, $set_name, $set_containswhat,
					$model_id, $model_firstname, $model_lastname);
		
			while($stmt->fetch())
			{
				$o = new self(
					$image_id, $image_filename, $image_fileextension, $image_filesize, $image_filechecksum, $image_filecrc32, $image_width, $image_height,
					$set_id, $set_prefix, $set_name, $set_containswhat,
					$model_id, $model_firstname, $model_lastname);
				
				$OutArray[] = $o;
			}
			
			$stmt->close();
			return $OutArray;
		}
		else
		{
			$e = new SQLerror($dbi->errno, $dbi->error);
			Error::AddError($e);
			return NULL;
		}
	}
	
	/**
	 * Inserts the given image into the database.
	 * @param Image $Image
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function Insert($Image, $CurrentUser)
	{
		return self::InsertMulti(array($Image), $CurrentUser);
	}
	
	/**
	 * Inserts the given images into the database.
	 * @param array(Image) $Images
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function InsertMulti($Images, $CurrentUser)
	{
		global $dbi;
	
		$outBool = TRUE;
		$mut_id = $CurrentUser->getID();
		$mut_date = time();
	
		if(!is_array($Images))
		{ return FALSE; }
	
		$q = sprintf("
			INSERT INTO	`Image` (
				`set_id`,
				`image_filename`,
				`image_fileextension`,
				`image_filesize`,
				`image_filechecksum`,
				`image_filecrc32`,
				`image_width`,
				`image_height`, 
				`mut_id`,
				`mut_date`
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		");
	
		if(!($stmt = $dbi->prepare($q)))
		{
			$e = new SQLerror($dbi->errno, $dbi->error);
			Error::AddError($e);
			return FALSE;
		}
	
		$stmt->bind_param('ississiiii',
			$set_id,
			$image_filename,
			$image_fileextension,
			$image_filesize,
			$image_filechecksum,
			$image_filecrc32,
			$image_width,
			$image_height,
			$mut_id,
			$mut_date
		);
	
		/* @var $Image Image */
		foreach($Images as $Image)
		{
			$set_id = $Image->getSetID();
			$image_filename = $Image->getFileName();
			$image_fileextension = $Image->getFileExtension();
			$image_filesize = $Image->getFileSize();
			$image_filechecksum = $Image->getFileCheckSum();
			$image_filecrc32 = $Image->getFileCRC32();
			$image_width = $Image->getImageWidth();
			$image_height = $Image->getImageHeight();
	
			$outBool = $stmt->execute();
			if($outBool)
			{
				$Image->setID($dbi->insert_id);
			}
			else
			{
				$e = new SQLerror($dbi->errno, $dbi->error);
				Error::AddError($e);
			}
		}
	
		$stmt->close();
		return $outBool;
	}
	
	/**
	 * Updates the databaserecord of supplied Image.
	 * @param Image $Image
	 * @param User $CurrentUser 
	 * @return bool
	 */
	public static function Update($Image, $CurrentUser)
	{
		return self::UpdateMulti(array($Image), $CurrentUser);
	}
	
	/**
	 * Updates the databaserecord of supplied Images.
	 * @param array(Image) $Images
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function UpdateMulti($Images, $CurrentUser)
	{
		global $dbi;
		
		$outBool = TRUE;
		$mut_id = $CurrentUser->getID();
		$mut_date = time();
		
		if(!is_array($Images))
		{ return FALSE; }
		
		$q = sprintf("
			UPDATE `Image` SET
				`set_id` = ?,
				`image_filename` = ?,
				`image_fileextension` = ?,
				`image_filesize` = ?,
				`image_filechecksum` = ?,
				`image_filecrc32` = ?,
				`image_width` = ?,
				`image_height` = ?,
				`mut_id` = ?,
				`mut_date` = ?
			WHERE
				`image_id` = ?
		");
		
		if(!($stmt = $dbi->prepare($q)))
		{
			$e = new SQLerror($dbi->errno, $dbi->error);
			Error::AddError($e);
			return FALSE;
		}
		
		$stmt->bind_param('ississiiiii',
			$set_id,
			$image_filename,
			$image_fileextension,
			$image_filesize,
			$image_filechecksum,
			$image_filecrc32,
			$image_width,
			$image_height,
			$mut_id,
			$mut_date,
			$id
		);
		
		foreach($Images as $Image)
		{
			$set_id = $Image->getSetID();
			$image_filename = $Image->getFileName();
			$image_fileextension = $Image->getFileExtension();
			$image_filesize = $Image->getFileSize();
			$image_filechecksum = $Image->getFileCheckSum();
			$image_filecrc32 = $Image->getFileCRC32();
			$image_width = $Image->getImageWidth();
			$image_height = $Image->getImageHeight();
			$id = $Image->getID();
		
			$outBool = $stmt->execute();
			if(!$outBool)
			{
				$e = new SQLerror($dbi->errno, $dbi->error);
				Error::AddError($e);
			}
		}
		
		$stmt->close();
		return $outBool;
	}
	
	/**
	 * Removes the specified Image from the database.
	 * @param Image $Image
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function Delete($Image, $CurrentUser)
	{
		return self::DeleteMulti(array($Image), $CurrentUser);
	}
	
	/**
	 * Removes the specified Images from the database.
	 * @param array(Image) $Images
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function DeleteMulti($Images, $CurrentUser)
	{
		global $dbi;
	
		$outBool = TRUE;
		$mut_id = $CurrentUser->getID();
		$mut_deleted = time();
	
		if(!is_array($Images))
		{ return FALSE; }
	
		$q = sprintf("
			UPDATE `Image` SET
				`mut_id` = ?,
				`mut_deleted` = ?
			WHERE
				`image_id` = ?
		");
	
		if(!($stmt = $dbi->prepare($q)))
		{
			$e = new SQLerror($dbi->errno, $dbi->error);
			Error::AddError($e);
			return FALSE;
		}
	
		$stmt->bind_param('iii',
			$mut_id,
			$mut_deleted,
			$id
		);
	
		foreach($Images as $Image)
		{
			$id = $Image->getID();
			$outBool = $stmt->execute();

			if(!$outBool)
			{
				$e = new SQLerror($dbi->errno, $dbi->error);
				Error::AddError($e);
			}
		}
	
		$stmt->close();
		return $outBool;
	}
	
	/**
	 * Filters an array of Images and returns those that match the given ModelID and SetID.
	 * @param array $ImageArray
	 * @param int $ModelID
	 * @param int $SetID
	 * @return array(Image)
	 */
	public static function Filter($ImageArray, $ModelID = NULL, $SetID = NULL, $Name = NULL)
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
	public static function OutputImage($filename = 'images/missing.jpg', $width = 800, $height = 600, $cachable = TRUE, $cacheFilenameToBe = NULL, $downloadFilenameToBe = NULL)
	{
		$filename = $filename ? $filename : 'images/missing.jpg';
		$info = getimagesize($filename);
		
		if($info === FALSE)
		{
			$filename = 'images/missing.jpg';
			$info = getimagesize($filename);
		}
		
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
		
		if(!is_null($downloadFilenameToBe)){
			header(
				sprintf('Content-Disposition: attachment; filename="%1$s"',
					$downloadFilenameToBe
				)
			);
		}
		
		header(sprintf('Content-Type: %1$s', Utils::GetMime('jpg')));
		
		@ob_clean();
		flush();
		imagejpeg($DestinationImage, NULL);
		imagedestroy($DestinationImage);
		
		exit;
	}
}

class ImageSearchParameters extends SearchParameters
{
	private $paramtypes = '';
	private $values = array();
	private $where = '';

	/**
	 * @param int $SingleID
	 * @param array(int) $MultipleIDs
	 * @param int $SingleSetID
	 * @param array(int) $MultipleSetIDs
	 * @param int $SingleModelID
	 * @param array(int) $MultipleModelIDs
	 * @param bool $OrAllMultipleIDs
	 * @param bool $PortraitOnly
	 * @param bool $LandscapeOnly
	 */
	public function __construct(
		$SingleID = FALSE, $MultipleIDs = FALSE,
		$SingleSetID = FALSE, $MultipleSetIDs = FALSE,
		$SingleModelID = FALSE, $MultipleModelIDs = FALSE,
		$OrAllMultipleIDs = FALSE, $PortraitOnly = FALSE, $LandscapeOnly = FALSE)
	{
		parent::__construct();

		if($SingleID !== FALSE)
		{
			$this->paramtypes .= "i";
			$this->values[] = $SingleID;
			$this->where .= " AND image_id = ?";
		}

		if(is_array($MultipleIDs) && count($MultipleIDs) > 0 && !$OrAllMultipleIDs)
		{
			$this->paramtypes .= str_repeat('i', count($MultipleIDs));
			$this->values = array_merge($this->values, $MultipleIDs);
			$this->where .= sprintf(" AND image_id IN ( %1s ) ",
					implode(', ', array_fill(0, count($MultipleIDs), '?'))
			);
		}
		
		if($SingleSetID !== FALSE)
		{
			$this->paramtypes .= "i";
			$this->values[] = $SingleSetID;
			$this->where .= " AND set_id = ?";
		}
		
		if(is_array($MultipleSetIDs) && count($MultipleSetIDs) > 0 && !$OrAllMultipleIDs)
		{
			$this->paramtypes .= str_repeat('i', count($MultipleSetIDs));
			$this->values = array_merge($this->values, $MultipleSetIDs);
			$this->where .= sprintf(" AND set_id IN ( %1s ) ",
					implode(', ', array_fill(0, count($MultipleSetIDs), '?'))
			);
		}

		if($SingleModelID !== FALSE)
		{
			$this->paramtypes .= "i";
			$this->values[] = $SingleModelID;
			$this->where .= " AND model_id = ?";
		}

		if(is_array($MultipleModelIDs) && count($MultipleModelIDs) > 0 && !$OrAllMultipleIDs)
		{
			$this->paramtypes .= str_repeat('i', count($MultipleModelIDs));
			$this->values = array_merge($this->values, $MultipleModelIDs);
			$this->where .= sprintf(" AND model_id IN ( %1s ) ",
					implode(', ', array_fill(0, count($MultipleModelIDs), '?'))
			);
		}
		
		if($OrAllMultipleIDs)
		{
			$pieces = array();
			
			if(is_array($MultipleIDs) && count($MultipleIDs) > 0)
			{
				$this->paramtypes .= str_repeat('i', count($MultipleIDs));
				$this->values = array_merge($this->values, $MultipleIDs);
				$pieces[] = sprintf("image_id IN ( %1s )",
						implode(', ', array_fill(0, count($MultipleIDs), '?'))
				);
			}
			
			if(is_array($MultipleSetIDs) && count($MultipleSetIDs) > 0)
			{
				$this->paramtypes .= str_repeat('i', count($MultipleSetIDs));
				$this->values = array_merge($this->values, $MultipleSetIDs);
				$pieces[] = sprintf("set_id IN ( %1s )",
						implode(', ', array_fill(0, count($MultipleSetIDs), '?'))
				);
			}
			
			if(is_array($MultipleModelIDs) && count($MultipleModelIDs) > 0)
			{
				$this->paramtypes .= str_repeat('i', count($MultipleModelIDs));
				$this->values = array_merge($this->values, $MultipleModelIDs);
				$pieces[] = sprintf("model_id IN ( %1s )",
						implode(', ', array_fill(0, count($MultipleModelIDs), '?'))
				);
			}
			
			if($pieces)
			{
				$this->where .= " AND (";
				$this->where .= implode(' OR ', $pieces);
				$this->where .= ")";
			}
		}

		if($PortraitOnly)
		{
			$this->where .= " AND image_height > image_width";
		}
		
		if($LandscapeOnly)
		{
			$this->where .= " AND image_width > image_height";
		}
	}

	public function getWhere()
	{ return $this->where; }

	public function getValues()
	{ return $this->values; }

	public function getParamTypes()
	{ return $this->paramtypes; }
}

?>
