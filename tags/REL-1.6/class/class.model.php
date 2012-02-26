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

class Model
{
 	private $ID;
	private $FirstName;
	private $LastName;
	private $BirthDate = -1;
	private $Remarks;
	private $SetCount = 0;
	
	/**
	 * Returns a concatenation of the Model's firstname and the first character of the Model's lastname.
	 * 
	 * @return string
	 */
	public function GetShortName($WithSpace = false)
	{
		return sprintf('%1$s%3$s%2$s',
			$this->getFirstName(),
			substr($this->getLastName(), 0, 1),
			$WithSpace ? ' ' : null);
	}

	/**
	 * Returns a concatenation of the Model's first- and lastname.
	 * 
	 * @return string
	 */
	public function GetFullName()
	{
		return sprintf('%1$s%2$s',
			$this->getFirstName(),
			$this->getLastName() ? ' '.$this->getLastName() : null);
	}
	
	
	/**
	 * Instantiates a new Model object.
	 * 
	 * @param int $ID
	 * @param string $FirstName
	 * @param string $LastName
	 */
	public function Model($ID = null, $FirstName = null, $LastName = null)
	{
		$this->ID = $ID;
		$this->FirstName = $FirstName;
		$this->LastName = $LastName;
	}
	
	/**
	 * Get the Model's ID.
	 * 
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
	 * Gets the Model's firstname.
	 * 
	 * @return string 
	 */
	public function getFirstName()
	{ return $this->FirstName; }
	
	/**
	 * @param string $FirstName
	 */
	public function setFirstName($FirstName)
	{ $this->FirstName = $FirstName; }
	
	/**
	 * Gets the Model's lastname.
	 * 
	 * @return string 
	 */
	public function getLastName()
	{ return $this->LastName; }
	
	/**
	 * @param string $LastName
	 */
	public function setLastName($LastName)
	{ $this->LastName = $LastName; }
	
	/**
	 * Gets the Model's bithdate, represented as a UNIX timstamp.
	 * 
	 * @return int 
	 */
	public function getBirthDate()
	{ return $this->BirthDate; }
	
	/**
	 * @param int $BirthDate
	 */
	public function setBirthDate($BirthDate)
	{ $this->BirthDate = $BirthDate; }

	/**
	 * Gets the Model's remarks.
	 * 
	 * @return string
	 */
	public function getRemarks()
	{ return $this->Remarks; }
	
	/**
	 * @param string $Remarks
	 */
	public function setRemarks($Remarks)
	{ $this->Remarks = preg_replace("/(?<=^|\n)[\t\v ]+/i", '', $Remarks); }
	
	/**
	 * Gets the Model's set count, defaults to 0.
	 * 
	 * @return int 
	 */
	public function getSetCount()
	{ return $this->SetCount; }
	
	/**
	 * @param int $SetCount
	 */
	public function setSetCount($SetCount)
	{ $this->SetCount = $SetCount;}
	
	/**
	 * Returns a random image-filename of the current model.
	 * @return string|NULL
	 */
	public function GetFileFromDisk($PortraitOnly = false, $LandscapeOnly = false, $SetID = null)
	{
		$folderPath = sprintf('%1$s/%2$s%3$s', CANDYIMAGEPATH, $this->GetFullName(), ($FullSetName ? '/'.$FullSetName : null)); 
		if(!file_exists($folderPath)){ return null; }
		
		$whereClause = sprintf('model_id = %1$d AND mut_deleted = -1', $this->getID());
		
		if($PortraitOnly){
			$whereClause .= ' AND image_height > image_width';
		}
		if($LandscapeOnly){
			$whereClause .= ' AND image_width > image_height';
		}
		if($SetID){
			$whereClause .= sprintf(' AND set_id = %1$d', $SetID);
		}
		
		$orderClause = sprintf('RAND()');
		$limitClause = sprintf('1');
		
		$Images = Image::GetImages($whereClause, $orderClause, $limitClause);
		if(!$Images)
		{
			/* Work-around for returning at least ONE image when none fit the specified aspect ratio */
			$whereClause = sprintf('model_id = %1$d AND mut_deleted = -1', $this->getID());
			
			if($SetID){
				$whereClause .= sprintf(' AND set_id = %1$d', $SetID);
			}
		}
		
		$Images = Image::GetImages($whereClause, $orderClause, $limitClause);
		if($Images)
		{
			$Image = $Images[0];
			return $Image->getFilenameOnDisk();
		}
		else
		{
			return null;
		}
	}

	
	/**
	 * Gets an array of Models from the database, or NULL on failure. The array can be empty.
	 * 
	 * @param string $WhereClause
	 * @param string $OrderClause
	 * @param string $LimitClause
	 * @return array(Model) | NULL
	 */
	public static function GetModels($WhereClause = 'mut_deleted = -1', $OrderClause = 'model_firstname ASC, model_lastname ASC', $LimitClause = null)
	{
		global $db;
		
		if($db->Select('vw_Model', '*', $WhereClause, $OrderClause, $LimitClause))
		{
			$OutArray = array();
			if($db->getResult())
			{
				foreach($db->getResult() as $ModelItem)
				{
					$ModelObject = new Model();
					
					foreach($ModelItem as $ColumnKey => $ColumnValue)
					{
						switch($ColumnKey)
						{
							case 'model_id'		: $ModelObject->setID($ColumnValue);		break;
							case 'model_firstname'	: $ModelObject->setFirstName($ColumnValue);	break;
							case 'model_lastname'	: $ModelObject->setLastName($ColumnValue);	break;
							case 'model_birthdate'	: $ModelObject->setBirthDate($ColumnValue);	break;
							case 'model_remarks'	: $ModelObject->setRemarks($ColumnValue);	break;
							case 'model_setcount'	: $ModelObject->setSetCount($ColumnValue);	break;
						}
					}
					
					$OutArray[] = $ModelObject;
				}
			}
			return $OutArray;
		}
		else
		{ return null; }
	}
	
	/**
	 * Inserts the given model into the database.
	 *
	 * @param Model $Model
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function InsertModel($Model, $CurrentUser)
	{
	    global $db;
	    
	    return $db->Insert(
		'Model',
		array(
			mysql_real_escape_string($Model->getFirstName()),
			mysql_real_escape_string($Model->getLastName()),
			$Model->getBirthDate(),
			mysql_real_escape_string($Model->getRemarks()),
			$CurrentUser->getID(),
			time()
		),
		'model_firstname, model_lastname, model_birthdate, model_remarks, mut_id, mut_date'
	    );
	}
	
	/**
	 * Updates the databaserecord of supplied Model.
	 * 
	 * @param Model $Model
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function UpdateModel($Model, $CurrentUser)
	{
		global $db;
		
		return $db->Update(
			'Model',
			array(
				'model_firstname' => mysql_real_escape_string($Model->getFirstName()),
				'model_lastname' => mysql_real_escape_string($Model->getLastName()),
				'model_birthdate' => $Model->getBirthDate(),
				'model_remarks' => mysql_real_escape_string($Model->getRemarks()),
				'mut_id' => $CurrentUser->getID(),
				'mut_date' => time()
			),
			array('model_id', $Model->getID())
		);
	}
	
	
	/**
	 * Removes the specified Model from the database.
	 * 
	 * @param Model $Model
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function DeleteModel($Model, $CurrentUser)
	{
		global $db;
		
		return $db->Update(
			'Model',
			array(
				'mut_id' => $CurrentUser->getID(),
				'mut_deleted' => time()
			),
			array('model_id', $Model->getID())
		);
	}

	/**
	 * Filters an array of Models, and returns only those who match the specified criteria.
	 * @param array(Model) $ModelArray
	 * @param int $ModelID
	 * @param string $FirstName
	 * @param string $LastName
	 * @return array(Model)
	 */
	public static function FilterModels($ModelArray, $ModelID = null, $FirstName = null, $LastName = null)
	{
		$OutArray = array();
		
		/* @var $Model Model */
		foreach($ModelArray as $Model)
		{
			if(
				(is_null($ModelID) || $Model->getID() == $ModelID)		&&
				(is_null($FirstName) || $Model->getFirstName() == $FirstName)	&&
				(is_null($LastName) || $Model->getLastName() == $LastName)
			){
				$OutArray[] = $Model;
			}
		}
		return $OutArray;
	}
	

}

?>