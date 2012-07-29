<?php

class Video
{
	private $ID;
	private $Set;
	private $FileName;
	private $FileExtension;
	private $FileSize;
	private $FileCheckSum;
	
	/**
	 * @param int $ID
	 * @param string $FileName
	 * @param string $FileExtension
	 */
	public function __construct(
		$video_id = null, $video_filename = null, $video_fileextension = null, $video_filesize = null, $video_filechecksum = null,
		$set_id = null, $set_prefix = null, $set_name = null, $set_containswhat = null,
		$model_id = null, $model_firstname = null, $model_lastname = null)
	{
		$this->ID = $video_id;
		$this->FileName = $video_filename;
		$this->FileExtension = $video_fileextension;
		$this->FileSize = $video_filesize;
		$this->FileCheckSum = $video_filechecksum;
		
		/* @var $m Model */
		/* @var $s Set */
		$m = new Model($model_id, $model_firstname, $model_lastname);
		$s = new Set($set_id, $set_prefix, $set_name, $set_containswhat);
		
		$s->setModel($m);
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
	public static function InsertVideo($Video, $CurrentUser)
	{
	    global $db;
	    
	    $result = $db->Insert(
			'Video',
			array(
			    $Video->getSet()->getID(),
				mysql_real_escape_string($Video->getFileName()),
			    mysql_real_escape_string($Video->getFileExtension()),
			    $Video->getFileSize(),
			    mysql_real_escape_string($Video->getFileCheckSum()),
			    $CurrentUser->getID(),
			    time()
			),
			'set_id, video_filename, video_fileextension, video_filesize, video_filechecksum, mut_id, mut_date'
	    );
	    
	    return $result;
	}
	
	/**
	 * Updates the databaserecord of supplied Video.
	 * @param Video $Video
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function UpdateVideo($Video, $CurrentUser)
	{
		global $db;
		
		$result = $db->Update(
			'Video',
			array(
				'set_id' => $Video->getSet()->getID(),
				'video_filename' => mysql_real_escape_string($Video->getFileName()),
				'video_fileextension' => mysql_real_escape_string($Video->getFileExtension()),
				'video_filesize' => $Video->getFileSize(),
				'video_filechecksum' => mysql_real_escape_string($Video->getFileCheckSum()),
				'mut_id' => $CurrentUser->getID(),
				'mut_date' => time()
			),
			array(
				'video_id', $Video->getID())
		);
		
		return $result;
	}
	
	/**
	 * Removes the specified Video from the database.
	 * @param Video $Video
	 * @param User $CurrentUser
	 * @return bool
	 */
	public static function DeleteVideo($Video, $CurrentUser)
	{
		global $db;
		
		return $db->Update(
			'Video',
			array(
				'mut_id' => $CurrentUser->getID(),
				'mut_deleted' => time()
			),
			array(
				'video_id', $Video->getID())
		);
	}
	
	/**
	 * Filters an array of Videos and returns those that match the given ModelID and SetID.
	 * @param array $VideoArray
	 * @param int $ModelID
	 * @param int $SetID
	 * @param string $Filename
	 * @return array(Video)
	 */
	public static function FilterVideos($VideoArray, $ModelID = null, $SetID = null, $Filename = null)
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

	public function __construct(
			$SingleID = null, $MultipleIDs = null,
			$SingleSetID = null, $MultipleSetIDs = null,
			$SingleModelID = null, $MultipleModelIDs = null,
			$OrAllMultipleIDs = false)
	{
		parent::__construct();

		if($SingleID)
		{
			$this->paramtypes .= "i";
			$this->values[] = $SingleID;
			$this->where .= " AND video_id = ?";
		}

		if($MultipleIDs && !$OrAllMultipleIDs)
		{
			$this->paramtypes .= str_repeat('i', count($MultipleIDs));
			$this->values = array_merge($this->values, $MultipleIDs);
			$this->where .= sprintf(" AND video_id IN ( %1s ) ",
					implode(', ', array_fill(0, count($MultipleIDs), '?'))
			);
		}

		if($SingleSetID)
		{
			$this->paramtypes .= "i";
			$this->values[] = $SingleSetID;
			$this->where .= " AND set_id = ?";
		}

		if($MultipleSetIDs && !$OrAllMultipleIDs)
		{
			$this->paramtypes .= str_repeat('i', count($MultipleSetIDs));
			$this->values = array_merge($this->values, $MultipleSetIDs);
			$this->where .= sprintf(" AND set_id IN ( %1s ) ",
					implode(', ', array_fill(0, count($MultipleSetIDs), '?'))
			);
		}

		if($SingleModelID)
		{
			$this->paramtypes .= "i";
			$this->values[] = $SingleModelID;
			$this->where .= " AND model_id = ?";
		}

		if($MultipleModelIDs && !$OrAllMultipleIDs)
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

			if($MultipleIDs)
			{
				$this->paramtypes .= str_repeat('i', count($MultipleIDs));
				$this->values = array_merge($this->values, $MultipleIDs);
				$pieces[] = sprintf("video_id IN ( %1s )",
						implode(', ', array_fill(0, count($MultipleIDs), '?'))
				);
			}

			if($MultipleSetIDs)
			{
				$this->paramtypes .= str_repeat('i', count($MultipleSetIDs));
				$this->values = array_merge($this->values, $MultipleSetIDs);
				$pieces[] = sprintf("set_id IN ( %1s )",
						implode(', ', array_fill(0, count($MultipleSetIDs), '?'))
				);
			}

			if($MultipleModelIDs)
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