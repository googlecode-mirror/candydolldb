<?php

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
	
	public function __construct(
		$image_id = null, $image_filename = null, $image_fileextension = null, $image_filesize = 0, $image_filechecksum = null, $image_width = 0, $image_height = 0,
		$set_id = null, $set_prefix = null, $set_name = null, $set_containswhat = SET_CONTENT_NONE,
		$model_id = null, $model_firstname = null, $model_lastname = null)
	{
		$this->ID = $image_id;
		$this->FileName = $image_filename;
		$this->FileExtension = $image_fileextension;
		$this->FileSize = $image_filesize;
		$this->FileCheckSum = $image_filechecksum;
		$this->ImageWidth = $image_width;
		$this->ImageHeight = $image_height;
		
		/* @var $m Model */
		/* @var $s Set */
		$m = new Model($model_id, $model_firstname, $model_lastname);
		$s = new Set($set_id, $set_prefix, $set_name, $set_containswhat);
		
		$s->setModel($m);
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
	 * Gets an array of Images from the database, or NULL on failure.
	 * @param ImageSearchParameters $SearchParameters
	 * @param string $OrderClause
	 * @param string $LimitClause
	 * @return Array(Image) | NULL
	 */
	public static function GetImages($SearchParameters = null, $OrderClause = 'model_firstname ASC, model_lastname ASC, set_prefix ASC, set_name ASC, image_filename ASC', $LimitClause = null)
	{
		global $dbi;
		$SearchParameters = $SearchParameters ? $SearchParameters : new ImageSearchParameters();		
		$OrderClause = empty($OrderClause) ? 'model_firstname ASC, model_lastname ASC, set_prefix ASC, set_name ASC, image_filename ASC' : $OrderClause;

		$q = sprintf("
			SELECT
				`image_id`, `image_filename`, `image_fileextension`, `image_filesize`, `image_filechecksum`, `image_width`, `image_height`,
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
			$LimitClause ? ' LIMIT '.$LimitClause : null
		);
		
		if(!($stmt = $dbi->prepare($q)))
		{
			$e = new SQLerror($dbi->errno, $dbi->error);
			Error::AddError($e);
			return null;
		}
		
		DBi::BindParamsToSelect($SearchParameters, $stmt);
		
		if($stmt->execute())
		{
			$OutArray = array();
			$stmt->bind_result(
					$image_id, $image_filename, $image_fileextension, $image_filesize, $image_filechecksum, $image_width, $image_height,
					$set_id, $set_prefix, $set_name, $set_containswhat,
					$model_id, $model_firstname, $model_lastname);
		
			while($stmt->fetch())
			{
				$o = new self(
					$image_id, $image_filename, $image_fileextension, $image_filesize, $image_filechecksum, $image_width, $image_height,
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
			return null;
		}
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
	    
	    $result = $db->Insert(
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
	    
	    return $result;
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
		
		$result = $db->Update(
			'Image',
			array(
				'set_id' => $Image->getSet()->getID(),
				'image_filename' => mysql_real_escape_string($Image->getFileName()),
				'image_fileextension' => mysql_real_escape_string($Image->getFileExtension()),
				'image_filesize' => $Image->getFileSize(),
				'image_filechecksum' => mysql_real_escape_string($Image->getFileCheckSum()),
				'image_width' => $Image->getImageWidth(),
				'image_height' => $Image->getImageHeight(),
				'mut_id' => $CurrentUser->getID(),
				'mut_date' => time()
			),
			array(
				'image_id', $Image->getID())
		);
		
		return $result;
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
	public static function OutputImage($filename = 'images/missing.jpg', $width = 800, $height = 600, $cachable = true, $cacheFilenameToBe = null, $downloadFilenameToBe = null)
	{
		$filename = $filename ? $filename : 'images/missing.jpg';
		$info = getimagesize($filename);
		
		if($info === false)
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
		
		header('Content-Type: image/jpeg');
		
		@ob_clean();
		flush();
		imagejpeg($DestinationImage, null);
		imagedestroy($DestinationImage);
		
		exit;
	}
}

class ImageSearchParameters extends SearchParameters
{
	private $paramtypes = '';
	private $values = array();
	private $where = '';

	public function __construct(
		$SingleID = null, $MultipleIDs = null,
		$SingleSetID = null, $MultipleSetIDs = null,
		$SingleModelID = null, $MultipleModelIDs = null,
		$OrAllMultipleIDs = false, $PortraitOnly = false, $LandscapeOnly = false)
	{
		parent::__construct();

		if($SingleID)
		{
			$this->paramtypes .= "i";
			$this->values[] = $SingleID;
			$this->where .= " AND image_id = ?";
		}

		if($MultipleIDs && !$OrAllMultipleIDs)
		{
			$this->paramtypes .= str_repeat('i', count($MultipleIDs));
			$this->values = array_merge($this->values, $MultipleIDs);
			$this->where .= sprintf(" AND image_id IN ( %1s ) ",
					implode(', ', array_fill(0, count($MultipleIDs), '?'))
			);
		}
		
		if($SingleSetID)
		{
			$this->paramtypes .= "i";
			$this->values[] = $SingleSetID;
			$this->where .= " AND set_id = ?";
		}
		
		if($MultipleSetIDs && !$OrAllMultipleIDs)
		{
			$this->paramtypes .= str_repeat('i', count($MultipleSetIDs));
			$this->values = array_merge($this->values, $MultipleSetIDs);
			$this->where .= sprintf(" AND set_id IN ( %1s ) ",
					implode(', ', array_fill(0, count($MultipleSetIDs), '?'))
			);
		}

		if($SingleModelID)
		{
			$this->paramtypes .= "i";
			$this->values[] = $SingleModelID;
			$this->where .= " AND model_id = ?";
		}

		if($MultipleModelIDs && !$OrAllMultipleIDs)
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
			
			if($MultipleIDs)
			{
				$this->paramtypes .= str_repeat('i', count($MultipleIDs));
				$this->values = array_merge($this->values, $MultipleIDs);
				$pieces[] = sprintf("image_id IN ( %1s )",
						implode(', ', array_fill(0, count($MultipleIDs), '?'))
				);
			}
			
			if($MultipleSetIDs)
			{
				$this->paramtypes .= str_repeat('i', count($MultipleSetIDs));
				$this->values = array_merge($this->values, $MultipleSetIDs);
				$pieces[] = sprintf("set_id IN ( %1s )",
						implode(', ', array_fill(0, count($MultipleSetIDs), '?'))
				);
			}
			
			if($MultipleModelIDs)
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