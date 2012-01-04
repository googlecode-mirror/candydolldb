<?php

class CacheImage
{
	private $ID;
	private $ModelID;
	private $ModelIndexID;
	private $SetID;
	private $ImageID;
	private $VideoID;
	private $Kind = CACHEIMAGE_KIND_UNKNOWN;
	private $ImageWidth = 0;
	private $ImageHeight = 0;
	
	public function CacheImage()
	{
		$this->ID = Utils::GUID();
	}
	
	/**
	* @return string
	*/
	public function getID()
	{ return $this->ID; }
	
	/**
	* @param string $ID
	*/
	public function setID($ID)
	{ $this->ID = $ID; }
	
	/**
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
	* @return int
	*/
	public function getModelIndexID()
	{ return $this->ModelIndexID; }
	
	/**
	 * @param int $ModelIndexID
	 */
	public function setModelIndexID($ModelIndexID)
	{ $this->ModelIndexID = $ModelIndexID; }
	
	/**
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
	* @return int
	*/
	public function getKind()
	{ return $this->Kind; }
	
	/**
	 * @param int $Kind
	 */
	public function setKind($Kind)
	{ $this->Kind = $Kind; }
	
	/**
	* @return int
	*/
	public function getImageWidth()
	{ return $this->ImageWidth; }
	
	/**
	* @param int $ImageWidth
	*/
	public function setImageWidth($ImageWidth)
	{ $this->ImageWidth = $ImageWidth; }
	
	/**
	* @return int
	*/
	public function getImageHeight()
	{ return $this->ImageHeight; }
	
	/**
	* @param int $ImageHeight
	*/
	public function setImageHeight($ImageHeight)
	{ $this->ImageHeight = $ImageHeight; }

	/**
	 * @return string
	 */
	public function getFilenameOnDisk()
	{
		$pathPrefix = (isset($argv) && $argc > 0) ? dirname($_SERVER['PHP_SELF']).'/' : '';
		return sprintf($pathPrefix.'cache/%1$s.jpg', $this->getID());
	}
	
	public static function GetCacheImages($WhereClause = null, $OrderClause = null, $LimitClause = null)
	{
		global $db;
			
		if($db->Select('CacheImage', '*', $WhereClause, $OrderClause, $LimitClause))
		{
			$OutArray = array();
				
			if($db->getResult())
			{
				foreach($db->getResult() as $CacheImageItem)
				{
					$CacheImageObject = new CacheImage();
						
					foreach($CacheImageItem as $ColumnKey => $ColumnValue)
					{
						switch($ColumnKey)
						{
							case 'cache_id'				: $CacheImageObject->setID($ColumnValue);			break;
							
							case 'image_id'				: $CacheImageObject->setImageID($ColumnValue); 		break;
							case 'video_id'				: $CacheImageObject->setVideoID($ColumnValue); 		break;
							case 'set_id'				: $CacheImageObject->setSetID($ColumnValue);		break;
							case 'index_id'				: $CacheImageObject->setModelIndexID($ColumnValue);	break;
							case 'model_id'				: $CacheImageObject->setModelID($ColumnValue);		break;
							
							case 'cache_imagewidth'		: $CacheImageObject->setImageWidth($ColumnValue);	break;
							case 'cache_imageheight'	: $CacheImageObject->setImageHeight($ColumnValue);	break;
						}
					}
					
					if($CacheImageObject->getModelID()){
						$CacheImageObject->setKind(CACHEIMAGE_KIND_MODEL);
					}
					
					if($CacheImageObject->getModelIndexID()){
						$CacheImageObject->setKind(CACHEIMAGE_KIND_INDEX);
					}
					
					if($CacheImageObject->getSetID()){
						$CacheImageObject->setKind(CACHEIMAGE_KIND_SET);
					}
					
					if($CacheImageObject->getImageID()){
						$CacheImageObject->setKind(CACHEIMAGE_KIND_IMAGE);
					}
					
					if($CacheImageObject->getVideoID()){
						$CacheImageObject->setKind(CACHEIMAGE_KIND_VIDEO);
					}
					
					$OutArray[] = $CacheImageObject;
				}
			}
			return $OutArray;
		}
		else
		{ return null; }
	}
	
	/**
	* Inserts the given cacheimage into the database.
	* @param CacheImage $CacheImage
	* @param User $CurrentUser
	* @return bool
	*/
	public static function InsertCacheImage($CacheImage, $CurrentUser)
	{
		global $db;
		 
		return $db->Insert(
			'CacheImage',
			array(
				$CacheImage->getID(),
				$CacheImage->getModelID(),
				$CacheImage->getModelIndexID(),
				$CacheImage->getSetID(),
				$CacheImage->getImageID(),
				$CacheImage->getVideoID(),
				$CacheImage->getImageWidth(),
				$CacheImage->getImageHeight()
			),
			'cache_id, model_id, index_id, set_id, image_id, video_id, cache_imagewidth, cache_imageheight'
		);
	}
	
	/**
	* Removes the specified CacheImage from the database.
	* @param CacheImage $CacheImage
	* @param User $CurrentUser
	* @return bool
	*/
	public static function DeleteImage($CacheImage, $CurrentUser)
	{
		global $db;
	
		return $db->Delete(
			'CacheImage',
			sprintf(
				"cache_id = '%1\$s'",
				mysql_escape_string(
					$CacheImage->getID()
				)
			)
		);
	}
	
	public static function FilterCacheImages($CacheImageArray, $CacheImageKind = null, $ModelID = null, $ModelIndexID = null, $SetID = null, $ImageID = null, $VideoID = null)
	{
		$OutArray = array();
			
		/* @var $CacheImage CacheImage */
		foreach($CacheImageArray as $CacheImage)
		{
			if(
				(is_null($CacheImageKind) || $CacheImage->getKind() == $CacheImageKind)		&&
				(is_null($ModelID) || $CacheImage->getModelID() == $ModelID)				&&
				(is_null($ModelIndexID) || $CacheImage->getModelIndexID() == $ModelIndexID)	&&
				(is_null($SetID) || $CacheImage->getSetID() == $SetID)						&&
				(is_null($ImageID) || $CacheImage->getImageID() == $ImageID)				&&
				(is_null($VideoID) || $CacheImage->getVideoID() == $VideoID)
			){
				$OutArray[] = $CacheImage;
			}
		}
		return $OutArray;
	}
}

?>