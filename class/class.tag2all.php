<?php

class Tag2All
{
	private $Tag;
	private $ModelID;
	private $SetID;
	private $ImageID;
	private $VideoID;
	
	public function __construct($tag_id = null, $tag_name = null, $model_id = null, $set_id = null, $image_id = null, $video_id = null)
	{
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
	 * Inserts the given Tag2All into the database
	 * @param Tag2All $Tag2All
	 * @param User $CurrentUser
	 */
	public static function Insert($Tag2All, $CurrentUser)
	{
		global $db;
		 
		$result = $db->Insert(
			'Tag2All',
			array(
				$Tag2All->getTag()->getID(),
				$Tag2All->getModelID(),
				$Tag2All->getSetID(),
				$Tag2All->getImageID(),
				$Tag2All->getVideoID()
			),
			'tag_id, model_id, set_id, image_id, video_id'
		);
		 
		return $result;
	}
	
	/**
	* Deletes the given Tag2All from the database
	* @param Tag2All $Tag2All
	* @param User $CurrentUser
	*/
	public static function Delete($Tag2All, $CurrentUser)
	{
		global $db;
			
		$result = $db->Delete(
			'Tag2All',
			sprintf('tag_id = %1$d%2$s%3$s%4$s%5$s',
				$Tag2All->getTag()->getID(),
				$Tag2All->getModelID() ? ' AND model_id = '.$Tag2All->getModelID() : ' AND model_id is null',
				$Tag2All->getSetID()   ? ' AND set_id = '  .$Tag2All->getSetID()   : ' AND set_id is null',
				$Tag2All->getImageID() ? ' AND image_id = '.$Tag2All->getImageID() : ' AND image_id is null',
				$Tag2All->getVideoID() ? ' AND video_id = '.$Tag2All->getVideoID() : ' AND video_id is null'
			)
		);
			
		return $result;
	}

	/**
	 * Gets Tag2All records from the database or NULL on failure.
	 * @param Tag2AllSearchParameters $SearchParameters
	 * @param string $OrderClause
	 * @return array(Tag2All) | NULL
	 */
	public static function GetTag2Alls($SearchParameters = null, $OrderClause = 'tag_name ASC')
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
			return null;
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
			return null;
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
	public static function FilterTag2Alls($Tag2AllArray, $TagID = null, $ModelID = null, $SetID = null, $ImageID = null, $VideoID = null)
	{
		$OutArray = array();

		/* @var $Tag2All Tag2All */
		foreach($Tag2AllArray as $Tag2All)
		{
			if(
				(is_null($TagID) || $Tag2All->getTag()->getID() === $TagID)	&&
				($ModelID === FALSE || $Tag2All->getModelID() === $ModelID)	&&
				($SetID === FALSE || $Tag2All->getSetID()   === $SetID)		&&
				($ImageID === FALSE || $Tag2All->getImageID() === $ImageID)	&&
				($VideoID === FALSE || $Tag2All->getVideoID() === $VideoID)
			){
				$OutArray[] = $Tag2All;
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
		$s = null;
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
	public static function HandleTags($newTags, $Tag2AllsThisItem, &$TagsInDB, $CurrentUser, $ModelID = null, $SetID = null, $ImageID = null, $VideoID = null, $DeleteOldTag2Alls = true)
	{
		global $db;
	
		foreach(array_unique($newTags) as $string)
		{
			$tInDB = Tag::FilterTags($TagsInDB, null, $string);
	
			if(!$tInDB)
			{
				$tNew = new Tag();
				$tNew->setName(trim($string));
	
				Tag::Insert($tNew, $CurrentUser);
				$tagid = $db->GetLatestID();
				if($tagid) {
					$tNew->setID($tagid);
				}
	
				$TagsInDB[] = $tNew;
			}
		}
	
		if($DeleteOldTag2Alls)
		{
			foreach($Tag2AllsThisItem as $tti)
			{
				Tag2All::Delete($tti, $CurrentUser);
			}
		}
	
		foreach(array_unique($newTags) as $string)
		{
			$tInDB = Tag::FilterTags($TagsInDB, null, $string);
			
			if(!$DeleteOldTag2Alls)
			{
				$ttits = Tag2All::FilterTag2Alls($Tag2AllsThisItem, $tInDB[0]->getID(), $ModelID, $SetID, $ImageID, $VideoID);
				
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

	public function __construct(
		$SingleTagID = null, $MultipleTagIDs = null, $TagName = null,
		$SingleModelID = null, $MultipleModelIDs = null,
		$SingleSetID = null, $MultipleSetIDs = null,
		$SingleImageID = null, $MultipleImageIDs = null,
		$SingleVideoID = null, $MultipleVideoIDs = null,
		$ModelIdIsNull = false, $SetIdIsNull = false, $ImageIdIsNull = false, $VideoIdIsNull = false)
	{
		parent::__construct();

		if($SingleTagID)
		{
			$this->paramtypes .= "i";
			$this->values[] = $SingleTagID;
			$this->where .= " AND tag_id = ?";
		}

		if($MultipleTagIDs)
		{
			$this->paramtypes .= str_repeat('i', count($MultipleTagIDs));
			$this->values = array_merge($this->values, $MultipleTagIDs);
			$this->where .= sprintf(" AND tag_id IN ( %1s ) ",
					implode(', ', array_fill(0, count($MultipleTagIDs), '?'))
			);
		}

		if($TagName)
		{
			$this->paramtypes .= 's';
			$this->values[] = '%'.$TagName.'%';
			$this->where .= " AND tag_name LIKE ?";
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