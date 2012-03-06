<?php

class Tag2All
{
	private $Tag;
	private $ModelID;
	private $SetID;
	private $ImageID;
	private $VideoID;

	/**
	 * Get the Tag2All's Tag.
	 * @return Tag
	 */
	public function getTag()
	{ return $this->Tag; }

	/**
	 * @param Tag $Tag
	 */
	public function setTag($Tag)
	{ $this->Tag = $Tag; }

	/**
	 * Get the Tag2All's ModelID.
	 * @return int
	 */
	public function getModelID()
	{ return $this->ModelID; }

	/**
	 * @param int $ModelID
	 */
	public function setModelID($ModelID)
	{ $this->ModelID = $ModelID; }

	/**
	 * Get the Tag2All's SetID.
	 * @return int
	 */
	public function getSetID()
	{ return $this->SetID; }

	/**
	 * @param int $SetID
	 */
	public function setSetID($SetID)
	{ $this->SetID = $SetID; }

	/**
	 * Get the Tag2All's ImageID.
	 * @return int
	 */
	public function getImageID()
	{ return $this->ImageID; }

	/**
	 * @param int $ImageID
	 */
	public function setImageID($ImageID)
	{ $this->ImageID = $ImageID; }

	/**
	 * Get the Tag2All's VideoID.
	 * @return int
	 */
	public function getVideoID()
	{ return $this->VideoID; }

	/**
	 * @param int $VideoID
	 */
	public function setVideoID($VideoID)
	{ $this->VideoID = $VideoID; }

	/**
	 * Gets Tag2All records from the database
	 * @param string $WhereClause
	 * @param string $OrderClause
	 * @return array(Tag2All) | NULL
	 */
	public static function GetTag2Alls($WhereClause = null, $OrderClause = 'tag_name ASC')
	{
		global $db;

		if($db->Select('vw_Tag2All', '*', $WhereClause, $OrderClause, $LimitClause))
		{
			$OutArray = array();

			if($db->getResult())
			{
				foreach($db->getResult() as $Tag2AllItem)
				{
					$Tag2AllObject = new Tag2All();
					$TagObject = new Tag();

					foreach($Tag2AllItem as $ColumnKey => $ColumnValue)
					{
						switch($ColumnKey)
						{
							case 'tag_id'	: $TagObject->setID($ColumnValue);		break;
							case 'tag_name'	: $TagObject->setName($ColumnValue);	break;
								
							case 'model_id'	: $Tag2AllObject->setModelID($ColumnValue);	break;
							case 'set_id'	: $Tag2AllObject->setSetID($ColumnValue);	break;
							case 'image_id'	: $Tag2AllObject->setImageID($ColumnValue);	break;
							case 'video_id'	: $Tag2AllObject->setVideoID($ColumnValue);	break;
						}
					}
						
					$Tag2AllObject->setTag($TagObject);

					$OutArray[] = $Tag2AllObject;
				}
			}
			return $OutArray;
		}
		else
		{ return null;
		}
	}

	/**
	 * Filters the supplied array on its IDs.
	 * @param array $Tag2AllArray
	 * @param int $TagID
	 * @param int $ModelID
	 * @param int $SetID
	 * @param int $ImageID
	 * @param int $VideoID
	 * @return array
	 */
	public static function FilterTag2Alls($Tag2AllArray, $TagID = null, $ModelID = null, $SetID = null, $ImageID = null, $VideoID = null)
	{
		$OutArray = array();

		/* @var $Tag2All Tag2All */
		foreach($Tag2AllArray as $Tag2All)
		{
			if(
				(is_null($TagID)   || $Tag2All->getTagID()   == $TagID)			&&
				(is_null($ModelID) || $Tag2All->getModelID() == $ModelID)		&&
				(is_null($SetID)   || $Tag2All->getSetID()   == $SetID)			&&
				(is_null($ImageID) || $Tag2All->getImageID() == $ImageID)		&&
				(is_null($VideoID) || $Tag2All->getVideoID() == $VideoID)
			){
				$OutArray[] = $Tag2All;
			}
		}
		return $OutArray;
	}
}

?>