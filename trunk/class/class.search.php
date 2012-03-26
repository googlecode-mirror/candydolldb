<?php

class AllIDs
{
	private $ModelID;
	private $SetID;
	private $ImageID;
	private $VideoID;
	
	public function getModelID()
	{ return $this->ModelID; }
	
	public function setModelID($ModelID)
	{ $this->ModelID = $ModelID; }
	
	public function getSetID()
	{ return $this->SetID; }
	
	public function setSetID($SetID)
	{ $this->SetID = $SetID; }
	
	public function getImageID()
	{ return $this->ImageID; }
	
	public function setImageID($ImageID)
	{ $this->ImageID = $ImageID; }
	
	public function getVideoID()
	{ return $this->VideoID; }
	
	public function setVideoID($VideoID)
	{ $this->VideoID = $VideoID; }
	
	public static function GetAllIDs($WhereClause = null, $OrderClause = null, $LimitClause = null)
	{
		global $db;
	
		if($db->Select('vw_IDs', '*', $WhereClause, $OrderClause, $LimitClause))
		{
			$OutArray = array();
			if($db->getResult())
			{
				foreach($db->getResult() as $IDItem)
				{
					$OutArray[] = self::ProcessDBitem($IDItem);
				}
			}
			return $OutArray;
		}
		else
		{ return null; }
	}
	
	public static function ProcessDBitem($IDItem)
	{
		$IDObject = new AllIDs();
			
		foreach($IDItem as $ColumnKey => $ColumnValue)
		{
			switch($ColumnKey)
			{
				case 'model_id'	: $IDObject->setModelID($ColumnValue);	break;
				case 'set_id'	: $IDObject->setSetID($ColumnValue);	break;
				case 'image_id'	: $IDObject->setImageID($ColumnValue);	break;
				case 'video_id'	: $IDObject->setVideoID($ColumnValue);	break;
			}
		}
	
		return $IDObject;
	}
}

class Search
{

}

?>