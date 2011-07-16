<?php

class Date
{
	private $ID;
	private $Set;
	private $DateKind = 0;
	private $TimeStamp = -1;
	
	
	/**
	 * @param int $ID
	 * @param Set $Set
	 * @param int $DateKind
	 * @param int $TimeStamp
	 
	 */
	public function Date($ID = null, $Set = null, $DateKind = null, $TimeStamp = null)
	{
		$this->ID = $ID;
		$this->Set = $Set;
		$this->DateKind = $DateKind;
		$this->TimeStamp = $TimeStamp;
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
	 * @param string $WhereClause
	 * @param string $OrderClause
	 * @param string $LimitClause
	 * @return Array(Date) | NULL
	 */
	public static function GetDates($WhereClause = 'mut_deleted = -1', $OrderClause = '', $LimitClause = null)
	{
		global $db;
			
		if($db->Select('vw_Date', '*', $WhereClause, $OrderClause, $LimitClause))
		{
			$OutArray = array();
			
			if($db->getResult())
			{
				foreach($db->getResult() as $DateItem)
				{
					$DateObject = new Date();
					$SetObject = new Set();
					$ModelObject = new Model();
					
					foreach($DateItem as $ColumnKey => $ColumnValue)
					{
						switch($ColumnKey)
						{
							case 'date_id'				: $DateObject->setID($ColumnValue); 		break;
							case 'date_kind'			: $DateObject->setDateKind($ColumnValue); 	break;
							case 'date_timestamp'		: $DateObject->setTimeStamp($ColumnValue);	break;
							
							case 'set_id'			: $SetObject->setID($ColumnValue);				break;
							case 'set_prefix'		: $SetObject->setPrefix($ColumnValue);			break;
							case 'set_name'			: $SetObject->setName($ColumnValue);			break;
							case 'set_containswhat'	: $SetObject->setContainsWhat($ColumnValue);	break;
							
							case 'model_id'			: $ModelObject->setID($ColumnValue);			break;
							case 'model_firstname'	: $ModelObject->setFirstName($ColumnValue);		break;
							case 'model_lastname'	: $ModelObject->setLastName($ColumnValue);		break;
						}
					}
					
					$SetObject->setModel($ModelObject);
					$DateObject->setSet($SetObject);
					
					$OutArray[] = $DateObject;
				}
			}
			return $OutArray;
		}
		else
		{ return null; }
	}
	
	/**
	 * Inserts the given date into the database.
	 * @param Date $Date
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function InsertDate($Date, $CurrentUser)
	{
	    global $db;
	    
	    return $db->Insert(
		'Date',
		array(
		    $Date->getSet()->getID(),
		    $Date->getDateKind(),
		    $Date->getTimeStamp(),
		    $CurrentUser->getID(),
		    time()
		),
		'set_id, date_kind, date_timestamp, mut_id, mut_date'
	    );
	}
	
	/**
	 * Updates the databaserecord of supplied Date.
	 * 
	 * @param Date $Date
	 * @param User $CurrentUser 
	 * @return bool
	 */
	public static function UpdateDate($Date, $CurrentUser)
	{
		global $db;
		
		return $db->Update(
			'Date',
			array(
				'set_id' => $Date->getSet()->getID(),
				'date_kind' => $Date->getDateKind(),
				'date_timestamp' => $Date->getTimeStamp(),
				'mut_id' => $CurrentUser->getID(),
				'mut_date' => time()
			),
			array(
				'date_id', $Date->getID())
		);
	}
	
	/**
	 * Removes the specified Date from the database.
	 * 
	 * @param Date $Date
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function DeleteDate($Date, $CurrentUser)
	{
		global $db;
		
		return $db->Update(
			'Date',
			array(
				'mut_id' => $CurrentUser->getID(),
				'mut_deleted' => time()),
			array(
				'date_id', $Date->getID())
		);
	}
	
	/**
	 * Filters an array of Dates and returns those that match the given ModelID and SetID.
	 * @param array $DateArray
	 * @param int $ModelID
	 * @param int $SetID
	 * @param int $Kind
	 * @param int $TimeStamp
	 * @return array(Date)
	 */
	public static function FilterDates($DateArray, $ModelID = null, $SetID = null, $Kind = null, $TimeStamp = null)
	{
		$OutArray = array();
			
		/* @var $Date Date */
		foreach($DateArray as $Date)
		{
			if(
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
	 * Parses an array of strings into an array of Date objects.
	 * @param array(string) $InArray
	 * @param int $DateKind
	 * @param Set $Set
	 * @return array(Date)
	 */
	public static function ParseDates($InArray, $DateKind = DATE_KIND_UNKNOWN, $Set = null)
	{
		$OutArray = array();
		if(is_array($InArray) && count($InArray) > 0 && is_array($InArray[0]))
		{
			for ($i = 0; $i < count($InArray[0]); $i++)
			{
				$timestamp = strtotime($InArray[0][$i]);
				if($timestamp !== false)
				{
					/* @var $Date Date */
					$Date = new Date();
					
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
	 * Formats the given Dates into a comma separated string 
	 * @param array(Date) $InArray
	 * @param string $DateFormat
	 * @return string
	 */
	public static function FormatDates($InArray, $DateFormat)
	{
		$OutString = null;
		if(is_array($InArray) && count($InArray) > 0)
		{
			/* @var $Date Date */
			foreach ($InArray as $Date)
			{ $OutString .= date($DateFormat, $Date->getTimeStamp()).', '; }
		}
		return trim($OutString, ', ');
	}
}

?>