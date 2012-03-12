<?php

class Tag2All
{
	private $Tag;
	private $ModelID;
	private $SetID;
	private $ImageID;
	private $VideoID;

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
				$Tag2All->getModelID() ? ' AND model_id = '.$Tag2All->getModelID() : null,
				$Tag2All->getSetID()   ? ' AND set_id = '  .$Tag2All->getSetID()   : null,
				$Tag2All->getImageID() ? ' AND image_id = '.$Tag2All->getImageID() : null,
				$Tag2All->getVideoID() ? ' AND video_id = '.$Tag2All->getVideoID() : null
			)
		);
			
		return $result;
	}

	/**
	 * Gets Tag2All records from the database
	 * @param string $WhereClause
	 * @param string $OrderClause
	 * @return array(Tag2All) | NULL
	 */
	public static function GetTag2Alls($WhereClause = null, $OrderClause = 'tag_name ASC')
	{
		global $db;

		if($db->Select('vw_Tag2All', '*', $WhereClause, $OrderClause, null))
		{
			$OutArray = array();

			if($db->getResult())
			{
				foreach($db->getResult() as $Tag2AllItem)
				{
					$Tag2AllObject = new Tag2All();
					$TagObject = new Tag();

					foreach($Tag2AllItem as $ColumnKey => $ColumnValue)
					{
						switch($ColumnKey)
						{
							case 'tag_id'	: $TagObject->setID($ColumnValue);		break;
							case 'tag_name'	: $TagObject->setName($ColumnValue);	break;
								
							case 'model_id'	: $Tag2AllObject->setModelID($ColumnValue);	break;
							case 'set_id'	: $Tag2AllObject->setSetID($ColumnValue);	break;
							case 'image_id'	: $Tag2AllObject->setImageID($ColumnValue);	break;
							case 'video_id'	: $Tag2AllObject->setVideoID($ColumnValue);	break;
						}
					}
						
					$Tag2AllObject->setTag($TagObject);

					$OutArray[] = $Tag2AllObject;
				}
			}
			return $OutArray;
		}
		else
		{ return null;
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
				(is_null($TagID)   || $Tag2All->getTagID()   == $TagID)			&&
				(is_null($ModelID) || $Tag2All->getModelID() == $ModelID)		&&
				(is_null($SetID)   || $Tag2All->getSetID()   == $SetID)			&&
				(is_null($ImageID) || $Tag2All->getImageID() == $ImageID)		&&
				(is_null($VideoID) || $Tag2All->getVideoID() == $VideoID)
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
	 */
	public static function HandleTags($newTags, $Tag2AllsThisItem, &$TagsInDB, $CurrentUser, $ModelID = null, $SetID = null, $ImageID = null, $VideoID = null)
	{
		global $db;
	
		foreach($newTags as $string)
		{
			$tInDB = Tag::FilterTags($TagsInDB, null, $string);
	
			if(!$tInDB)
			{
				$tNew = new Tag();
				$tNew->setName(trim($string));
	
				Tag::InsertTag($tNew, $CurrentUser);
				$tagid = $db->GetLatestID();
				if($tagid) {
					$tNew->setID($tagid);
				}
	
				$TagsInDB[] = $tNew;
			}
		}
	
		foreach($Tag2AllsThisItem as $tti)
		{
			Tag2All::Delete($tti, $CurrentUser);
		}
	
		foreach($newTags as $string)
		{
			$tInDB = Tag::FilterTags($TagsInDB, null, $string);
	
			$t2a = new Tag2All();
			$t2a->setTag($tInDB[0]);
			
			if(!is_null($ModelID)){
				$t2a->setModelID($ModelID);
			}
			else if(!is_null($SetID)){
				$t2a->setSetID($SetID);
			}
			else if(!is_null($ImageID)){
				$t2a->setImageID($ImageID);
			}
			else if(!is_null($VideoID)){
				$t2a->setVideoID($VideoID);
			}
	
			if($t2a->getModelID() || $t2a->getSetID() || $t2a->getImageID() || $t2a->getVideoID())
			{ Tag2All::Insert($t2a, $CurrentUser); }
		}
	}
}

?>