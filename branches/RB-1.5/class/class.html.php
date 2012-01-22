<?php

class HTMLstuff
{
	/**
	 * Generate a HTML header, containing DOCTYPE, head- and meta-tags, JavaScript-includes and title.
	 * @param string $Title
	 * @param User $CurrentUser
	 * @return string
	 */
	public static function HtmlHeader($Title = null, $CurrentUser = null)
	{
		$Output = sprintf("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
		<html xmlns=\"http://www.w3.org/1999/xhtml\">

		<head>
		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
		<meta name=\"language\" content=\"en-US\" />
		
		<link rel=\"stylesheet\" type=\"text/css\" href=\"style.css\" title=\"CandyDoll DB\" />
		<link rel=\"shortcut icon\" href=\"favicon.ico\" />
		<link rel=\"icon\" href=\"favicon.ico\" />
		
		<script type=\"text/javascript\" src=\"%4\$s://code.jquery.com/jquery.min.js\"></script>
		<script type=\"text/javascript\" src=\"js/fwiep.js\"></script>

		<title>CandyDoll DB v%1\$s%2\$s</title>
		</head>

		<body>
		
		<h1 class=\"Hidden\">CandyDollDB v%1\$s%2\$s</h1>
		%3\$s
		
		<div id=\"Wrapper\">
		<div id=\"Header\">
		</div>
		
		<div id=\"Content\">",

			CANDYDOLLDB_VERSION,
			$Title ? ' :: '.htmlentities($Title) : null,
			Error::GenerateErrorList(),
			(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http') 
		);


		if($CurrentUser != null) {
		
			$Output .= sprintf("
			<ul id=\"TopNavigation\">
			<li><a href=\"index.php\">Home</a></li>
			<li><a href=\"#\">Features</a>
		
				<ul>
				<li><a href=\"setup_data.php\">Process XML</a></li>
				<li><a href=\"model_view.php\">New model</a></li>
				<li><a href=\"set_dirty.php\">Dirty sets</a></li>
				<li><a href=\"admin_panel.php\">Admin-panel</a></li>
				<li><a href=\"download_multi.php\">Multi-download</a></li>
				</ul>
		
			</li>
			<li><a href=\"user.php\">Users</a>
			
				<ul>
				<li><a href=\"user_view.php?user_id=%1\$d\">My account</a></li>
				</ul>
			
			</li>
			<li><a href=\"logout.php\">Logout</a></li>
			</ul>",
			
			$CurrentUser->getID());
		}

		return $Output;
	}
	
	/**
	 * Generate a HTML footer, corresponding to this class' HTML-header function.
	 * @return string
	 */
	public static function HtmlFooter($CurrentUser = null)
	{
		return sprintf("
			</div>
			
			<div id=\"Footer\">
			<hr class=\"Hidden\" />
			<div class=\"AbsolutePoint\">

			%1\$s
			
			<div class=\"cddbstuff\">
			CandyDollDB v%2\$s<br />by <a href=\"http://www.fwiep.nl/\" rel=\"external\">FWieP</a>
			</div>
			
			</div>
			</div>
			
			</div>
			
			</body>
			</html>",
		
		$CurrentUser != null ? sprintf("<div class=\"userstats\">
			Logged in as <a href=\"user_view.php?user_id=%3\$d\"><strong>%1\$s</strong></a>.<br />Last login: %2\$s</div>",
			htmlentities($CurrentUser->getUserName()),
			$CurrentUser->getPreLastLogin() > 0 ? date('j F Y', $CurrentUser->getPreLastLogin()) : 'never',
			$CurrentUser->getID()
		) : '&nbsp;',
		
		CANDYDOLLDB_VERSION
		);
	}
	
	/**
	 * Generate a hyperlink pointing to the given URL, or index.php.  
	 * @param string $URL
	 * @param string $ButtonText
	 * @param string $CustomAttributes
	 * @return string
	 */
	public static function Button($URL = null, $ButtonText = null, $CustomAttributes = null)
	{
		return sprintf(
			"<a href=\"%1\$s\" class=\"Button\"%3\$s>%2\$s</a>",
		 	$URL ? $URL : (array_key_exists('HTTP_REFERER', $_SERVER) ? $_SERVER['HTTP_REFERER'] : 'index.php'),
			$ButtonText ? htmlentities($ButtonText) : 'Home',
			$CustomAttributes ? $CustomAttributes : null 
		);
	}
	/**
	 * Generates a JavaScript-snippet which shows a nice loading animation while an image loads.
	 * @param string $url
	 * @param int $width
	 * @param int $height
	 * @param string $title
	 * @param string $alt
	 * @return string
	 */
	public static function ImageLoading($url, $width = 400, $height = 600, $title = '', $alt = '')
	{
		$template = <<<HfuheuhUHfuh3e83uhfuhdfu3
		<script type="text/javascript">
		//<![CDATA[
		
			$(document).ready(function(){
				var img = new Image();
				$(img).load(function(){
					$(this).hide();
					$('.Loading').removeClass('Loading').append(this);
					$(this).fadeIn();
				}).attr({
					width : %2\$d,
					height : %3\$d,
					title : '%4\$s',
					alt : '%5\$s',
					src : '%1\$s'
				});
			});
		           
		//]]>
		</script>
HfuheuhUHfuh3e83uhfuhdfu3;

		return sprintf($template, $url, $width, $height, $title, $alt);
	}
	
	/**
	 * Returns a HTML-snippet providing a HTML textinput for the given DateKind.
	 * @param string $UniqueId
	 * @param string $Value
	 * @param int $DateKind
	 * @param bool $Disabled 
	 * @return string
	 */
	public static function DateFormField($UniqueId, $Value = null, $DateKind = DATE_KIND_UNKNOWN, $Disabled = false)
	{
		$template = <<<GYtguefggefegfgefgegfgfuguf
		<div class="FormRow">
		<label for="txtDate%2\$s%3\$d">Date%1\$s:</label>
		<input type="text" id="txtDate%2\$s%3\$d" name="txtDate%2\$s%3\$d" class="DatePicker" maxlength="10" value="%4\$s"%5\$s />
		%6\$s
		</div>
		
GYtguefggefegfgefgegfgfuguf;

		return sprintf($template,
			$DateKind == DATE_KIND_IMAGE ? ' (images)' : ($DateKind == DATE_KIND_VIDEO ? ' (videos)' : ''),
			$DateKind == DATE_KIND_IMAGE ? 'Pic' : ($DateKind == DATE_KIND_VIDEO ? 'Vid' : ''),
			$UniqueId,
			$Value,
			HTMLstuff::DisabledStr($Disabled),
			($UniqueId && !$Disabled ? sprintf("<a href=\"date_delete.php?date_id=%1\$d\" onclick=\"if(!confirm('Are you sure you wish to delete this date?')){return false;}\"><img src=\"images/button_delete.png\" title=\"Delete date\" alt=\"Delete date\"/></a>", $UniqueId ) : null)
			
		);
	}
	
	/**
	 * Filters the InArray for HTML textinputs and returns corresponding Dates
	 * @param array $InArray
	 * @param Set $Set
	 * @param int $DateKind
	 * @return array(Date)
	 */
	public static function DatesFromPOST($InArray, $Set, $DateKind = DATE_KIND_UNKNOWN)
	{
		$OutArray = array();
		if(is_array($InArray))
		{
			foreach ($InArray as $k => $v)
			{
				$pm = preg_match('/^txtDate(?P<Kind>Pic|Vid)(?P<ID>\d+)$/i', $k, $matches);
				if($pm > 0 && isset($matches))
				{
					if(($matches['Kind'] == 'Pic' && $DateKind != DATE_KIND_IMAGE)
					|| ($matches['Kind'] == 'Vid' && $DateKind != DATE_KIND_VIDEO))
					{ continue; }
					
					/* @var $Date Date */
					$Date = new Date();
					
					if($matches['ID'] != 0)
					{ $Date->setID(intval($matches['ID'])); }
					
					$Date->setSet($Set);
					$Date->setDateKind($DateKind);

					if(!$v || $v == 'YYYY-MM-DD')
					{ $Date->setTimeStamp(-1); }
					
					if(($timestamp = strtotime($v)) !== false)
					{ $Date->setTimeStamp($timestamp); }
					
					$OutArray[] = $Date;
				}
			}
		}
		return $OutArray;
	}
	
	public static function DisabledStr($InBool)
	{
		return $InBool ? ' disabled="disabled"' : '';
	}
	
	public static function CheckedStr($InBool)
	{
		return $InBool ? ' checked="checked"' : '';
	}
	
	public static function RefererRedirect($RedirURL = 'index.php')
	{
		global $argv, $argc;
		
		if(isset($argv) && $argc > 0)
		{
			exit;
		}
		else 
		{
			if(array_key_exists('HTTP_REFERER', $_SERVER))
			{
				header('location:'.$_SERVER['HTTP_REFERER']);
				exit;
			}
			else
			{
				header('location:'.$RedirURL);
				exit;
			}	
		}
	}
}

?>
