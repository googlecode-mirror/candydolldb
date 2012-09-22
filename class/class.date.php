<?php

class Date
{
	private $ID;
	private $Set;
	private $DateKind = DATE_KIND_UNKNOWN;
	private $TimeStamp = -1;
	
	/**
	 * @param int $date_id
	 * @param int $date_kind
	 * @param int $date_timestamp
	 * @param int $set_id
	 * @param string $set_prefix
	 * @param string $set_name
	 * @param int $set_containswhat
	 * @param int $model_id
	 * @param string $model_firstname
	 * @param string $model_lastname
	 */
	public function __construct(
		$date_id = NULL, $date_kind = DATE_KIND_UNKNOWN, $date_timestamp = -1,
		$set_id = NULL, $set_prefix = NULL, $set_name = NULL, $set_containswhat = SET_CONTENT_NONE,
		$model_id = NULL, $model_firstname = NULL, $model_lastname = NULL)
	{
		$this->ID = $date_id;
		$this->DateKind = $date_kind;
		$this->TimeStamp = $date_timestamp;
		
		/* @var $s Set */
		$s = new Set($set_id, $set_prefix, $set_name, $set_containswhat, $model_id, $model_firstname, $model_lastname);
		
		$this->Set = $s;
	}
	
	/**
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
	 * @return Set
	 */
	public function getSet()
	{ return $this->Set; }
	
	/**
	 * @return int
	 */
	public function getSetID()
	{ return $this->Set ? $this->Set->getID() : NULL; }
	
	/**
	 * @param Set $Set
	 */
	public function setSet($Set)
	{ $this->Set = $Set; }
	
	/**
	 * @return int
	 */
	public function getTimeStamp()
	{ return $this->TimeStamp; }
	
	/**
	 * @param int $TimeStamp
	 */
	public function setTimeStamp($TimeStamp)
	{ $this->TimeStamp = $TimeStamp; }
	
	/**
	 * @return int
	 */
	public function getDateKind()
	{ return $this->DateKind; }
	
	/**
	 * @param int $DateKind
	 */
	public function setDateKind($DateKind)
	{ $this->DateKind = $DateKind; }
	
	
	/**
	 * Gets an array of Dates from the database, or NULL on failure.
	 * @param DateSearchParameters $SearchParameters
	 * @param string $OrderClause
	 * @param string $LimitClause
	 * @return Array(Date) | NULL
	 */
	public static function GetDates($SearchParameters = NULL, $OrderClause = NULL, $LimitClause = NULL)
	{
		global $dbi;
		$SearchParameters = $SearchParameters ? $SearchParameters : new DateSearchParameters();
		$OrderClause = empty($OrderClause) ? 'model_firstname ASC, model_lastname ASC, set_prefix ASC, set_name ASC, date_timestamp ASC' : $OrderClause;
		
		$q = sprintf("
			SELECT
				`date_id`, `date_kind`, `date_timestamp`,
				`set_id`, `set_prefix`, `set_name`, `set_containswhat`,
				`model_id`, `model_firstname`, `model_lastname`
			FROM
				`vw_Date`
			WHERE
				mut_deleted = -1
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
					$date_id, $date_kind, $date_timestamp,
					$set_id, $set_prefix, $set_name, $set_containswhat,
					$model_id, $model_firstname, $model_lastname);
			
			while($stmt->fetch())
			{
				$o = new self(
					$date_id, $date_kind, $date_timestamp,
					$set_id, $set_prefix, $set_name, $set_containswhat,
					$model_id, $model_firstname, $model_lastname);
				
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
	 * Inserts the given date into the database.
	 * @param Date $Date
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function Insert($Date, $CurrentUser)
	{
		return self::InsertMulti(array($Date), $CurrentUser);
	}
	
	/**
	 * Inserts the given dates into the database.
	 * @param array(Date) $Date
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function InsertMulti($Dates, $CurrentUser)
	{
		global $dbi;
		
		$outBool = TRUE;
		$set_id = $date_kind = $date_timestamp = NULL;
		$mut_id = $CurrentUser->getID();
		$mut_date = time();
		
		if(!is_array($Dates))
		{ return FALSE; }
		
		$q = sprintf("
			INSERT INTO	`Date` (
				`set_id`,
				`date_kind`,
				`date_timestamp`,
				`mut_id`,
				`mut_date`
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
			$set_id,
			$date_kind,
			$date_timestamp,
			$mut_id,
			$mut_date
		);
		
		foreach($Dates as $Date)
		{
			$set_id = $Date->getSetID();
			$date_kind = $Date->getDateKind();
			$date_timestamp = $Date->getTimeStamp();
		
			$outBool = $stmt->execute(); 
			if($outBool)
			{
				$Date->setID($dbi->insert_id);
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
	 * Updates the databaserecord of supplied Date.
	 * @param Date $Date
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function Update($Date, $CurrentUser)
	{
		return self::UpdateMulti(array($Date), $CurrentUser);
	}
	
	/**
	 * Updates the databaserecords of supplied Dates.
	 * @param array(Date) $Date
	 * @param User $CurrentUser 
	 * @return bool
	 */
	public static function UpdateMulti($Dates, $CurrentUser)
	{
		global $dbi;
		$outBool = TRUE;

		$id = $set_id = $date_kind = $date_timestamp = NULL;
		$mut_id = $CurrentUser->getID();
		$mut_date = time();
		
		if(!is_array($Dates))
		{ return FALSE; }
		
		$q = sprintf("
			UPDATE
				`Date`
			SET
				`set_id` = ?,
				`date_kind` = ?,
				`date_timestamp` = ?,
				`mut_id` = ?,
				`mut_date` = ?
			WHERE
				`date_id` = ?
		");
		
		if(!($stmt = $dbi->prepare($q)))
		{
			$e = new SQLerror($dbi->errno, $dbi->error);
			Error::AddError($e);
			return FALSE;
		}
		
		$stmt->bind_param('iiiiii', $set_id, $date_kind, $date_timestamp, $mut_id, $mut_date, $id);
		
		foreach($Dates as $Date)
		{
			$set_id = $Date->getSetID();
			$date_kind = $Date->getDateKind();
			$date_timestamp = $Date->getTimeStamp();
			$id = $Date->getID();
			
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
	 * Removes the specified Date from the database.
	 * @param Date $Dates
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function Delete($Date, $CurrentUser)
	{
		return self::DeleteMulti(array($Date), $CurrentUser);
	}
	
	/**
	 * Removes the specified Dates from the database.
	 * @param array(Date) $Dates
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function DeleteMulti($Dates, $CurrentUser)
	{
		global $dbi;
		
		$outBool = TRUE;
		$id = NULL;
		$mut_id = $CurrentUser->getID();
		$mut_deleted = time();
		
		if(!is_array($Dates))
		{ return FALSE; }
		
		$q = sprintf("
			UPDATE 
				`Date`
			SET
				`mut_id` = ?,
				`mut_deleted` = ?
			WHERE
				`date_id` = ?
		");
		
		if(!($stmt = $dbi->prepare($q)))
		{
			$e = new SQLerror($dbi->errno, $dbi->error);
			Error::AddError($e);
			return FALSE;
		}
		
		$stmt->bind_param('iii', $mut_id, $mut_deleted, $id);
		
		foreach($Dates as $Date)
		{
			$id = $Date->getID();
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
	 * Filters an array of Dates and returns those that match the given properties.
	 * @param array $DateArray
	 * @param int $DateID
	 * @param int $ModelID
	 * @param int $SetID
	 * @param int $Kind
	 * @param int $TimeStamp
	 * @return array(Date)
	 */
	public static function FilterDates($DateArray, $DateID = NULL, $ModelID = NULL, $SetID = NULL, $Kind = NULL, $TimeStamp = NULL)
	{
		$OutArray = array();
			
		/* @var $Date Date */
		foreach($DateArray as $Date)
		{
			if(
				(is_null($DateID) || $Date->getID() == $DateID)							&&
				(is_null($ModelID) || $Date->getSet()->getModel()->getID() == $ModelID)	&&
				(is_null($SetID) || $Date->getSet()->getID() == $SetID)					&&
				(is_null($Kind) || $Date->getDateKind() == $Kind)						&&
				(is_null($TimeStamp) || $Date->getTimeStamp() == $TimeStamp)				
			){
				$OutArray[] = $Date;
			}
		}
		return $OutArray;
	}
	
	/**
	 * Formats the given Dates into one string 
	 * @param array(Date) $InArray
	 * @param string $DateFormat
	 * @param bool $PrefixType
	 * @param string $Glue
	 * @return string
	 */
	public static function FormatDates($InArray, $DateFormat, $PrefixType = FALSE, $Glue = ', ')
	{
		$OutString = NULL;
		if(is_array($InArray) && count($InArray) > 0)
		{
			/* @var $Date Date */
			foreach ($InArray as $Date)
			{
				if($Date->getTimeStamp() > 0)
				{
					$OutString .= $PrefixType ? ($Date->getDateKind() == DATE_KIND_VIDEO ? 'V-' : 'P-') : NULL; 
					$OutString .= date($DateFormat, $Date->getTimeStamp()).$Glue;
				}
			}
		}
		return trim($OutString, ', ');
	}
	
	/**
	* Parses an array of strings into an array of Date objects.
	* @param array(string) $InArray
	* @param int $DateKind
	* @param Set $Set
	* @return array(Date)
	*/
	public static function ParseDates($InArray, $DateKind = DATE_KIND_UNKNOWN, $Set = NULL)
	{
		$OutArray = array();
		if(is_array($InArray) && count($InArray) > 0)
		{
			for ($i = 0; $i < count($InArray); $i++)
			{
				$timestamp = strtotime($InArray[$i]);
				if($timestamp !== FALSE)
				{
					/* @var $Date Date */
					$Date = new self();
	
					$Date->setSet($Set);
					$Date->setDateKind($DateKind);
					$Date->setTimeStamp($timestamp);
	
					$OutArray[] = $Date;
				}
			}
		}
		return $OutArray;
	}
	
	/**
	 * Filters the supplied array for the Date with the lowest timestamp 
	 * @param array(Date) $InArray
	 * @return Date
	 */
	public static function SmallestDate($InArray)
	{
		return array_reduce($InArray, function($d1, $d2){
			if(is_null($d1)) { return $d2; }
			if(is_null($d2)) { return $d1; }
			return ($d1->getTimeStamp() < $d2->getTimeStamp() ? $d1 : $d2);
		});
	}
	
	/**
	 * Filters the supplied array for the Date with the highest timestamp
	 * @param array(Date) $InArray
	 * @return Date
	 */
	public static function LargestDate($InArray)
	{
		return array_reduce($InArray, function($d1, $d2){
			if(is_null($d1)) { return $d2; }
			if(is_null($d2)) { return $d1; }
			return ($d1->getTimeStamp() > $d2->getTimeStamp() ? $d1 : $d2);
		});
	}
}

class DateSearchParameters extends SearchParameters
{
	private $paramtypes = '';
	private $values = array();
	private $where = '';

	/**
	 * @param int $SingleID
	 * @param array(int) $MultipleIDs
	 * @param int $SingleSetID
	 * @param array(int) $MultipleSetIDs
	 * @param int $SingleModelID
	 * @param array(int) $MultipleModelIDs
	 * @param int $DateKind
	 * @param string $FullName
	 */
	public function __construct(
		$SingleID = FALSE, $MultipleIDs = FALSE,
		$SingleSetID = FALSE, $MultipleSetIDs = FALSE,
		$SingleModelID = FALSE, $MultipleModelIDs = FALSE,
		$DateKind = FALSE, $FullName = FALSE)
	{
		parent::__construct();

		if($SingleID !== FALSE)
		{
			$this->paramtypes .= "i";
			$this->values[] = $SingleID;
			$this->where .= " AND date_id = ?";
		}

		if(is_array($MultipleIDs) && count($MultipleIDs) > 0)
		{
			$this->paramtypes .= str_repeat('i', count($MultipleIDs));
			$this->values = array_merge($this->values, $MultipleIDs);
			$this->where .= sprintf(" AND date_id IN ( %1s ) ",
					implode(', ', array_fill(0, count($MultipleIDs), '?'))
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
		
		if($DateKind !== FALSE)
		{
			$this->paramtypes .= "i";
			$this->values[] = $DateKind;
			$this->where .= " AND date_kind = ?";
		}
		
		if($FullName !== FALSE)
		{
			$this->paramtypes .= 's';
			$this->values[] = '%'.$FullName.'%';
			$this->where .= " AND CONCAT_WS(' ', model_firstname, model_lastname) LIKE ?";
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