<?php

class CacheImage
{
	private $ID;
	private $ModelIndexID;
	private $ModelID;
	private $SetID;
	private $ImageID;
	private $VideoID;
	private $ImageWidth = 0;
	private $ImageHeight = 0;
	private $SequenceNumber = 1;
	private $SequenceTotal = 1;
	private $Kind = CACHEIMAGE_KIND_UNKNOWN;
	
	public function __construct(
		$cache_id = NULL, $index_id = NULL, $model_id = NULL, $set_id = NULL, $image_id = NULL, $video_id = NULL, $cache_imagewidth = 0, $cache_imageheight = 0, $index_sequence_number = 1, $index_sequence_total = 1)
	{
		$this->ID = $cache_id ? $cache_id : Utils::UUID();

		$this->ModelIndexID = $index_id;
		$this->ModelID = $model_id;
		$this->SetID = $set_id;
		$this->ImageID = $image_id;
		$this->VideoID = $video_id;
		$this->ImageWidth = $cache_imagewidth;
		$this->ImageHeight = $cache_imageheight;
		$this->SequenceNumber = $index_sequence_number;
		$this->SequenceTotal = $index_sequence_total;
		
		$this->Kind =
			($index_id ? CACHEIMAGE_KIND_INDEX :
			($model_id ? CACHEIMAGE_KIND_MODEL :
			($set_id ? CACHEIMAGE_KIND_SET :
			($image_id ? CACHEIMAGE_KIND_IMAGE :
			($video_id ? CACHEIMAGE_KIND_VIDEO :
			(CACHEIMAGE_KIND_UNKNOWN
		))))));
	}
	
	/**
	* @return string
	*/
	public function getID()
	{ return $this->ID; }
	
	/**
	* @param string $ID
	*/
	public function setID($ID)
	{ $this->ID = $ID; }
	
	/**
	* @return int
	*/
	public function getModelID()
	{ return $this->ModelID; }
	
	/**
	 * @param int $ModelID
	 */
	public function setModelID($ModelID)
	{ $this->ModelID = $ModelID; }
	
	/**
	* @return int
	*/
	public function getModelIndexID()
	{ return $this->ModelIndexID; }
	
	/**
	 * @param int $ModelIndexID
	 */
	public function setModelIndexID($ModelIndexID)
	{ $this->ModelIndexID = $ModelIndexID; }
	
	/**
	* @return int
	*/
	public function getSetID()
	{ return $this->SetID; }
	
	/**
	* @param int $SetID
	*/
	public function setSetID($SetID)
	{ $this->SetID = $SetID; }
	
	/**
	* @return int
	*/
	public function getImageID()
	{ return $this->ImageID; }
	
	/**
	 * @param int $ImageID
	 */
	public function setImageID($ImageID)
	{ $this->ImageID = $ImageID; }
	
	/**
	* @return int
	*/
	public function getVideoID()
	{ return $this->VideoID; }
	
	/**
	 * @param int $VideoID
	 */
	public function setVideoID($VideoID)
	{ $this->VideoID = $VideoID; }
	
	/**
	* @return int
	*/
	public function getKind()
	{ return $this->Kind; }
	
	/**
	 * @param int $Kind
	 */
	public function setKind($Kind)
	{ $this->Kind = $Kind; }
	
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
	* @return int
	*/
	public function getSequenceNumber()
	{ return $this->SequenceNumber; }
	
	/**
	* @param int $SequenceNumber
	*/
	public function setSequenceNumber($SequenceNumber)
	{ $this->SequenceNumber = $SequenceNumber; }
	
	/**
	* @return int
	*/
	public function getSequenceTotal()
	{ return $this->SequenceTotal; }
	
	/**
	* @param int $SequenceTotal
	*/
	public function setSequenceTotal($SequenceTotal)
	{ $this->SequenceTotal = $SequenceTotal; }

	/**
	 * The on-disk filename of this CacheImage, in- or excluding the Kind-prefix
	 * @param bool $omitPrefix
	 * @param bool $omitSequence
	 * @return string
	 */
	public function getFilenameOnDisk($omitPrefix = FALSE, $omitSequence = FALSE)
	{
		global $argv, $argc;
		$pathPrefix = (isset($argv) && $argc > 0) ? dirname($_SERVER['PHP_SELF']).'/' : '';
		
		return sprintf(
			$pathPrefix.'cache/%1$s%2$s%3$s.jpg',
			$omitPrefix ? '' : self::getFilenamePrefix(),
			$this->getID(),
			$omitSequence || $this->Kind != CACHEIMAGE_KIND_INDEX  ? '' :
				sprintf('-%1$d-%2$d', $this->SequenceNumber, $this->SequenceTotal)
		);
	}
	
	/**
	 * Translates this CacheImage's Kind to a string prefix, used for storage on-disk.
	 * @return string
	 */
	private function getFilenamePrefix()
	{
		$s = NULL;
		switch ($this->Kind)
		{
			case CACHEIMAGE_KIND_MODEL: 	$s = 'M-'; break;
			case CACHEIMAGE_KIND_INDEX: 	$s = 'X-'; break;
			case CACHEIMAGE_KIND_SET:		$s = 'S-'; break;
			case CACHEIMAGE_KIND_IMAGE: 	$s = 'I-'; break;
			case CACHEIMAGE_KIND_VIDEO: 	$s = 'V-'; break;
			
			default:
			case CACHEIMAGE_KIND_UNKNOWN: $s = ''; break;
		}
		return $s;
	}
	
	/**
	 * Gets an array of CacheImages from the database, or NULL on failure.
	 * @param $SearchParameters $SearchParameters
	 * @param string $OrderClause
	 * @param string $LimitClause
	 */
	public static function GetCacheImages($SearchParameters = NULL, $OrderClause = NULL, $LimitClause = NULL)
	{
		global $dbi;
		$SearchParameters = $SearchParameters ? $SearchParameters : new CacheImageSearchParameters();
		$OrderClause = empty($OrderClause) ? 'index_id ASC, model_id ASC, set_id ASC, image_id ASC, video_id ASC' : $OrderClause;
		
		$q = sprintf("
			SELECT
				`cache_id`, `index_id`, `model_id`, `set_id`, `image_id`, `video_id`, `cache_imagewidth`, `cache_imageheight`, `index_sequence_number`, `index_sequence_total`
			FROM
				`CacheImage`
			WHERE
				1 = 1
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
				$cache_id, $index_id, $model_id, $set_id, $image_id, $video_id,
				$cache_imagewidth, $cache_imageheight, $index_sequence_number, $index_sequence_total);
		
			while($stmt->fetch())
			{
				$o = new self(
					$cache_id, $index_id, $model_id, $set_id, $image_id, $video_id,
					$cache_imagewidth, $cache_imageheight, $index_sequence_number, $index_sequence_total);
			
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
	 * Inserts the given cacheimage into the database.
	 * @param CacheImage $CacheImage
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function Insert($CacheImage, $CurrentUser)
	{
		return self::InsertMulti(array($CacheImage), $CurrentUser);
	}
	
	/**
	* Inserts the given cacheimages into the database.
	* @param array(CacheImage) $CacheImages
	* @param User $CurrentUser
	* @return bool
	*/
	public static function InsertMulti($CacheImages, $CurrentUser)
	{
		global $dbi;
		$outBool = TRUE;
		
		if(!is_array($CacheImages))
		{ return FALSE; }
		
		$q = sprintf("
			INSERT INTO	`CacheImage` (
				`cache_id`,
				`model_id`,
				`index_id`,
				`set_id`,
				`image_id`,
				`video_id`,
				`cache_imagewidth`,
				`cache_imageheight`,
				`index_sequence_number`,
				`index_sequence_total`
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
		
		$stmt->bind_param('siiiiiiiii',
			$cache_id,
			$model_id,
			$index_id,
			$set_id,
			$image_id,
			$video_id,
			$cache_imagewidth,
			$cache_imageheight,
			$index_sequence_number,
			$index_sequence_total
		);
		
		foreach($CacheImages as $CacheImage)
		{
			$cache_id = $CacheImage->getID();
			$model_id = $CacheImage->getModelID();
			$index_id = $CacheImage->getModelIndexID();
			$set_id = $CacheImage->getSetID();
			$image_id = $CacheImage->getImageID();
			$video_id = $CacheImage->getVideoID();
			$cache_imagewidth = $CacheImage->getImageWidth();
			$cache_imageheight = $CacheImage->getImageHeight();
			$index_sequence_number = $CacheImage->getSequenceNumber();
			$index_sequence_total = $CacheImage->getSequenceTotal();
		
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
	* Removes the specified CacheImage from the database.
	* @param CacheImage $CacheImage
	* @param User $CurrentUser
	* @return bool
	*/
	public static function Delete($CacheImage, $CurrentUser)
	{
		return self::DeleteMulti(array($CacheImage), $CurrentUser);
	}
	
	/**
	* Removes multiple CacheImages from the database.
	* @param array(CacheImage) $CacheImages
	* @param User $CurrentUser
	* @return bool
	*/
	public static function DeleteMulti($CacheImages, $CurrentUser)
	{
		global $dbi;
		$outBool = TRUE;
		
		if(!is_array($CacheImages))
		{ return FALSE; }
		
		$q = sprintf("
			DELETE FROM
				`CacheImage`
			WHERE
				`cache_id` = ?
		");
		
		if(!($stmt = $dbi->prepare($q)))
		{
			$e = new SQLerror($dbi->errno, $dbi->error);
			Error::AddError($e);
			return FALSE;
		}
		
		$stmt->bind_param('s', $id);
		
		foreach($CacheImages as $CacheImage)
		{
			$id = $CacheImage->getID();
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
	
	public static function Filter($CacheImageArray, $CacheImageKind = NULL, $ModelID = NULL, $ModelIndexID = NULL, $SetID = NULL, $ImageID = NULL, $VideoID = NULL, $CacheImageID = NULL)
	{
		$OutArray = array();
			
		/* @var $CacheImage CacheImage */
		foreach($CacheImageArray as $CacheImage)
		{
			if(
				(is_null($CacheImageKind) || $CacheImage->getKind() == $CacheImageKind)		&&
				(is_null($ModelID) || $CacheImage->getModelID() == $ModelID)				&&
				(is_null($ModelIndexID) || $CacheImage->getModelIndexID() == $ModelIndexID)	&&
				(is_null($SetID) || $CacheImage->getSetID() == $SetID)						&&
				(is_null($ImageID) || $CacheImage->getImageID() == $ImageID)				&&
				(is_null($VideoID) || $CacheImage->getVideoID() == $VideoID)				&&
				(is_null($CacheImageID) || strcasecmp($CacheImage->getID(), $CacheImageID) == 0)
			){
				$OutArray[] = $CacheImage;
			}
		}
		return $OutArray;
	}
}

class CacheImageSearchParameters extends SearchParameters
{
	private $paramtypes = '';
	private $values = array();
	private $where = '';

	/**
	 * @param string $SingleID
	 * @param array(string) $MultipleIDs
	 * @param int $SingleIndexID
	 * @param array(int) $MultipleIndexIDs
	 * @param int $SingleModelID
	 * @param array(int) $MultipleModelIDs
	 * @param int $SingleSetID
	 * @param array(int) $MultipleSetIDs
	 * @param int $SingleImageID
	 * @param array(int) $MultipleImageIDs
	 * @param int $SingleVideoID
	 * @param array(int) $MultipleVideoIDs
	 * @param int $CacheImageWidth
	 * @param int $CacheImageHeight
	 * @param int $IndexSequenceNumber
	 * @param int $IndexSequenceTotal
	 */
	public function __construct(
		$SingleID = FALSE, $MultipleIDs = FALSE,
		$SingleIndexID = FALSE, $MultipleIndexIDs = FALSE,
		$SingleModelID = FALSE, $MultipleModelIDs = FALSE,
		$SingleSetID = FALSE, $MultipleSetIDs = FALSE,
		$SingleImageID = FALSE, $MultipleImageIDs = FALSE,
		$SingleVideoID = FALSE, $MultipleVideoIDs = FALSE,
		$CacheImageWidth = FALSE, $CacheImageHeight = FALSE,
		$IndexSequenceNumber = FALSE, $IndexSequenceTotal = FALSE)
	{
		parent::__construct();

		if($SingleID !== FALSE)
		{
			$this->paramtypes .= "s";
			$this->values[] = $SingleID;
			$this->where .= " AND cache_id = ?";
		}

		if(is_array($MultipleIDs) && count($MultipleIDs) > 0)
		{
			$this->paramtypes .= str_repeat('s', count($MultipleIDs));
			$this->values = array_merge($this->values, $MultipleIDs);
			$this->where .= sprintf(" AND cache_id IN ( %1s ) ",
				implode(', ', array_fill(0, count($MultipleIDs), '?'))
			);
		}

		if($SingleIndexID !== FALSE)
		{
			$this->paramtypes .= "i";
			$this->values[] = $SingleIndexID;
			$this->where .= " AND index_id = ?";
		}

		if(is_array($MultipleIndexIDs) && count($MultipleIndexIDs) > 0)
		{
			$this->paramtypes .= str_repeat('i', count($MultipleIndexIDs));
			$this->values = array_merge($this->values, $MultipleIndexIDs);
			$this->where .= sprintf(" AND index_id IN ( %1s ) ",
				implode(', ', array_fill(0, count($MultipleIndexIDs), '?'))
			);
		}

		if($SingleModelID !== FALSE)
		{
			$this->paramtypes .= "i";
			$this->values[] = $SingleModelID;
			$this->where .= " AND model_id = ?";
		}

		if(is_array($MultipleModelIDs) && count($MultipleModelIDs) > 0)
		{
			$this->paramtypes .= str_repeat('i', count($MultipleModelIDs));
			$this->values = array_merge($this->values, $MultipleModelIDs);
			$this->where .= sprintf(" AND model_id IN ( %1s ) ",
				implode(', ', array_fill(0, count($MultipleModelIDs), '?'))
			);
		}

		if($SingleSetID !== FALSE)
		{
			$this->paramtypes .= "i";
			$this->values[] = $SingleSetID;
			$this->where .= " AND set_id = ?";
		}

		if(is_array($MultipleSetIDs) && count($MultipleSetIDs) > 0)
		{
			$this->paramtypes .= str_repeat('i', count($MultipleSetIDs));
			$this->values = array_merge($this->values, $MultipleSetIDs);
			$this->where .= sprintf(" AND set_id IN ( %1s ) ",
				implode(', ', array_fill(0, count($MultipleSetIDs), '?'))
			);
		}
		
		if($SingleImageID !== FALSE)
		{
			$this->paramtypes .= "i";
			$this->values[] = $SingleImageID;
			$this->where .= " AND image_id = ?";
		}
		
		if(is_array($MultipleImageIDs) && count($MultipleImageIDs) > 0)
		{
			$this->paramtypes .= str_repeat('i', count($MultipleImageIDs));
			$this->values = array_merge($this->values, $MultipleImageIDs);
			$this->where .= sprintf(" AND image_id IN ( %1s ) ",
				implode(', ', array_fill(0, count($MultipleImageIDs), '?'))
			);
		}
		
		if($SingleVideoID !== FALSE)
		{
			$this->paramtypes .= "i";
			$this->values[] = $SingleVideoID;
			$this->where .= " AND video_id = ?";
		}
		
		if(is_array($MultipleVideoIDs) && count($MultipleVideoIDs) > 0)
		{
			$this->paramtypes .= str_repeat('i', count($MultipleVideoIDs));
			$this->values = array_merge($this->values, $MultipleVideoIDs);
			$this->where .= sprintf(" AND video_id IN ( %1s ) ",
				implode(', ', array_fill(0, count($MultipleVideoIDs), '?'))
			);
		}
		
		if($CacheImageWidth !== FALSE)
		{
			$this->paramtypes .= "i";
			$this->values[] = $CacheImageWidth;
			$this->where .= " AND cache_imagewidth = ?";
		}
		
		if($CacheImageHeight !== FALSE)
		{
			$this->paramtypes .= "i";
			$this->values[] = $CacheImageHeight;
			$this->where .= " AND cache_imageheight = ?";
		}
		
		if($IndexSequenceNumber !== FALSE)
		{
			$this->paramtypes .= "i";
			$this->values[] = $IndexSequenceNumber;
			$this->where .= " AND index_sequence_number = ?";
		}
		
		if($IndexSequenceTotal !== FALSE)
		{
			$this->paramtypes .= "i";
			$this->values[] = $IndexSequenceTotal;
			$this->where .= " AND index_sequence_total = ?";
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