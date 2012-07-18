<?php

class Info
{
	private $InfoMessage;
	
	/**
	 * return string
	 */
	public function getInfoMessage()
	{ return $this->InfoMessage; }
	
	/**
	 * Sets this Info's message.
	 * @param string $InInfoMessage
	 */
	public function setInfoMessage($InInfoMessage)
	{ $this->InfoMessage = $InInfoMessage; }
	

	public function Info($InfoMessage = null)
	{
		$this->InfoMessage = $InfoMessage;
	}
	
	
	/**
	 * Adds an info to the 'global' Info-array, or outputs it to STDOUT.
	 * @param Info $InInfo
	 */
	public static function AddInfo($InInfo)
	{
		global $argv, $argc;
		
		if(isset($argv) && $argc > 0)
		{
			fwrite(STDOUT, $InInfo->getInfoMessage()."\n");
		}
		else 
		{
			$Infos = unserialize($_SESSION['Infos']);
			$Infos[] = $InInfo;
			$_SESSION['Infos'] = serialize($Infos);
		}
	}
	
	/**
	 * Generates a HTML unordered list, suitable for showing in an infoscreen overlay.
	 * @return string|NULL
	 */
	public static function GenerateInfoList()
	{
		$infoList = '';
		$infoCount = 0;
		$inInfos = unserialize($_SESSION['Infos']);
	
		if($inInfos !== null && is_array($inInfos) && count($inInfos) > 0)
		{
			$inInfos = array_reverse($inInfos);
			$infoList .= "\n<div class=\"InfoList\" title=\"Click to close this message.\"><div><ul>";
			
			/* @var $Info Info */
		 	while(($Info = array_pop($inInfos)) !== null)
			{
				$infoList .= sprintf("\n<li>%1\$s<br /><br /></li>",
					htmlentities($Info->getInfoMessage())
				);
				$infoCount++;
			}
			$infoList .= "</ul></div></div>";
		}
		
		$_SESSION['Infos'] = serialize($inInfos);
		
		if($infoCount > 0)
		{ return "<div id=\"InfoContainer\"></div>".$infoList; }
		else
		{ return null; }
	}
}

?>