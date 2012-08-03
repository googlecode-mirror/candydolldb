<?php

class Video
{
	private $ID;
	private $Set;
	private $FileName;
	private $FileExtension;
	private $FileSize = 0;
	private $FileCheckSum;
	
	/**
	 * @param int $video_id
	 * @param string $video_filename
	 * @param string $video_fileextension
	 * @param int $video_filesize
	 * @param string $video_filechecksum
	 * @param int $set_id
	 * @param string $set_prefix
	 * @param string$set_name
	 * @param int $set_containswhat
	 * @param int $model_id
	 * @param string $model_firstname
	 * @param string $model_lastname
	 */
	public function __construct(
		$video_id = null, $video_filename = null, $video_fileextension = null, $video_filesize = 0, $video_filechecksum = null,
		$set_id = null, $set_prefix = null, $set_name = null, $set_containswhat = SET_CONTENT_NONE,
		$model_id = null, $model_firstname = null, $model_lastname = null)
	{
		$this->ID = $video_id;
		$this->FileName = $video_filename;
		$this->FileExtension = $video_fileextension;
		$this->FileSize = $video_filesize;
		$this->FileCheckSum = $video_filechecksum;
		
		/* @var $s Set */
		$s = new Set($set_id, $set_prefix, $set_name, $set_containswhat,
			$model_id, $model_firstname, $model_lastname);
		
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
	{ return $this->Set ? $this->Set->getID() : null; }
	
	/**
	 * @param Set $Set
	 */
	public function setSet($Set)
	{ $this->Set = $Set; }
	
	/**
	 * @return string
	 */
	public function getFileName()
	{ return $this->FileName; }
	
	/**
	 * @param string $FileName
	 */
	public function setFileName($FileName)
	{ $this->FileName = $FileName; }
	
	/**
	 * @return string
	 */
	public function getFileExtension()
	{ return $this->FileExtension; }
	
	/**
	 * @param string $FileExtension
	 */
	public function setFileExtension($FileExtension)
	{ $this->FileExtension = $FileExtension; }

	/**
	 * @return int
	 */
	public function getFileSize()
	{ return $this->FileSize; }
	
	/**
	 * @param int $FileSize
	 */
	public function setFileSize($FileSize)
	{ $this->FileSize = $FileSize; }
	
	/**
	 * @return string
	 */	
	public function getFileCheckSum()
	{ return $this->FileCheckSum; }
	
	/**
	 * @param string $FileCheckSum
	 */
	public function setFileCheckSum($FileCheckSum)
	{ $this->FileCheckSum = $FileCheckSum; }
	
	/**
	 * Gets an array of Videos from the database, or NULL on failure.
	 * @param VideoSearchParameters $SearchParameters
	 * @param string $OrderClause
	 * @param string $LimitClause
	 * @return Array(Video) | NULL
	 */
	public static function GetVideos($SearchParameters = null, $OrderClause = 'model_firstname ASC, model_lastname ASC, set_prefix ASC, set_name ASC, video_filename ASC', $LimitClause = null)
	{
		global $dbi;
		$SearchParameters = $SearchParameters ? $SearchParameters : new VideoSearchParameters();
		$OrderClause = empty($OrderClause) ? 'model_firstname ASC, model_lastname ASC, set_prefix ASC, set_name ASC, video_filename ASC' : $OrderClause;
		
		$q = sprintf("
			SELECT
				`video_id`, `video_filename`, `video_fileextension`, `video_filesize`, `video_filechecksum`, 
				`set_id`, `set_prefix`, `set_name`, `set_containswhat`,
				`model_id`, `model_firstname`, `model_lastname`
			FROM
				`vw_Video`
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
			$stmt->bind_result(
				$video_id, $video_filename, $video_fileextension, $video_filesize, $video_filechecksum, 
				$set_id, $set_prefix, $set_name, $set_containswhat,
				$model_id, $model_firstname, $model_lastname);
		
			while($stmt->fetch())
			{
				$o = new Video(
					$video_id, $video_filename, $video_fileextension, $video_filesize, $video_filechecksum,
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
			return null;
		}
	}
	
	/**
	 * Inserts the given video into the database.
	 * @param Video $Video
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function Insert($Video, $CurrentUser)
	{
	    return self::InsertMulti(array($Video), $CurrentUser);
	}
	
	/**
	 * Inserts the given videos into the database.
	 * @param array(Video) $Videos
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function InsertMulti($Videos, $CurrentUser)
	{
		global $dbi;
	
		$outBool = true;
		$set_id = $video_filename = $video_fileextension = $video_filechecksum = null;
		$video_filesize = 0;
		$mut_id = $CurrentUser->getID();
		$mut_date = time();
	
		if(!is_array($Videos))
		{ return false; }
	
		$q = sprintf("
			INSERT INTO	`Video` (
				`set_id`,
				`video_filename`,
				`video_fileextension`,
				`video_filesize`,
				`video_filechecksum`,
				`mut_id`,
				`mut_date`
			) VALUES (
				?, ?, ?, ?, ?, ?, ?
			)
		");
	
		if(!($stmt = $dbi->prepare($q)))
		{
			$e = new SQLerror($dbi->errno, $dbi->error);
			Error::AddError($e);
			return false;
		}
	
		$stmt->bind_param('issisii',
			$set_id,
			$video_filename,
			$video_fileextension,
			$video_filesize,
			$video_filechecksum,
			$mut_id,
			$mut_date
		);

		foreach($Videos as $Video)
		{
			$set_id = $Video->getSetID();
			$video_filename = $Video->getFileName();
			$video_fileextension = $Video->getFileExtension();
			$video_filesize = $Video->getFileSize();
			$video_filechecksum = $Video->getFileCheckSum();
	
			$outBool = $stmt->execute();
			if($outBool)
			{
				$Video->setID($dbi->insert_id);
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
	 * Updates the databaserecord of supplied Video.
	 * @param Video $Video
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function Update($Video, $CurrentUser)
	{
		return self::UpdateMulti(array($Video), $CurrentUser);
	}
	
	/**
	 * Updates the databaserecords of supplied Videos.
	 * @param array(Video) $Videos
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function UpdateMulti($Videos, $CurrentUser)
	{
		global $dbi;
		$outBool = true;

		$id = $set_id = $video_filename = $video_fileextension = $video_filechecksum = null;
		$video_filesize = 0;
		$mut_id = $CurrentUser->getID();
		$mut_date = time();
	
		if(!is_array($Videos))
		{ return false; }
	
		$q = sprintf("
			UPDATE `Video` SET
				`set_id` = ?,
				`video_filename` = ?,
				`video_fileextension` = ?,
				`video_filesize` = ?,
				`video_filechecksum` = ?,
				`mut_id` = ?,
				`mut_date` = ?
			WHERE
				`video_id` = ?
		");
	
		if(!($stmt = $dbi->prepare($q)))
		{
			$e = new SQLerror($dbi->errno, $dbi->error);
			Error::AddError($e);
			return false;
		}
	
		$stmt->bind_param('issisiii',
			$set_id,
			$video_filename,
			$video_fileextension,
			$video_filesize,
			$video_filechecksum,
			$mut_id,
			$mut_date,
			$id
		);

		foreach($Videos as $Video)
		{
			$set_id = $Video->getSetID();
			$video_filename = $Video->getFileName();
			$video_fileextension = $Video->getFileExtension();
			$video_filesize = $Video->getFileSize();
			$video_filechecksum = $Video->getFileCheckSum();
			$id = $Video->getID();
	
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
	 * Removes the specified Video from the database.
	 * @param Video $Video
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function Delete($Video, $CurrentUser)
	{
		return self::DeleteMulti(array($Video), $CurrentUser);
	}
	
	/**
	 * Removes the specified Videos from the database.
	 * @param array(Video) $Videos
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function DeleteMulti($Videos, $CurrentUser)
	{
		global $dbi;
	
		$outBool = true;
		$id = null;
		$mut_id = $CurrentUser->getID();
		$mut_deleted = time();
	
		if(!is_array($Videos))
		{ return false; }
	
		$q = sprintf("
			UPDATE `Video` SET
				`mut_id` = ?,
				`mut_deleted` = ?
			WHERE
				`video_id` = ?
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
	
		foreach($Videos as $Video)
		{
			$id = $Video->getID();
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
	 * Filters an array of Videos and returns those that match the given ModelID and SetID.
	 * @param array $VideoArray
	 * @param int $ModelID
	 * @param int $SetID
	 * @param string $Filename
	 * @return array(Video)
	 */
	public static function Filter($VideoArray, $ModelID = null, $SetID = null, $Filename = null)
	{
		$OutArray = array();
			
		/* @var $Video Video */
		foreach($VideoArray as $Video)
		{
			if($ModelID && $SetID)
			{
				if($Video->getSet()->getModel()->getID() == $ModelID && $Video->getSet()->getID() == $SetID && (is_null($Filename) || $Video->getFileName() == $Filename))
				{ $OutArray[] = $Video; }
			}
			else if($ModelID)
			{
				if($Video->getSet()->getModel()->getID() == $ModelID && (is_null($Filename) || $Video->getFileName() == $Filename))
				{ $OutArray[] = $Video; }
			}
			else if($SetID && (is_null($Filename) || $Video->getFileName() == $Filename))
			{
				if($Video->getSet()->getID() == $SetID)
				{ $OutArray[] = $Video; }
			}
			else if(is_null($Filename) || $Video->getFileName() == $Filename)
			{ $OutArray[] = $Video; }
		}
		return $OutArray;
	}
}

class VideoSearchParameters extends SearchParameters
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
	 * @param bool $OrAllMultipleIDs
	 */
	public function __construct(
			$SingleID = FALSE, $MultipleIDs = FALSE,
			$SingleSetID = FALSE, $MultipleSetIDs = FALSE,
			$SingleModelID = FALSE, $MultipleModelIDs = FALSE,
			$OrAllMultipleIDs = FALSE)
	{
		parent::__construct();

		if($SingleID !== FALSE)
		{
			$this->paramtypes .= "i";
			$this->values[] = $SingleID;
			$this->where .= " AND video_id = ?";
		}

		if(is_array($MultipleIDs) && count($MultipleIDs) > 0 && !$OrAllMultipleIDs)
		{
			$this->paramtypes .= str_repeat('i', count($MultipleIDs));
			$this->values = array_merge($this->values, $MultipleIDs);
			$this->where .= sprintf(" AND video_id IN ( %1s ) ",
					implode(', ', array_fill(0, count($MultipleIDs), '?'))
			);
		}

		if($SingleSetID !== FALSE)
		{
			$this->paramtypes .= "i";
			$this->values[] = $SingleSetID;
			$this->where .= " AND set_id = ?";
		}

		if(is_array($MultipleSetIDs) && count($MultipleSetIDs) > 0 && !$OrAllMultipleIDs)
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

		if(is_array($MultipleModelIDs) && count($MultipleModelIDs) > 0 && !$OrAllMultipleIDs)
		{
			$this->paramtypes .= str_repeat('i', count($MultipleModelIDs));
			$this->values = array_merge($this->values, $MultipleModelIDs);
			$this->where .= sprintf(" AND model_id IN ( %1s ) ",
					implode(', ', array_fill(0, count($MultipleModelIDs), '?'))
			);
		}

		if($OrAllMultipleIDs)
		{
			$pieces = array();

			if(is_array($MultipleIDs) && count($MultipleIDs) > 0)
			{
				$this->paramtypes .= str_repeat('i', count($MultipleIDs));
				$this->values = array_merge($this->values, $MultipleIDs);
				$pieces[] = sprintf("video_id IN ( %1s )",
						implode(', ', array_fill(0, count($MultipleIDs), '?'))
				);
			}

			if(is_array($MultipleSetIDs) && count($MultipleSetIDs) > 0)
			{
				$this->paramtypes .= str_repeat('i', count($MultipleSetIDs));
				$this->values = array_merge($this->values, $MultipleSetIDs);
				$pieces[] = sprintf("set_id IN ( %1s )",
						implode(', ', array_fill(0, count($MultipleSetIDs), '?'))
				);
			}

			if(is_array($MultipleModelIDs) && count($MultipleModelIDs) > 0)
			{
				$this->paramtypes .= str_repeat('i', count($MultipleModelIDs));
				$this->values = array_merge($this->values, $MultipleModelIDs);
				$pieces[] = sprintf("model_id IN ( %1s )",
						implode(', ', array_fill(0, count($MultipleModelIDs), '?'))
				);
			}

			if($pieces)
			{
				$this->where .= " AND (";
				$this->where .= implode(' OR ', $pieces);
				$this->where .= ")";
			}
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