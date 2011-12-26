<?php

class CacheImage
{
	private $ID;
	private $ModelID;
	private $SetID;
	private $ImageID;
	private $VideoID;
	private $ImageWidth = 0;
	private $ImageHeight = 0;
	
	public function CacheImage()
	{
		
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
	
}

?>