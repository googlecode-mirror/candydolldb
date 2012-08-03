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
		
		DBi::BindParamsToSelect($SearchParameters, $stmt);
		
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
	public static function Filter($Tags, $TagID = null, $TagName = null)
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
	public static function FilterByCSV($Tags, $input)
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
	* @return bool
	*/
	public static function Insert($Tag, $CurrentUser)
	{
		return self::InsertMulti(array($Tag), $CurrentUser);
	}
	
	/**
	 * Inserts the given tags into the database.
	 * @param array(Tag) $Tags
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function InsertMulti($Tags, $CurrentUser)
	{
		global $dbi;
	
		$outBool = true;
		$tag_name = null;
		$mut_id = $CurrentUser->getID();
		$mut_date = time();
	
		if(!is_array($Tags))
		{ return false; }
	
		$q = sprintf("
			INSERT INTO	`Tag` (
				`tag_name`,
				`mut_id`,
				`mut_date`
			) VALUES (
				?, ?, ?
			)
		");
	
		if(!($stmt = $dbi->prepare($q)))
		{
			$e = new SQLerror($dbi->errno, $dbi->error);
			Error::AddError($e);
			return false;
		}
	
		$stmt->bind_param('sii',
			$tag_name,
			$mut_id,
			$mut_date
		);
	
		foreach($Tags as $Tag)
		{
			$tag_name = $Tag->getName();
			$outBool = $stmt->execute();

			if($outBool)
			{
				$Tag->setID($dbi->insert_id);
			}
			else
			{
				$e = new SQLerror($dbi->errno, $dbi->error);
				Error::AddError($e);
			}
		}
	
		$stmt->close();
		return $outBool;
	}
	
	/**
	* Updates the databaserecord of supplied Tag.
	* @param Tag $Tag
	* @param User $CurrentUser
	* @return bool
	*/
	public static function Update($Tag, $CurrentUser)
	{
		return self::UpdateMulti(array($Tag), $CurrentUser);
	}
	
	/**
	 * Updates the databaserecords of supplied Tags.
	 * @param array(Tag) $Tags
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function UpdateMulti($Tags, $CurrentUser)
	{
		global $dbi;
	
		$outBool = true;
		$tag_name = $id = null;
		$mut_id = $CurrentUser->getID();
		$mut_date = time();
	
		if(!is_array($Tags))
		{ return false; }
	
		$q = sprintf("
			UPDATE `Tag` SET
				`tag_name` = ?,
				`mut_id` = ?,
				`mut_date` = ?
			WHERE
				`tag_id` = ?
		");
		
		if(!($stmt = $dbi->prepare($q)))
		{
			$e = new SQLerror($dbi->errno, $dbi->error);
			Error::AddError($e);
			return false;
		}
	
		$stmt->bind_param('siii',
			$tag_name,
			$mut_id,
			$mut_date,
			$id
		);
	
		foreach($Tags as $Tag)
		{
			$tag_name = $Tag->getName();
			$id = $Tag->getID();
			
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
	* Removes the specified Tag from the database.
	* @param Tag $Tag
	* @param User $CurrentUser
	* @return bool
	*/
	public static function Delete($Tag, $CurrentUser)
	{
		return self::DeleteMulti(array($Tag), $CurrentUser);
	}
	
	/**
	 * Removes the specified Tags from the database.
	 * @param array(Tag) $Tags
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function DeleteMulti($Tags, $CurrentUser)
	{
		global $dbi;
	
		$outBool = true;
		$id = null;
		$mut_id = $CurrentUser->getID();
		$mut_deleted = time();
	
		if(!is_array($Tags))
		{ return false; }
	
		$q = sprintf("
			UPDATE `Tag` SET
				`mut_id` = ?,
				`mut_deleted` = ?
			WHERE
				`tag_id` = ?
		");
	
		if(!($stmt = $dbi->prepare($q)))
		{
			$e = new SQLerror($dbi->errno, $dbi->error);
			Error::AddError($e);
			return false;
		}
	
		$stmt->bind_param('iii',
			$mut_id,
			$mut_deleted,
			$id
		);
	
		foreach($Tags as $Tag)
		{
			$id = $Tag->getID();
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
}

class TagSearchParameters extends SearchParameters
{
	private $paramtypes = '';
	private $values = array();
	private $where = '';

	/**
	 * @param int $SingleID
	 * @param array(int) $MultipleIDs
	 * @param string $Name
	 */
	public function __construct($SingleID = FALSE, $MultipleIDs = FALSE, $Name = FALSE)
	{
		parent::__construct();

		if($SingleID !== FALSE)
		{
			$this->paramtypes .= "i";
			$this->values[] = $SingleID;
			$this->where .= " AND tag_id = ?";
		}

		if(is_array($MultipleIDs) && count($MultipleIDs) > 0)
		{
			$this->paramtypes .= str_repeat('i', count($MultipleIDs));
			$this->values = array_merge($this->values, $MultipleIDs);
			$this->where .= sprintf(" AND tag_id IN ( %1s ) ",
					implode(', ', array_fill(0, count($MultipleIDs), '?'))
			);
		}

		if($Name !== FALSE)
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