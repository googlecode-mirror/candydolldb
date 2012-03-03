<?php

class Video
{
	private $ID;
	private $Set;
	private $FileName;
	private $FileExtension;
	private $FileSize;
	private $FileCheckSum;
	private $Tags = null;
	
	/**
	 * @param int $ID
	 * @param string $FileName
	 * @param string $FileExtension
	 */
	public function Video($ID = null, $FileName = null, $FileExtension = null)
	{
		$this->ID = $ID;
		$this->FileName = $FileName;
		$this->FileExtension = $FileExtension;
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
	* @return string
	*/
	private function getRawTags()
	{ return $this->Tags; }
	
	/**
	 * @return array
	 */
	public function getTagsArray()
	{ return Tag::GetTagArray($this->getRawTags()); }
	
	/**
	* @return string
	*/
	public function getTags()
	{ return join(', ', $this->getTagsArray()); }
	
	/**
	* @param string $Tags
	*/
	public function setTags($Tags)
	{ $this->Tags = $Tags; }
	
	
	/**
	 * @param string $WhereClause
	 * @param string $OrderClause
	 * @param string $LimitClause
	 * @return Array(Video) | NULL
	 */
	public static function GetVideos($WhereClause = 'mut_deleted = -1', $OrderClause = 'model_firstname ASC, model_lastname ASC, set_prefix ASC, set_name ASC, video_filename ASC', $LimitClause = null)
	{
		global $db;
		
		if($db->Select('vw_Video', '*', $WhereClause, $OrderClause, $LimitClause))
		{
			$OutArray = array();
			if($db->getResult())
			{
				foreach($db->getResult() as $VideoItem)
				{
					$VideoObject = new Video();
					$SetObject = new Set();
					$ModelObject = new Model();
					
					foreach($VideoItem as $ColumnKey => $ColumnValue)
					{
						switch($ColumnKey)
						{
							case 'video_id'				: $VideoObject->setID($ColumnValue); 			break;
							case 'video_filename'		: $VideoObject->setFileName($ColumnValue); 		break;
							case 'video_fileextension'	: $VideoObject->setFileExtension($ColumnValue); break;
							case 'video_filesize'		: $VideoObject->setFileSize($ColumnValue); 		break;
							case 'video_filechecksum'	: $VideoObject->setFileCheckSum($ColumnValue); 	break;
							case 'video_tags'			: $VideoObject->setTags($ColumnValue);			break;
							
							case 'set_id'			: $SetObject->setID($ColumnValue);				break;
							case 'set_prefix'		: $SetObject->setPrefix($ColumnValue);			break;
							case 'set_name'			: $SetObject->setName($ColumnValue);			break;
							case 'set_containswhat'	: $SetObject->setContainsWhat($ColumnValue);	break;
							case 'set_tags'			: $SetObject->setTags($ColumnValue);			break;
							
							case 'model_id'			: $ModelObject->setID($ColumnValue);			break;
							case 'model_firstname'	: $ModelObject->setFirstName($ColumnValue);		break;
							case 'model_lastname'	: $ModelObject->setLastName($ColumnValue);		break;
							case 'model_tags'		: $ModelObject->setTags($ColumnValue);			break;
						}
					}
					
					$SetObject->setModel($ModelObject);
					$VideoObject->setSet($SetObject);
					
					$OutArray[] = $VideoObject;
				}
			}
			return $OutArray;
		}
		else
		{ return null; }
	}
	
	/**
	 * Inserts the given video into the database.
	 *
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
		    	mysql_real_escape_string($Video->getTags()),
			    $CurrentUser->getID(),
			    time()
			),
			'set_id, video_filename, video_fileextension, video_filesize, video_filechecksum, video_tags, mut_id, mut_date'
	    );
	    
	    if($result == true)
	    { Tag::InsertStrings($Video->getTagsArray(), $CurrentUser); }
	    
	    return $result;
	}
	
	/**
	 * Updates the databaserecord of supplied Video.
	 * 
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
				'video_tags' => mysql_real_escape_string($Video->getTags()),
				'mut_id' => $CurrentUser->getID(),
				'mut_date' => time()
			),
			array(
				'video_id', $Video->getID())
		);
		
		if($result == true)
		{ Tag::InsertStrings($Video->getTagsArray(), $CurrentUser); }
		
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
?>