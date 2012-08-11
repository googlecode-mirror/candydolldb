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
	 * @param int $model_id
	 * @param string $model_firstname
	 * @param string $model_lastname
	 * @param int $model_birthdate
	 * @param string $model_remarks
	 * @param int $model_setcount
	 */
	public function __construct($model_id = NULL, $model_firstname = NULL, $model_lastname = NULL, $model_birthdate = -1, $model_remarks = NULL, $model_setcount = 0)
	{
		$this->ID = $model_id;
		$this->FirstName = $model_firstname;
		$this->LastName = $model_lastname;
		$this->BirthDate = $model_birthdate;
		$this->Remarks = $model_remarks;
		$this->SetCount = $model_setcount;
	}
	
	/**
	 * Returns a concatenation of the Model's firstname and the first character of the Model's lastname.
	 * @return string
	 */
	public function GetShortName($WithSpace = FALSE)
	{
		return sprintf('%1$s%3$s%2$s',
			$this->getFirstName(),
			substr($this->getLastName(), 0, 1),
			$WithSpace ? ' ' : NULL);
	}

	/**
	 * Returns a concatenation of the Model's first- and lastname.
	 * @return string
	 */
	public function GetFullName()
	{
		return sprintf('%1$s%2$s',
			$this->getFirstName(),
			$this->getLastName() ? ' '.$this->getLastName() : NULL);
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
			(empty($Remarks) ? NULL : preg_replace("/(?<=^|\n)[\t\v ]+/i", '', $Remarks));
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
	public function GetFileFromDisk($PortraitOnly = FALSE, $LandscapeOnly = FALSE, $SetID = FALSE)
	{
		$folderPath = sprintf('%1$s/%2$s', CANDYPATH, $this->GetFullName()); 
		if(!file_exists($folderPath)){ return NULL; }
		
		$orderClause = sprintf('RAND()');
		$limitClause = sprintf('1');
		
		$Images = Image::GetImages(
			new ImageSearchParameters(
				FALSE,
				FALSE,
				is_null($SetID) ? FALSE : $SetID,
				FALSE,
				$this->getID(),
				FALSE,
				FALSE,
				$PortraitOnly,
				$LandscapeOnly),
			$orderClause,
			$limitClause);
		
		if(!$Images)
		{
			/* Work-around for returning at least ONE image when none fit the specified aspect ratio */
			$Images = Image::GetImages(
				new ImageSearchParameters(
					FALSE,
					FALSE,
					is_null($SetID) ? FALSE : $SetID,
					FALSE,
					$this->getID()),
				$orderClause,
				$limitClause);
		}
		
		if($Images)
		{
			$Image = $Images[0];
			return $Image->getFilenameOnDisk();
		}
		else
		{
			return NULL;
		}
	}
	
	/**
	 * Gets an array of Models from the database, or NULL on failure.
	 * @param ModelSearchParameters $SearchParameters
	 * @param string $OrderClause
	 * @param string $LimitClause
	 * @return array(Model)
	 */
	public static function GetModels($SearchParameters = NULL, $OrderClause = 'model_firstname ASC, model_lastname ASC', $LimitClause = NULL)
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
			return NULL;
		}
	}
	
	/**
	 * Inserts the given model into the database.
	 * @param Model $Model
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function Insert($Model, $CurrentUser)
	{
		return self::InsertMulti(array($Model), $CurrentUser);
	}
	
	/**
	 * Inserts the given models into the database.
	 * @param array(Model) $Models
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function InsertMulti($Models, $CurrentUser)
	{
		global $dbi;
	
		$outBool = TRUE;
		$model_firstname = $model_lastname =  $model_remarks = NULL;
		$model_birthdate = -1;
		$mut_id = $CurrentUser->getID();
		$mut_date = time();
	
		if(!is_array($Models))
		{ return FALSE; }
	
		$q = sprintf("
			INSERT INTO	`Model` (
				`model_firstname`,
				`model_lastname`,
				`model_birthdate`,
				`model_remarks`, 
				`mut_id`,
				`mut_date`
			) VALUES (
				?, ?, ?, ?, ?, ?
			)
		");
	
		if(!($stmt = $dbi->prepare($q)))
		{
			$e = new SQLerror($dbi->errno, $dbi->error);
			Error::AddError($e);
			return FALSE;
		}
	
		$stmt->bind_param('ssisii',
			$model_firstname,
			$model_lastname,
			$model_birthdate,
			$model_remarks,
			$mut_id,
			$mut_date
		);
	
		foreach($Models as $Model)
		{
			$model_firstname = $Model->getFirstName();
			$model_lastname = $Model->getLastName();
			$model_birthdate = $Model->getBirthDate();
			$model_remarks = $Model->getRemarks();
			
			$outBool = $stmt->execute();
			if($outBool)
			{
				$Model->setID($dbi->insert_id);
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
	 * Updates the databaserecord of supplied Model.
	 * @param Model $Model
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function Update($Model, $CurrentUser)
	{
		return self::UpdateMulti(array($Model), $CurrentUser);
	}
	
	/**
	 * Updates the databaserecords of supplied Models.
	 * @param array(Model) $Models
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function UpdateMulti($Models, $CurrentUser)
	{
		global $dbi;
	
		$outBool = TRUE;
		$id = $model_firstname = $model_lastname =  $model_remarks = NULL;
		$model_birthdate = -1;
		$mut_id = $CurrentUser->getID();
		$mut_date = time();
	
		if(!is_array($Models))
		{ return FALSE; }
	
		$q = sprintf("
			UPDATE `Model` SET
				`model_firstname` = ?,
				`model_lastname` = ?,
				`model_birthdate` = ?,
				`model_remarks` = ?,
				`mut_id` = ?,
				`mut_date` = ?
			WHERE
				`model_id` = ?
		");
	
		if(!($stmt = $dbi->prepare($q)))
		{
			$e = new SQLerror($dbi->errno, $dbi->error);
			Error::AddError($e);
			return FALSE;
		}
	
		$stmt->bind_param('ssisiii',
			$model_firstname,
			$model_lastname,
			$model_birthdate,
			$model_remarks,
			$mut_id,
			$mut_date,
			$id
		);
	
		foreach($Models as $Model)
		{
			$model_firstname = $Model->getFirstName();
			$model_lastname = $Model->getLastName();
			$model_birthdate = $Model->getBirthDate();
			$model_remarks = $Model->getRemarks();
			$id = $Model->getID();
			
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
	 * Removes the specified Model from the database.
	 * @param Model $Model
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function Delete($Model, $CurrentUser)
	{
		return self::DeleteMulti(array($Model), $CurrentUser);
	}
	
	/**
	 * Removes the specified Models from the database.
	 * @param array(Model) $Model
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function DeleteMulti($Models, $CurrentUser)
	{
		global $dbi;
	
		$outBool = TRUE;
		$id = NULL;
		$mut_id = $CurrentUser->getID();
		$mut_deleted = time();
	
		if(!is_array($Models))
		{ return FALSE; }
	
		$q = sprintf("
			UPDATE `Model` SET
				`mut_id` = ?,
				`mut_deleted` = ?
			WHERE
				`model_id` = ?
		");
	
		if(!($stmt = $dbi->prepare($q)))
		{
			$e = new SQLerror($dbi->errno, $dbi->error);
			Error::AddError($e);
			return FALSE;
		}
	
		$stmt->bind_param('iii',
			$mut_id,
			$mut_deleted,
			$id
		);
	
		foreach($Models as $Model)
		{
			$id = $Model->getID();
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
	 * Filters an array of Models, and returns only those who match the specified criteria.
	 * @param array(Model) $ModelArray
	 * @param int $ModelID
	 * @param string $FirstName
	 * @param string $LastName
	 * @return array(Model)
	 */
	public static function Filter($ModelArray, $ModelID = NULL, $FirstName = NULL, $LastName = NULL, $ShortName = NULL)
	{
		$OutArray = array();
		$ModelID = empty($ModelID) ? FALSE : $ModelID;
		$FirstName = empty($FirstName) ? FALSE : $FirstName;
		$LastName = empty($LastName) ? FALSE : $LastName;
		$ShortName = empty($ShortName) ? FALSE : $ShortName;
		
		/* @var $Model Model */
		foreach($ModelArray as $Model)
		{
			if(
				($ModelID === FALSE || $Model->getID() === $ModelID)			&&
				($FirstName === FALSE || $Model->getFirstName() === $FirstName)	&&
				($LastName === FALSE || $Model->getLastName() === $LastName)	&&
				($ShortName === FALSE || $Model->getShortName() === $ShortName)
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
	
	/**
	 * @param int $SingleID
	 * @param array(int) $MultipleIDs
	 * @param string $FirstName
	 * @param string $LastName
	 * @param string $FullName
	 */
	public function __construct($SingleID = FALSE, $MultipleIDs = FALSE, $FirstName = FALSE, $LastName = FALSE, $FullName = FALSE)
	{
		parent::__construct();
		
		if($SingleID !== FALSE)
		{
			$this->paramtypes .= "i";
			$this->values[] = $SingleID;
			$this->where .= " AND model_id = ?";
		}
		
		if(is_array($MultipleIDs) && count($MultipleIDs) > 0)
		{
			$this->paramtypes .= str_repeat('i', count($MultipleIDs));
			$this->values = array_merge($this->values, $MultipleIDs);
			$this->where .= sprintf(" AND model_id IN ( %1s ) ",
				implode(', ', array_fill(0, count($MultipleIDs), '?'))
			);
		}
		
		if($FirstName !== FALSE)
		{
			$this->paramtypes .= 's';
			$this->values[] = '%'.$FirstName.'%';
			$this->where .= " AND model_firstname LIKE ?";
		}
		
		if($LastName !== FALSE)
		{
			$this->paramtypes .= 's';
			$this->values[] = '%'.$LastName.'%';
			$this->where .= " AND model_lastname LIKE ?";
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