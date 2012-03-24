<?php

class TagModel extends Model
{
	private $TagID;
	private $TagName;
	
	public function getTagID()
	{ return $this->TagID; }
	
	public function setTagID($TagID)
	{ $this->TagID = $TagID; }
	
	public function getTagName()
	{ return $this->TagName; }

 	public function setTagName($TagName)
 	{ $this->TagName = $TagName; }
 	
 	public function setModelID($ModelID)
 	{ parent::setID($ModelID); }
 	
 	public function getModelID()
 	{ return parent::getID(); }

	/**
	* Process a DB->result() datarow into a TagModel object.
	* @param array $TagModelItem
	* @return TagModel
	*/
	public static function ProcessDBitem($TagModelItem)
	{
		$TagModelObject = new TagModel();
			
		foreach($TagModelItem as $ColumnKey => $ColumnValue)
		{
			switch($ColumnKey)
			{
				case 'tag_id'			: $TagModelObject->setTagID($ColumnValue);		break;
				case 'tag_name'			: $TagModelObject->setTagName($ColumnValue);	break;
				
				case 'model_id'			: $TagModelObject->setModelID($ColumnValue);	break;
				case 'model_firstname'	: $TagModelObject->setFirstName($ColumnValue);	break;
				case 'model_lastname'	: $TagModelObject->setLastName($ColumnValue);	break;
				case 'model_birthdate'	: $TagModelObject->setBirthDate($ColumnValue);	break;
				case 'model_remarks'	: $TagModelObject->setRemarks($ColumnValue);	break;
			}
		}
	
		return $TagModelObject;
	}
	
	public static function FilterTagModels($TagModels, $ModelID, $TagID)
	{
		$OutArray = array();
		foreach($TagModels as $tm)
		{
			if(
				$tm->getModelID() == $ModelID &&
				$tm->getTagID() == $TagID
			){
				$OutArray[] = $tm;
			}
		}
		return $OutArray;
	}
}

class Search
{
	private static $qModelByTagID = <<<uyhfueffgg8fg484gyirghirguy4gh4g4uiogfjh4984h

	select
		VWT.tag_id,
		VWT.tag_name,
		M.model_id,
		M.model_firstname,
		M.model_lastname,
		M.model_birthdate,
		M.model_remarks
	from
		vw_Tag2All as VWT
		join vw_IDs as VWI on (VWI.model_id = VWT.model_id or VWI.set_id = VWT.set_id or VWI.image_id = VWT.image_id or VWI.video_id = VWT.video_id)
		join `Model` as M on M.model_id = VWI.model_id
	where
		M.mut_deleted = -1	
		and
		VWT.tag_id in ( %1\$s )
	
uyhfueffgg8fg484gyirghirguy4gh4g4uiogfjh4984h;

	/**
	 * Returns an SQL-query to retrieve all Models tagged with one or more of the supplied TagIDs.
	 * @param array $TagIDs
	 * @return string
	 */
	private static function ModelsByTagIDsSQL($TagIDs)
	{
		$a = Utils::SafeInts($TagIDs);
	
		return sprintf(
			self::$qModelByTagID,
			($a ? implode(', ', $a) : 0)
		);
	}
	
	/**
	 * Gets an array of Models based on this class' model-by-TagID query
	 * @param array(int) $TagIDs
	 * @return array(Model)
	 */
	public static function ModelByTagIDs($TagIDs)
	{
		global $db;
		
		echo self::ModelsByTagIDsSQL($TagIDs);
		
		if($db->ExecuteSelect(self::ModelsByTagIDsSQL($TagIDs)))
		{
			$tmFromDB = array();
			if($db->getResult())
			{
				foreach($db->getResult() as $TagModelItem)
				{
					$tmFromDB[] = TagModel::ProcessDBitem($TagModelItem);
				}
			}
			
			$addthismodel = true;
			$OutArray = array();
			
			
			
			return $tmFromDB;
		}
		else
		{ return null; }
	}
}

?>