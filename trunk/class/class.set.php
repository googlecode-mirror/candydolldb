<?php

class Set
{
	private $ID;
	private $Model;
	private $Prefix;
	private $Name;
	private $DatesPic = array();
	private $DatesVid = array();
	private $ContainsWhat = SET_CONTENT_NONE;
	private $AmountPicsInDB = 0;
	private $AmountVidsInDB = 0;
	
	/**
	 * @param int $set_id
	 * @param string $set_prefix
	 * @param string $set_name
	 * @param int $set_containswhat
	 * @param int $model_id
	 * @param string $model_firstname
	 * @param string $model_lastname
	 * @param int $set_amount_pics_in_db
	 * @param int $set_amount_vids_in_db
	 */
	public function __construct($set_id = null, $set_prefix = null, $set_name = null, $set_containswhat = null, $model_id = null, $model_firstname = null, $model_lastname = null, $set_amount_pics_in_db = null, $set_amount_vids_in_db = null)
	{
		$this->ID = $set_id;
		$this->Prefix = $set_prefix;
		$this->Name = $set_name;
		$this->ContainsWhat = $set_containswhat;
		
		$m = new Model($model_id, $model_firstname, $model_lastname);
		$this->Model = $m;
		
		$this->AmountPicsInDB = $set_amount_pics_in_db;
		$this->AmountVidsInDB = $set_amount_vids_in_db;
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
	 * @return Model 
	 */
	public function getModel()
	{ return $this->Model; }
	
	/**
	 * @param int $Model
	 */
	public function setModel($Model)
	{ $this->Model = $Model; }
	
	/**
	 * @return string 
	 */
	public function getName()
	{ return $this->Name; }
	
	/**
	 * @param string $Name
	 */
	public function setName($Name)
	{ $this->Name = $Name; }
	
	/**
	 * @return string
	 */
	public function getPrefix()
	{ return $this->Prefix; }
	
	/**
	 * @param string $Prefix
	 */
	public function setPrefix($Prefix)
	{ $this->Prefix = $Prefix; }
	
	/**
	 * @return array(Date)
	 */
	public function getDatesPic()
	{ return $this->DatesPic; }

	/**
	 * @param array(Date) $DatesPic
	 */
	public function setDatesPic($DatesPic)
	{ $this->DatesPic = $DatesPic; }
	
	/**
	 * @return array(Date)
	 */
	public function getDatesVid()
	{ return $this->DatesVid; }
	
	/**
	 * @param array(Date) $DatesVid
	 */
	public function setDatesVid($DatesVid)
	{ $this->DatesVid = $DatesVid; }
	
	/**
	 * @return int
	 */
	public function getContainsWhat()
	{ return $this->ContainsWhat; }
	
	/**
	 * @param int $What
	 */
	public function setContainsWhat($What)
	{ $this->ContainsWhat = $What; }
	
	/**
	 * @return int
	 */
	public function getAmountPicsInDB()
	{ return $this->AmountPicsInDB; }
	
	/**
	 * @param int $AmountPicsInDB
	 */
	public function setAmountPicsInDB($AmountPicsInDB)
	{ $this->AmountPicsInDB = $AmountPicsInDB; }
	
	/**
	 * @return int
	 */
	public function getAmountVidsInDB()
	{ return $this->AmountVidsInDB; }
	
	/**
	 * @param int $AmountVidsInDB
	 */
	public function setAmountVidsInDB($AmountVidsInDB)
	{ $this->AmountVidsInDB = $AmountVidsInDB; }
	
	/**
	 * @return bool
	 */
	public function getSetIsDirtyPic()
	{
		if(($this->ContainsWhat & SET_CONTENT_IMAGE) > 0)
		{ return (($this->getAmountPicsInDB() % 25 != 0) || $this->getAmountPicsInDB() == 0); }
		else
		{ return false; }
	}
	
	/**
	 * @return bool
	 */
	public function getSetIsDirtyVid()
	{
		if(($this->ContainsWhat & SET_CONTENT_VIDEO) > 0)
		{ return ($this->getAmountVidsInDB() < 1); }
		else
		{ return false; }
	}
	
	/**
	 * @return bool
	 */
	public function getSetIsDirty()
	{
		return ($this->getSetIsDirtyPic() || $this->getSetIsDirtyVid());
	}
	
	
	/**
	 * Gets an array of Sets from the database, or NULL on failure.
	 * @param SetSearchParameters $SearchParameters
	 * @param string $OrderClause
	 * @param string $LimitClause 
	 * @return Array(Set) | NULL
	 */
	public static function GetSets($SearchParameters = null, $OrderClause = 'model_firstname ASC, model_lastname ASC, set_prefix ASC, set_name ASC', $LimitClause = null)
	{
		global $dbi;
		$SearchParameters = $SearchParameters ? $SearchParameters : new SetSearchParameters();
		$OrderClause = empty($OrderClause) ? 'model_firstname ASC, model_lastname ASC, set_prefix ASC, set_name ASC' : $OrderClause;
		
		$q = sprintf("
			SELECT
				`set_id`,`set_prefix`,`set_name`,`set_containswhat`,`model_id`,`model_firstname`,`model_lastname`,`set_amount_pics_in_db`,`set_amount_vids_in_db`
			FROM
				`vw_Set`
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
			$stmt->bind_result($set_id, $set_prefix, $set_name, $set_containswhat, $model_id, $model_firstname, $model_lastname, $set_amount_pics_in_db, $set_amount_vids_in_db);
		
			while($stmt->fetch())
			{
				$o = new Set($set_id, $set_prefix, $set_name, $set_containswhat, $model_id, $model_firstname, $model_lastname, $set_amount_pics_in_db, $set_amount_vids_in_db);
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
	 * @param Set $Set
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function InsertSet($Set, $CurrentUser)
	{
		global $db;
		
		$result = $db->Insert(
			'Set',
			array(
				$Set->getModel()->getID(),
				mysql_real_escape_string($Set->getPrefix()),
				mysql_real_escape_string($Set->getName()),
				$Set->getContainsWhat(),
				$CurrentUser->getID(),
				time()),
			'model_id, set_prefix, set_name, set_containswhat, mut_id, mut_date'
		);
		
		return $result;
	}
	
	/**
	 * Updates the databaserecord of supplied Set.
	 * 
	 * @param Set $Set
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function UpdateSet($Set, $CurrentUser)
	{
		global $db;
		
		$result = $db->Update(
			'Set',
			array(
				'model_id' => $Set->getModel()->getID(),
				'set_prefix' => mysql_real_escape_string($Set->getPrefix()),
				'set_name' => mysql_real_escape_string($Set->getName()),
				'set_containswhat' => $Set->getContainsWhat(),
				'mut_id' => $CurrentUser->getID(),
				'mut_date' => time()
			),
			array(
				'set_id', $Set->getID())
		);
		
		return $result;
	}
	
	
	/**
	 * Removes the specified Set from the database.
	 * 
	 * @param Set $Set
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function DeleteSet($Set, $CurrentUser)
	{
		global $db;
		
		return $db->Update(
			'Set',
			array(
				'mut_id' => $CurrentUser->getID(),
				'mut_deleted' => time()
			),
			array(
				'set_id', $Set->getID())
		);
	}
	
	/**
	 * Filters an array of Sets, and returns only those who match the specified criteria.
	 * @param array(Set) $SetArray
	 * @param int $ModelID
	 * @param int $SetID
	 * @param string $Name
	 * @param string $Prefix
	 * @return array(Set)
	 */
	public static function FilterSets($SetArray, $ModelID = null, $SetID = null, $Name = null, $Prefix = null)
	{
		$OutArray = array();

		/* @var $Set Set */
		foreach($SetArray as $Set)
		{
			if(
				(is_null($ModelID) || $Set->getModel()->getID() == $ModelID)				&&
				(is_null($SetID) || $Set->getID() == $SetID)						 		&&
				(is_null($Prefix) || strlen($Prefix) == 0 || $Set->getPrefix() == $Prefix)  &&
				(
					is_null($Name) ||
					strlen($Name) == 0 ||
					$Set->getName() == $Name ||
					sprintf('%1$s%2$s', $Set->getModel()->GetShortName(), $Set->getName()) == $Name
				)
			){
				$OutArray[] = $Set;
			}
		}
		return $OutArray;
	}
	
	/**
	 * Return a concatenated, condensed string of all the array's values,
	 * For example '1,2,3,4,6,7,8,10,13' becomes '1-8, 10, 13'.
	 * @param array(Set) $inArray
	 */
	public static function RangeString($inArray)
	{	
		if(!is_array($inArray) || count($inArray) == 0){
			return null;
		}
	
		$s = count($inArray) == 1 ? 'Set ' : 'Sets '; 
		
		for ($i = 0; $i < count($inArray); $i++)
		{	
			/* @var $previousSet Set */
			/* @var $currentSet Set */
			/* @var $nextSet Set */
			$previousSet = $i == 0 ? null : $inArray[$i -1];
			$currenSet = $inArray[$i];
			$nextSet = $i == count($inArray)-1 ? null : $inArray[$i +1];
			
			
			if($previousSet == null){
				$s .= (int)$currenSet->getName();	
			}
	
			else if(
				(int)$currenSet->getName() == ((int)$previousSet->getName()) + 1
				&&
				$nextSet != null
				&&
				(int)$currenSet->getName() == ((int)$nextSet->getName()) - 1)
				{
					continue;
			}
			
			else if(
				(int)$previousSet->getName() == ((int)$currenSet->getName()) -1)
				{
					$s .= '-'.((int)$currenSet->getName());
			}
			
			else if(
				(int)$previousSet->getName() != ((int)$currenSet->getName()) -1)
				{
					$s .= ', '.((int)$currenSet->getName());
			}
		}
		
		return $s;
	}
	
	/**
	 * @param Set $m
	 * @param Set $n
	 */
	public static function CompareAsc($m, $n)
	{
		if($m->getName() == $n->getName()){
			return 0;
		}
		
		$mNumeric = preg_match('/^[0-9]{2,3}$/', $m->getName());
		$nNumeric = preg_match('/^[0-9]{2,3}$/', $n->getName());
		
		if(($mNumeric && $nNumeric) || (!$mNumeric && !$nNumeric))
		{
			return strnatcasecmp($m->getName(), $n->getName());
		}
		else
		{
			return ($mNumeric ? 1 : -1);
		}
	}
}

class SetSearchParameters extends SearchParameters
{
	private $paramtypes = '';
	private $values = array();
	private $where = '';
	
	public function __construct($SingleID = null, $MultipleIDs = null, $SingleModelID = null, $MultipleModelIDs = null, $ModelFullName = null)
	{
		parent::__construct();

		if($SingleID)
		{
			$this->paramtypes .= "i";
			$this->values[] = $SingleID;
			$this->where .= " AND set_id = ?";
		}

		if($MultipleIDs)
		{
			$this->paramtypes .= str_repeat('i', count($MultipleIDs));
			$this->values = array_merge($this->values, $MultipleIDs);
			$this->where .= sprintf(" AND set_id IN ( %1s ) ",
					implode(', ', array_fill(0, count($MultipleIDs), '?'))
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

		if($ModelFullName)
		{
			$this->paramtypes .= 's';
			$this->values[] = '%'.$ModelFullName.'%';
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