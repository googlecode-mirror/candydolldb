<?php

class Tag
{
	private $ID;
	private $Name;
	
	public function __construct($tag_id = null, $tag_name = null)
	{
		$this->ID = $tag_id;
		$this->Name = $tag_name;	
	}
	
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
	* Gets an array of Tags from the database, or NULL on failure.
	* @param TagSearchParameters $SearchParameters
	* @param string $OrderClause
	* @param string $LimitClause
	* @return array(Tag) | NULL
	*/
	public static function GetTags($SearchParameters = null, $OrderClause = 'tag_name ASC', $LimitClause = null)
	{
		global $dbi;
		$SearchParameters = $SearchParameters ? $SearchParameters : new TagSearchParameters();
		$OrderClause = empty($OrderClause) ? 'tag_name ASC' : $OrderClause;
		
		$q = sprintf("
			SELECT
				`tag_id`, `tag_name`
			FROM
				`Tag`
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
			$stmt->bind_result($tag_id, $tag_name);
		
			while($stmt->fetch())
			{
				$o = new self($tag_id, $tag_name);
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

class TagSearchParameters extends SearchParameters
{
	private $paramtypes = '';
	private $values = array();
	private $where = '';

	public function __construct($SingleID = null, $MultipleIDs = null, $Name = null)
	{
		parent::__construct();

		if($SingleID)
		{
			$this->paramtypes .= "i";
			$this->values[] = $SingleID;
			$this->where .= " AND tag_id = ?";
		}

		if($MultipleIDs)
		{
			$this->paramtypes .= str_repeat('i', count($MultipleIDs));
			$this->values = array_merge($this->values, $MultipleIDs);
			$this->where .= sprintf(" AND tag_id IN ( %1s ) ",
					implode(', ', array_fill(0, count($MultipleIDs), '?'))
			);
		}

		if($Name)
		{
			$this->paramtypes .= 's';
			$this->values[] = '%'.$Name.'%';
			$this->where .= " AND tag_name LIKE ?";
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