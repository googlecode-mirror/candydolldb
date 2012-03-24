<?php

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
	 * Filters the supplied array for Tags having the specified ID
	 * @param array(Tag) $Tags
	 * @param int $TagID
	 * @param string $TagName
	 */
	public static function FilterTags($Tags, $TagID = null, $TagName = null)
	{
		$OutArray = array();
		
		/* @var $Tag Tag */
		foreach($Tags as $Tag)
		{
			if(
				(is_null($TagID) || $Tag->getID() == $TagID) &&
				(is_null($TagName) || strcasecmp(trim($Tag->getName()), trim($TagName)) == 0)
			){
				$OutArray[] = $Tag;
			}
		}
		
		return $OutArray;
	}
	
	/**
	* Filters the supplied tags by all CSV tags in the input string
	* @param array(Tag) $Tags
	* @param string $input
	*/
	public static function FilterTagsByCSV($Tags, $input)
	{
		global $CSVRegex;
		$OutArray = array();

		$a = preg_split($CSVRegex, $input.',', null, PREG_SPLIT_NO_EMPTY);
		
		if(!is_null($a))
		{
			foreach ($a as $q)
			{
				foreach ($Tags as $t)
				{
					if(strtoupper($t->getName()) == strtoupper($q))
					{
						$OutArray[] = $t;
					}
				}
			}
		}

		return $OutArray;
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