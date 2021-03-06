<?php

class Tag2All
{
	private $Tag;
	private $ModelID;
	private $SetID;
	private $ImageID;
	private $VideoID;
	
	public function __construct($tag_id = NULL, $tag_name = NULL, $model_id = NULL, $set_id = NULL, $image_id = NULL, $video_id = NULL)
	{
		/* @var $t Tag */
		$t = new Tag($tag_id, $tag_name);
		$this->Tag = $t;
		
		$this->ModelID = $model_id;
		$this->SetID = $set_id;
		$this->ImageID = $image_id;
		$this->VideoID = $video_id;
	}

	/**
	 * Get the Tag2All's Tag.
	 * @return Tag
	 */
	public function getTag()
	{ return $this->Tag; }
	
	/**
	 * @return int
	 */
	public function getTagID()
	{ return $this->Tag ? $this->Tag->getID() : NULL; }
	
	/**
	 * @param Tag $Tag
	 */
	public function setTag($Tag)
	{ $this->Tag = $Tag; }

	/**
	 * Get the Tag2All's ModelID.
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
	 * Get the Tag2All's SetID.
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
	 * Get the Tag2All's ImageID.
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
	 * Get the Tag2All's VideoID.
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
	 * Gets a parameter-pattern suitable for use in DELETE
	 * @return string
	 */
	public function getDeleteBindPattern()
	{
		return sprintf('i%1$s%2$s%3$s%4$s',
			$this->getModelID() ? 'i' : NULL,
			$this->getSetID() ? 'i' : NULL,
			$this->getImageID() ? 'i' : NULL,
			$this->getVideoID() ?  'i' : NULL
		);
	}
	
	/**
	 * Gets an array of values suitable for use in DELETE.
	 * @return array
	 */
	public function getDeleteBindValues()
	{
		$o = array($this->getTagID());
		
		if($this->getModelID()) { $o[] = $this->getModelID(); }
		if($this->getSetID()) { $o[] = $this->getSetID(); }
		if($this->getImageID()) { $o[] = $this->getImageID(); }
		if($this->getVideoID()) { $o[] = $this->getVideoID(); }

		return $o;
	}
	
	/**
	 * Inserts the given Tag2All into the database.
	 * @param Tag2All $Tag2All
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function Insert($Tag2All, $CurrentUser)
	{
		return self::InsertMulti(array($Tag2All), $CurrentUser);
	}
	
	/**
	 * Inserts the given Tag2Alls into the database.
	 * @param array(Tag2All) $Tag2Alls
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function InsertMulti($Tag2Alls, $CurrentUser)
	{
		global $dbi;
		
		$outBool = TRUE;
		
		if(!is_array($Tag2Alls))
		{ return FALSE; }
		
		$q = sprintf("
			INSERT INTO	`Tag2All` (
				`tag_id`,
				`model_id`,
				`set_id`,
				`image_id`,
				`video_id`
			) VALUES (
				?, ?, ?, ?, ?
			)
		");
		
		if(!($stmt = $dbi->prepare($q)))
		{
			$e = new SQLerror($dbi->errno, $dbi->error);
			Error::AddError($e);
			return FALSE;
		}
		
		$stmt->bind_param('iiiii',
			$tag_id,
			$model_id,
			$set_id,
			$image_id,
			$video_id
		);
		
		foreach($Tag2Alls as $t2a)
		{
			$tag_id = $t2a->getTagID();
			$model_id = $t2a->getModelID();
			$set_id = $t2a->getSetID();
			$image_id = $t2a->getImageID();
			$video_id = $t2a->getVideoID();
			
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
	* Deletes the given Tag2All from the database
	* @param Tag2All $Tag2All
	* @param User $CurrentUser
	*/
	public static function Delete($Tag2All, $CurrentUser)
	{
		return self::DeleteMulti(array($Tag2All), $CurrentUser);
	}
	
	/**
	 * Deletes the given Tag2Alls from the database.
	 * @param array(Tag2All) $Tag2Alls
	 * @param User $CurrentUser
	 */
	public static function DeleteMulti($Tag2Alls, $CurrentUser)
	{
		global $dbi;
		$outBool = TRUE;
		
		if(!is_array($Tag2Alls))
		{ return FALSE; }
		
		foreach($Tag2Alls as $t2a)
		{
			$q = sprintf("
				DELETE FROM
					`Tag2All`
				WHERE
					`tag_id` = ?
					AND `model_id` %1\$s
					AND `set_id` %2\$s
					AND `image_id` %3\$s
					AND `video_id` %4\$s ",
				
				$t2a->getModelID() ? '= ?' : 'IS NULL',
				$t2a->getSetID() ? '= ?' : 'IS NULL',
				$t2a->getImageID() ? '= ?' : 'IS NULL',
				$t2a->getVideoID() ? '= ?' : 'IS NULL'
			);
		
			if(!($stmt = $dbi->prepare($q)))
			{
				$e = new SQLerror($dbi->errno, $dbi->error);
				Error::AddError($e);
				return FALSE;
			}
			
			DBi::BindParamsToDeleteT2A($t2a, $stmt);			
			
			$outBool = $stmt->execute();
			if(!$outBool)
			{
				$e = new SQLerror($dbi->errno, $dbi->error);
				Error::AddError($e);
			}
			
			$stmt->close();
		}
		return $outBool;
	}

	/**
	 * Gets Tag2All records from the database or NULL on failure.
	 * @param Tag2AllSearchParameters $SearchParameters
	 * @param string $OrderClause
	 * @return array(Tag2All) | NULL
	 */
	public static function GetTag2Alls($SearchParameters = NULL, $OrderClause = 'tag_name ASC')
	{
		global $dbi;
		$SearchParameters = $SearchParameters ? $SearchParameters : new Tag2AllSearchParameters();
		$OrderClause = empty($OrderClause) ? 'tag_name ASC' : $OrderClause;
		
		$q = sprintf("
			SELECT
				`tag_id`, `tag_name`, `model_id`, `set_id`, `image_id`, `video_id`
			FROM
				`vw_Tag2All`
			WHERE
				1 = 1
				%1\$s
			ORDER BY
				%2\$s",
			$SearchParameters->getWhere(),
			$OrderClause
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
			$stmt->bind_result($tag_id, $tag_name, $model_id, $set_id, $image_id, $video_id);
			
			while($stmt->fetch())
			{
				$o = new self($tag_id, $tag_name, $model_id, $set_id, $image_id, $video_id);
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
	 * Filters the supplied array on its IDs.
	 * @param array $Tag2AllArray
	 * @param int $TagID
	 * @param int $ModelID
	 * @param int $SetID
	 * @param int $ImageID
	 * @param int $VideoID
	 * @return array
	 */
	public static function Filter($Tag2AllArray, $TagID = NULL, $ModelID = NULL, $SetID = NULL, $ImageID = NULL, $VideoID = NULL)
	{
		$OutArray = array();

		/* @var $t2a Tag2All */
		foreach($Tag2AllArray as $t2a)
		{
			if(
				(is_null($TagID) || $t2a->getTag()->getID() === $TagID)	&&
				($ModelID === FALSE || $t2a->getModelID() === $ModelID)	&&
				($SetID === FALSE || $t2a->getSetID()   === $SetID)		&&
				($ImageID === FALSE || $t2a->getImageID() === $ImageID)	&&
				($VideoID === FALSE || $t2a->getVideoID() === $VideoID)
			){
				$OutArray[] = $t2a;
			}
		}
		return $OutArray;
	}
	
	/**
	* Returns a CSV string of all the Tag2All's Tag-names
	* @param array $Tag2Alls
	* @return string
	*/
	public static function Tags2AllCSV($Tag2Alls)
	{
		$s = NULL;
		if(is_array($Tag2Alls))
		{
			foreach ($Tag2Alls as $t2a)
			{
				$s .= sprintf('%1$s, ', $t2a->getTag()->getName());
			}
			return trim(trim($s), ',');
		}
		return s;
	}
	
	/**
	 * Handles
	 * a) the adding of new Tags to the database,
	 * b) the deleting of all existing Tag2All for this Model|Set|Image|Video and
	 * c) inserting the appropriate Tag2All records for this Model|Set|Image|Video
	 * @param array(string) $newTags
	 * @param array(Tag2All) $Tag2AllsThisItem
	 * @param array(Tag) $TagsInDB, passed by reference
	 * @param User $CurrentUser
	 * @param int $ModelID
	 * @param int $SetID
	 * @param int $ImageID
	 * @param int $VideoID
	 * @param bool $DeleteOldTag2Alls determines whether to delete or merge existing Tag2Alls
	 */
	public static function HandleTags($newTags, $Tag2AllsThisItem, &$TagsInDB, $CurrentUser, $ModelID = NULL, $SetID = NULL, $ImageID = NULL, $VideoID = NULL, $DeleteOldTag2Alls = TRUE)
	{
		global $db;
	
		foreach(array_unique($newTags) as $string)
		{
			$tInDB = Tag::Filter($TagsInDB, NULL, $string);
	
			if(!$tInDB)
			{
				$tNew = new Tag();
				$tNew->setName(trim($string));
	
				if(Tag::Insert($tNew, $CurrentUser))
				{ $TagsInDB[] = $tNew; }
			}
		}
	
		if($DeleteOldTag2Alls)
		{
			self::DeleteMulti($Tag2AllsThisItem, $CurrentUser);
		}
	
		foreach(array_unique($newTags) as $string)
		{
			$tInDB = Tag::Filter($TagsInDB, NULL, $string);
			
			if(!$DeleteOldTag2Alls)
			{
				$ttits = self::Filter($Tag2AllsThisItem, $tInDB[0]->getID(), $ModelID, $SetID, $ImageID, $VideoID);
				
				if($ttits)
				{ continue; }
			}
	
			$t2a = new Tag2All();
			$t2a->setTag($tInDB[0]);
			
			if(!is_null($ModelID)){
				$t2a->setModelID($ModelID);
			}
			if(!is_null($SetID)){
				$t2a->setSetID($SetID);
			}
			if(!is_null($ImageID)){
				$t2a->setImageID($ImageID);
			}
			if(!is_null($VideoID)){
				$t2a->setVideoID($VideoID);
			}
	
			if($t2a->getModelID() || $t2a->getSetID() || $t2a->getImageID() || $t2a->getVideoID())
			{ Tag2All::Insert($t2a, $CurrentUser); }
		}
	}
}

class Tag2AllSearchParameters extends SearchParameters
{
	private $paramtypes = '';
	private $values = array();
	private $where = '';

	/**
	 * @param int $SingleTagID
	 * @param array(int) $MultipleTagIDs
	 * @param string $TagName
	 * @param int $SingleModelID
	 * @param array(int) $MultipleModelIDs
	 * @param int $SingleSetID
	 * @param array(int) $MultipleSetIDs
	 * @param int $SingleImageID
	 * @param array(int) $MultipleImageIDs
	 * @param int $SingleVideoID
	 * @param array(int) $MultipleVideoIDs
	 * @param bool $ModelIdIsNull
	 * @param bool $SetIdIsNull
	 * @param bool $ImageIdIsNull
	 * @param bool $VideoIdIsNull
	 */
	public function __construct(
		$SingleTagID = FALSE, $MultipleTagIDs = FALSE, $TagName = FALSE,
		$SingleModelID = FALSE, $MultipleModelIDs = FALSE,
		$SingleSetID = FALSE, $MultipleSetIDs = FALSE,
		$SingleImageID = FALSE, $MultipleImageIDs = FALSE,
		$SingleVideoID = FALSE, $MultipleVideoIDs = FALSE,
		$ModelIdIsNull = FALSE, $SetIdIsNull = FALSE, $ImageIdIsNull = FALSE, $VideoIdIsNull = FALSE)
	{
		parent::__construct();

		if($SingleTagID !== FALSE)
		{
			$this->paramtypes .= "i";
			$this->values[] = $SingleTagID;
			$this->where .= " AND tag_id = ?";
		}

		if(is_array($MultipleTagIDs) && count($MultipleTagIDs) > 0)
		{
			$this->paramtypes .= str_repeat('i', count($MultipleTagIDs));
			$this->values = array_merge($this->values, $MultipleTagIDs);
			$this->where .= sprintf(" AND tag_id IN ( %1s ) ",
				implode(', ', array_fill(0, count($MultipleTagIDs), '?'))
			);
		}

		if($TagName !== FALSE)
		{
			$this->paramtypes .= 's';
			$this->values[] = '%'.$TagName.'%';
			$this->where .= " AND tag_name LIKE ?";
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
		
		if($ModelIdIsNull)
		{
			$this->where .= " AND model_id IS NULL";
		}
		
		if($SetIdIsNull)
		{
			$this->where .= " AND set_id IS NULL";
		}
		
		if($ImageIdIsNull)
		{
			$this->where .= " AND image_id IS NULL";
		}
		
		if($VideoIdIsNull)
		{
			$this->where .= " AND video_id IS NULL";
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