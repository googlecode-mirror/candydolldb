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
	private $Kind = CACHEIMAGE_KIND_UNKNOWN;
	
	public function __construct(
		$cache_id = null, $index_id = null, $model_id = null, $set_id = null, $image_id = null, $video_id = null, $cache_imagewidth = 0, $cache_imageheight = 0)
	{
		$this->ID = $cache_id ? $cache_id : Utils::UUID();

		$this->ModelIndexID = $index_id;
		$this->ModelID = $model_id;
		$this->SetID = $set_id;
		$this->ImageID = $image_id;
		$this->VideoID = $video_id;
		$this->ImageWidth = $cache_imagewidth;
		$this->ImageHeight = $cache_imageheight;
		
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
	 * The on-disk filename of this CacheImage, in- or excluding the Kind-prefix
	 * @param bool $omitPrefix
	 * @return string
	 */
	public function getFilenameOnDisk($omitPrefix = false)
	{
		global $argv, $argc;
		$pathPrefix = (isset($argv) && $argc > 0) ? dirname($_SERVER['PHP_SELF']).'/' : '';
		
		return 	sprintf(
			$pathPrefix.'cache/%1$s%2$s.jpg',
			$omitPrefix ? '' : self::getFilenamePrefix(),
			$this->getID()
		);
	}
	
	/**
	 * Translates this CacheImage's Kind to a string prefix, used for storage on-disk.
	 * @return string
	 */
	private function getFilenamePrefix()
	{
		$s = null;
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
	public static function GetCacheImages($SearchParameters = null, $OrderClause = null, $LimitClause = null)
	{
		global $dbi;
		$SearchParameters = $SearchParameters ? $SearchParameters : new CacheImageSearchParameters();
		$OrderClause = empty($OrderClause) ? 'index_id ASC, model_id ASC, set_id ASC, image_id ASC, video_id ASC' : $OrderClause;
		
		$q = sprintf("
			SELECT
				`cache_id`, `index_id`, `model_id`, `set_id`, `image_id`, `video_id`, `cache_imagewidth`, `cache_imageheight`
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
			$LimitClause ? ' LIMIT '.$LimitClause : null
		);
		
		if(!($stmt = $dbi->prepare($q)))
		{
			$e = new SQLerror($dbi->errno, $dbi->error);
			Error::AddError($e);
			return null;
		}
		
		if($SearchParameters->getValues())
		{
			$bind_names[] = $SearchParameters->getParamTypes();
			$params = $SearchParameters->getValues();
		
			for ($i=0; $i<count($params);$i++)
			{
				$bind_name = 'bind' . $i;
				$$bind_name = $params[$i];
				$bind_names[] = &$$bind_name;
			}
			call_user_func_array(array($stmt, 'bind_param'), $bind_names);
		}
		
		if($stmt->execute())
		{
			$OutArray = array();
			$stmt->bind_result($cache_id, $index_id, $model_id, $set_id, $image_id, $video_id, $cache_imagewidth, $cache_imageheight);
		
			while($stmt->fetch())
			{
				$o = new self(
					$cache_id, $index_id, $model_id, $set_id, $image_id, $video_id, $cache_imagewidth, $cache_imageheight);
			
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
	 * Inserts the given cacheimage into the database.
	 * @param CacheImage $CacheImage
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function InsertCacheImage($CacheImage, $CurrentUser)
	{
		return self::InsertCacheImages(array($CacheImage), $CurrentUser);
	}
	
	/**
	* Inserts the given cacheimages into the database.
	* @param array(CacheImage) $CacheImages
	* @param User $CurrentUser
	* @return bool
	*/
	public static function InsertCacheImages($CacheImages, $CurrentUser)
	{
		global $dbi;
		$outBool = true;
		$cache_id = $model_id = $index_id = $set_id = $image_id = $video_id = $cache_imagewidth = $cache_imageheight = null;
		
		if(!is_array($CacheImages))
		{ return false; }
		
		$q = sprintf("
			INSERT INTO	`CacheImage` (
				`cache_id`,
				`model_id`,
				`index_id`,
				`set_id`,
				`image_id`,
				`video_id`,
				`cache_imagewidth`,
				`cache_imageheight`
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?
			)
		");
		
		if(!($stmt = $dbi->prepare($q)))
		{
			$e = new SQLerror($dbi->errno, $dbi->error);
			Error::AddError($e);
			return false;
		}
		
		$stmt->bind_param('siiiiiii',
			$cache_id,
			$model_id,
			$index_id,
			$set_id,
			$image_id,
			$video_id,
			$cache_imagewidth,
			$cache_imageheight
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
	public static function DeleteImage($CacheImage, $CurrentUser)
	{
		return self::DeleteImages(array($CacheImage), $CurrentUser);
	}
	
	/**
	* Removes multiple CacheImages from the database.
	* @param array(CacheImage) $CacheImages
	* @param User $CurrentUser
	* @return bool
	*/
	public static function DeleteImages($CacheImages, $CurrentUser)
	{
		global $dbi;
		$outBool = true;
		$id = null;
		
		if(!is_array($CacheImages))
		{ return false; }
		
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
			return false;
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
	
	public static function FilterCacheImages($CacheImageArray, $CacheImageKind = null, $ModelID = null, $ModelIndexID = null, $SetID = null, $ImageID = null, $VideoID = null, $CacheImageID = null)
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

	public function __construct(
		$SingleID = null, $MultipleIDs = null,
		$SingleIndexID = null, $MultipleIndexIDs = null,
		$SingleModelID = null, $MultipleModelIDs = null,
		$SingleSetID = null, $MultipleSetIDs = null,
		$SingleImageID = null, $MultipleImageIDs = null,
		$SingleVideoID = null, $MultipleVideoIDs = null,
		$CacheImageWidth = null, $CacheImageHeight = null)
	{
		parent::__construct();

		if($SingleID)
		{
			$this->paramtypes .= "s";
			$this->values[] = $SingleID;
			$this->where .= " AND cache_id = ?";
		}

		if($MultipleIDs)
		{
			$this->paramtypes .= str_repeat('s', count($MultipleIDs));
			$this->values = array_merge($this->values, $MultipleIDs);
			$this->where .= sprintf(" AND cache_id IN ( %1s ) ",
				implode(', ', array_fill(0, count($MultipleIDs), '?'))
			);
		}

		if($SingleIndexID)
		{
			$this->paramtypes .= "i";
			$this->values[] = $SingleIndexID;
			$this->where .= " AND index_id = ?";
		}

		if($MultipleIndexIDs)
		{
			$this->paramtypes .= str_repeat('i', count($MultipleIndexIDs));
			$this->values = array_merge($this->values, $MultipleIndexIDs);
			$this->where .= sprintf(" AND index_id IN ( %1s ) ",
				implode(', ', array_fill(0, count($MultipleIndexIDs), '?'))
			);
		}

		if($SingleModelID)
		{
			$this->paramtypes .= "i";
			$this->values[] = $SingleModelID;
			$this->where .= " AND model_id = ?";
		}

		if($MultipleModelIDs)
		{
			$this->paramtypes .= str_repeat('i', count($MultipleModelIDs));
			$this->values = array_merge($this->values, $MultipleModelIDs);
			$this->where .= sprintf(" AND model_id IN ( %1s ) ",
				implode(', ', array_fill(0, count($MultipleModelIDs), '?'))
			);
		}

		if($SingleSetID)
		{
			$this->paramtypes .= "i";
			$this->values[] = $SingleSetID;
			$this->where .= " AND set_id = ?";
		}

		if($MultipleSetIDs)
		{
			$this->paramtypes .= str_repeat('i', count($MultipleSetIDs));
			$this->values = array_merge($this->values, $MultipleSetIDs);
			$this->where .= sprintf(" AND set_id IN ( %1s ) ",
				implode(', ', array_fill(0, count($MultipleSetIDs), '?'))
			);
		}
		
		if($SingleImageID)
		{
			$this->paramtypes .= "i";
			$this->values[] = $SingleImageID;
			$this->where .= " AND image_id = ?";
		}
		
		if($MultipleImageIDs)
		{
			$this->paramtypes .= str_repeat('i', count($MultipleImageIDs));
			$this->values = array_merge($this->values, $MultipleImageIDs);
			$this->where .= sprintf(" AND image_id IN ( %1s ) ",
				implode(', ', array_fill(0, count($MultipleImageIDs), '?'))
			);
		}
		
		if($SingleVideoID)
		{
			$this->paramtypes .= "i";
			$this->values[] = $SingleVideoID;
			$this->where .= " AND video_id = ?";
		}
		
		if($MultipleVideoIDs)
		{
			$this->paramtypes .= str_repeat('i', count($MultipleVideoIDs));
			$this->values = array_merge($this->values, $MultipleVideoIDs);
			$this->where .= sprintf(" AND video_id IN ( %1s ) ",
				implode(', ', array_fill(0, count($MultipleVideoIDs), '?'))
			);
		}
		
		if($CacheImageWidth)
		{
			$this->paramtypes .= "i";
			$this->values[] = $CacheImageWidth;
			$this->where .= " AND cache_imagewidth = ?";
		}
		
		if($CacheImageHeight)
		{
			$this->paramtypes .= "i";
			$this->values[] = $CacheImageHeight;
			$this->where .= " AND cache_imageheight = ?";
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