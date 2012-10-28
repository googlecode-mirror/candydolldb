<?php
/* This file is part of CandyDollDB.

CandyDollDB is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

CandyDollDB is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with CandyDollDB. If not, see <http://www.gnu.org/licenses/>.
*/

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

	public function __construct($InfoMessage = NULL)
	{ $this->InfoMessage = $InfoMessage; }
		
	/**
	 * Adds an info to the 'global' Info-array, or outputs it to STDOUT.
	 * @param Info $InInfo
	 */
	public static function AddInfo($InInfo)
	{
		global $argv, $argc;
		
		if(isset($argv) && $argc > 0)
		{
			fwrite(STDOUT, html_entity_decode($InInfo->getInfoMessage(), ENT_COMPAT, 'UTF-8')."\n");
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
	
		if(is_array($inInfos) && count($inInfos) > 0)
		{
			$inInfos = array_reverse($inInfos);
			$infoList .= "\n<div class=\"InfoList\" title=\"Click to close this message.\"><div><ul>";
			
			/* @var $Info Info */
		 	while(($Info = array_pop($inInfos)) !== NULL)
			{
				$infoList .= sprintf("\n<li>%1\$s<br /><br /></li>",
					$Info->getInfoMessage()
				);
				$infoCount++;
			}
			$infoList .= "</ul></div></div>";
		}
		
		$_SESSION['Infos'] = serialize($inInfos);
		
		if($infoCount > 0)
		{ return "<div id=\"InfoContainer\"></div>".$infoList; }
		else
		{ return NULL; }
	}
}

?>
