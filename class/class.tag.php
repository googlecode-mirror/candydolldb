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