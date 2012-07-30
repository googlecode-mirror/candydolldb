<?php

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
	 * @param int $model_id
	 * @param string $model_firstname
	 * @param string $model_lastname
	 * @param int $model_birthdate
	 * @param string $model_remarks
	 * @param int $model_setcount
	 */
	public function __construct($model_id = null, $model_firstname = null, $model_lastname = null, $model_birthdate = null, $model_remarks = null, $model_setcount = null)
	{
		$this->ID = $model_id;
		$this->FirstName = $model_firstname;
		$this->LastName = $model_lastname;
		$this->BirthDate = $model_birthdate;
		$this->Remarks = $model_remarks;
		$this->SetCount = $model_setcount;
	}
	
	/**
	 * Get the Model's ID.
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
	 * @return string
	 */
	public function getRemarks()
	{ return $this->Remarks; }
	
	/**
	 * @param string $Remarks
	 */
	public function setRemarks($Remarks)
	{
		$this->Remarks =
			(empty($Remarks) ? null : preg_replace("/(?<=^|\n)[\t\v ]+/i", '', $Remarks));
	}
	
	/**
	 * Gets the Model's set count, defaults to 0.
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
		$folderPath = sprintf('%1$s/%2$s', CANDYIMAGEPATH, $this->GetFullName()); 
		if(!file_exists($folderPath)){ return null; }
		
		$orderClause = sprintf('RAND()');
		$limitClause = sprintf('1');
		
		$Images = Image::GetImages(
			new ImageSearchParameters(null, null, $SetID, null, $this->getID(), null, false, $PortraitOnly, $LandscapeOnly),
			$orderClause,
			$limitClause);
		
		if(!$Images)
		{
			/* Work-around for returning at least ONE image when none fit the specified aspect ratio */
			$Images = Image::GetImages(new ImageSearchParameters(null, null, $SetID, null, $this->getID()), $orderClause, $limitClause);
		}
		
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
	 * Gets an array of Models from the database, or NULL on failure.
	 * @param ModelSearchParameters $SearchParameters
	 * @param string $OrderClause
	 * @param string $LimitClause
	 * @return array(Model)
	 */
	public static function GetModels($SearchParameters = null, $OrderClause = 'model_firstname ASC, model_lastname ASC', $LimitClause = null)
	{
		global $dbi;
		$SearchParameters = $SearchParameters ? $SearchParameters : new ModelSearchParameters();
		$OrderClause = empty($OrderClause) ? 'model_firstname ASC, model_lastname ASC' : $OrderClause; 
		
		$q = sprintf("
				SELECT
					`model_id`,`model_firstname`,`model_lastname`,`model_birthdate`,`model_remarks`,`model_setcount`
				FROM
					`vw_Model`
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
			$stmt->bind_result($model_id, $model_firstname, $model_lastname, $model_birthdate, $model_remarks, $model_setcount);
			
			while($stmt->fetch())
			{
				$o = new Model($model_id, $model_firstname, $model_lastname, $model_birthdate, $model_remarks, $model_setcount);
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
	 * Process a DB->result() datarow into a Model object.
	 * @param array $ModelItem
	 * @return Model
	 */
	public static function ProcessDBitem($ModelItem)
	{
		$ModelObject = new Model();
			
		foreach($ModelItem as $ColumnKey => $ColumnValue)
		{
			switch($ColumnKey)
			{
				case 'model_id'			: $ModelObject->setID($ColumnValue);		break;
				case 'model_firstname'	: $ModelObject->setFirstName($ColumnValue);	break;
				case 'model_lastname'	: $ModelObject->setLastName($ColumnValue);	break;
				case 'model_birthdate'	: $ModelObject->setBirthDate($ColumnValue);	break;
				case 'model_remarks'	: $ModelObject->setRemarks($ColumnValue);	break;
				case 'model_setcount'	: $ModelObject->setSetCount($ColumnValue);	break;
			}
		}
		
		return $ModelObject;
	}
	
	/**
	 * Inserts the given model into the database.
	 * @param Model $Model
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function InsertModel($Model, $CurrentUser)
	{
	    global $db;
	    
	    $result = $db->Insert(
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
	    
	    return $result;
	}
	
	/**
	 * Updates the databaserecord of supplied Model.
	 * @param Model $Model
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function UpdateModel($Model, $CurrentUser)
	{
		global $db;
		
		$result = $db->Update(
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
		
		return $result;
	}
	
	
	/**
	 * Removes the specified Model from the database.
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
		$ModelID = empty($ModelID) ? FALSE : $ModelID;
		$FirstName = empty($FirstName) ? FALSE : $FirstName;
		$LastName = empty($LastName) ? FALSE : $LastName;
		
		/* @var $Model Model */
		foreach($ModelArray as $Model)
		{
			if(
				($ModelID === FALSE || $Model->getID() === $ModelID)			&&
				($FirstName === FALSE || $Model->getFirstName() === $FirstName)	&&
				($LastName === FALSE || $Model->getLastName() === $LastName)
			){
				$OutArray[] = $Model;
			}
		}
		return $OutArray;
	}
}

class ModelSearchParameters extends SearchParameters
{
	private $paramtypes = '';
	private $values = array();
	private $where = '';
	
	public function __construct($SingleID = null, $MultipleIDs = null, $FirstName = null, $LastName = null, $FullName = null)
	{
		parent::__construct();
		
		if($SingleID)
		{
			$this->paramtypes .= "i";
			$this->values[] = $SingleID;
			$this->where .= " AND model_id = ?";
		}
		
		if($MultipleIDs)
		{
			$this->paramtypes .= str_repeat('i', count($MultipleIDs));
			$this->values = array_merge($this->values, $MultipleIDs);
			$this->where .= sprintf(" AND model_id IN ( %1s ) ",
				implode(', ', array_fill(0, count($MultipleIDs), '?'))
			);
		}
		
		if($FirstName)
		{
			$this->paramtypes .= 's';
			$this->values[] = '%'.$FirstName.'%';
			$this->where .= " AND model_firstname LIKE ?";
		}
		
		if($LastName)
		{
			$this->paramtypes .= 's';
			$this->values[] = '%'.$LastName.'%';
			$this->where .= " AND model_lastname LIKE ?";
		}
		
		if($FullName)
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