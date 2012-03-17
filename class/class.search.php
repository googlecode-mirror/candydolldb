<?php

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
	group by
		M.model_id
	
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
		
		if($db->ExecuteSelect(self::ModelsByTagIDsSQL($TagIDs)))
		{
			$OutArray = array();
			if($db->getResult())
			{
				foreach($db->getResult() as $ModelItem)
				{
					$OutArray[] = Model::ProcessDBitem($ModelItem);
				}
			}
			return $OutArray;
		}
		else
		{ return null; }
	}
}

?>