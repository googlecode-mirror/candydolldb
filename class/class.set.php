<?php
/*	This file is part of CandyDollDB.

    CandyDollDB is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    CandyDollDB is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with CandyDollDB.  If not, see <http://www.gnu.org/licenses/>.
*/

class Set
{
	private $ID;
	private $Model;
	private $Prefix;
	private $Name;
	private $DatePic = -1;
	private $DateVid = -1;
	private $ContainsWhat = SET_CONTENT_NONE;
	private $AmountPicsInDB = 0;
	private $AmountVidsInDB = 0;
	
	/**
	 * @param int $ID
	 * @param string $Name
	 */
	public function Set($ID = null, $Name = null)
	{
		$this->ID = $ID;
		$this->Name = $Name;
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
	 * @return int 
	 */
	public function getDatePic()
	{ return $this->DatePic; }
	
	/**
	 * @param int $DatePic
	 */
	public function setDatePic($DatePic)
	{ $this->DatePic = $DatePic; }
	
	/**
	 * @return int 
	 */
	public function getDateVid()
	{ return $this->DateVid; }
	
	/**
	 * @param int $DateVid
	 */
	public function setDateVid($DateVid)
	{ $this->DateVid = $DateVid; }
	
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
	 * @param string $WhereClause
	 * @param string $OrderClause
	 * @param string $LimitClause 
	 * @return Array(Set) | NULL
	 */
	public static function GetSets($WhereClause = 'mut_deleted = -1', $OrderClause = 'model_firstname ASC, model_lastname ASC, set_prefix ASC, set_name ASC', $LimitClause = null)
	{
		global $db;
		
		if($db->Select('vw_Set', '*', $WhereClause, $OrderClause, $LimitClause))
		{
			$OutArray = array();
			
			if($db->getResult())
			{
				foreach($db->getResult() as $SetItem)
				{
					$SetObject = new Set();
					$ModelObject = new Model();
					
					foreach($SetItem as $ColumnKey => $ColumnValue)
					{
						switch($ColumnKey)
						{
							case 'set_id'				: $SetObject->setID($ColumnValue);				break;
							case 'set_prefix'			: $SetObject->setPrefix($ColumnValue);			break;
							case 'set_name'				: $SetObject->setName($ColumnValue);			break;
							case 'set_date_pic'			: $SetObject->setDatePic($ColumnValue);			break;
							case 'set_date_vid'			: $SetObject->setDateVid($ColumnValue);			break;
							case 'set_containswhat'		: $SetObject->setContainsWhat($ColumnValue);	break;
							case 'set_amount_pics_in_db': $SetObject->setAmountPicsInDB($ColumnValue);	break;
							case 'set_amount_vids_in_db': $SetObject->setAmountVidsInDB($ColumnValue);	break;
							
							case 'model_id'			: $ModelObject->setID($ColumnValue);		break;
							case 'model_firstname'	: $ModelObject->setFirstName($ColumnValue);	break;
							case 'model_lastname'	: $ModelObject->setLastName($ColumnValue);	break;
						}
					}
					
					$SetObject->setModel($ModelObject);
					
					$OutArray[] = $SetObject;
				}
			}
			return $OutArray;
		}
		else
		{ return null; }
	}
	
	/**
	 * @param Set $Set
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function InsertSet($Set, $CurrentUser)
	{
		global $db;
		
		return $db->Insert(
			'Set',
			array(
				$Set->getModel()->getID(),
				mysql_real_escape_string($Set->getPrefix()),
				mysql_real_escape_string($Set->getName()),
				$Set->getDatePic(),
				$Set->getDateVid(),
				$Set->getContainsWhat(),
				$CurrentUser->getID(),
				time()),
			'model_id, set_prefix, set_name, set_date_pic, set_date_vid, set_containswhat, mut_id, mut_date'
		);
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
		
		return $db->Update(
			'Set',
			array(
				'model_id' => $Set->getModel()->getID(),
				'set_prefix' => mysql_real_escape_string($Set->getPrefix()),
				'set_name' => mysql_real_escape_string($Set->getName()),
				'set_date_pic' => $Set->getDatePic(),
				'set_date_vid' => $Set->getDateVid(),
				'set_containswhat' => $Set->getContainsWhat(),
				'mut_id' => $CurrentUser->getID(),
				'mut_date' => time()
			),
			array(
				'set_id', $Set->getID())
		);
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
}

?>