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
	{ return $this->ImageID;  }
	
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
	 * Gets Tag2All records from the database
	 * @param string $WhereClause
	 * @param string $OrderClause
	 * @return array(Tag2All) | NULL
	 */
	public static function GetTag2Alls($WhereClause = null, $OrderClause = 'tag_name ASC')
	{
		global $db;
	
		if($db->Select('vw_Tag2All', '*', $WhereClause, $OrderClause, $LimitClause))
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
		{ return null; }
	}
}

class Tag
{
	private $ID;
	private $Name;
	
	/**
	* Get the Tag's ID.
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
	* Gets the Tag's name.
	* @return string
	*/
	public function getName()
	{ return $this->Name; }
	
	/**
	* @param string $UserName
	*/
	public function setName($Name)
	{ $this->Name = $Name; }
	
	
	/**
	* Returns a sorted string array of all comma separated pieces in the input
	* @param string $tagString
	* @return array
	*/
	public static function GetTagArray($tagString)
	{
		global $CSVRegex;
		$s = preg_split($CSVRegex, $tagString.',', null, PREG_SPLIT_NO_EMPTY);
		array_unique($s);
		sort($s);
		return $s;
	}
	

	/**
	* Gets an array of Tags from the database, or NULL on failure. The array can be empty.
	* @param string $WhereClause
	* @param string $OrderClause
	* @param string $LimitClause
	* @return array(Tag) | NULL
	*/
	public static function GetTags($WhereClause = 'mut_deleted = -1', $OrderClause = 'tag_name ASC', $LimitClause = null)
	{
		global $db;
	
		if($db->Select('Tag', '*', $WhereClause, $OrderClause, $LimitClause))
		{
			$OutArray = array();
				
			if($db->getResult())
			{
				foreach($db->getResult() as $TagItem)
				{
					$TagObject = new Tag();
						
					foreach($TagItem as $ColumnKey => $ColumnValue)
					{
						switch($ColumnKey)
						{
							case 'tag_id'	: $TagObject->setID($ColumnValue);		break;
							case 'tag_name'	: $TagObject->setName($ColumnValue);	break;
						}
					}
						
					$OutArray[] = $TagObject;
				}
			}
			return $OutArray;
		}
		else
		{ return null; }
	}
	

	/**
	 * Inserts all strings into the Tags table, ignoring any duplicates
	 * @param array $strings
	 * @param User $CurrentUser
	 */
	public static function InsertStrings($strings, $CurrentUser)
	{
		foreach ($strings as $tag)
		{
			$t = new Tag();
			$t->setName($tag);
			Tag::InsertTag($t, $CurrentUser, true);
		}
	}
	
	/**
	* Inserts the given tag into the database.
	* @param Tag $Tag
	* @param User $CurrentUser
	* @param bool $AddIgnore, for ignoring duplicate key violations
	* @return bool
	*/
	public static function InsertTag($Tag, $CurrentUser, $AddIgnore = false)
	{
		global $db;
		 
		return $db->Insert(
			'Tag',
			array(
				mysql_real_escape_string($Tag->getName()),
				$CurrentUser->getID(),
				time()
			),
			'tag_name, mut_id, mut_date',
			$AddIgnore
		);
	}
	
	/**
	* Updates the databaserecord of supplied Tag.
	* @param Tag $Tag
	* @param User $CurrentUser
	* @return bool
	*/
	public static function UpdateTag($Tag, $CurrentUser)
	{
		global $db;
	
		return $db->Update(
				'Tag',
			array(
				'tag_name' => mysql_real_escape_string($Tag->getName()),
				'mut_id' => $CurrentUser->getID(),
				'mut_date' => time()),
			array('tag_id', $Tag->getID())
		);
	}
	
	/**
	* Removes the specified Tag from the database.
	*
	* @param Tag $Tag
	* @param User $CurrentUser
	* @return bool
	*/
	public static function DeleteTag($Tag, $CurrentUser)
	{
		global $db;
	
		return $db->Update(
				'Tag',
			array(
				'mut_id' => $CurrentUser->getID(),
				'mut_deleted' => time()),
			array('tag_id', $Tag->getID())
		);
	}
}

?>