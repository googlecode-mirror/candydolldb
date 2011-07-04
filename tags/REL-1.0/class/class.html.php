<?php
/*	This file is part of CandyDollDB.

    CandyDollDB is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    CandyDollDB is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with CandyDollDB.  If not, see <http://www.gnu.org/licenses/>.
*/

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
		return sprintf("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
		<html xmlns=\"http://www.w3.org/1999/xhtml\">

		<head>
		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
		<meta name=\"language\" content=\"en-US\" />
		
		<link rel=\"stylesheet\" type=\"text/css\" href=\"style.css\" title=\"CandyDoll DB v1.0\" />
		<link rel=\"shortcut icon\" href=\"favicon.ico\" />
		<link rel=\"icon\" href=\"favicon.ico\" />
		
		<script type=\"text/javascript\" src=\"http://code.jquery.com/jquery.min.js\"></script>
		<script type=\"text/javascript\" src=\"js/fwiep.js\"></script>
		<title>CandyDoll DB v1.0%1\$s</title>
		</head>

		<body>

		<div id=\"Container\">
		<div id=\"Header\"%3\$s>

		<h1>CandyDoll DB v1.0%1\$s</h1>
		<p>by <a href=\"http://www.fwiep.nl/\" rel=\"external\" title=\"FWieP\">FWieP</a></p>
		%4\$s
		<div class=\"AbsolutePoint\">
		%2\$s
		</div>
		
		</div>

		<div id=\"Content\">",
		
		$Title ? ' :: '.htmlentities($Title) : null,
		
		$CurrentUser != null ? sprintf(
			"<div class=\"LoginStats\">
			Logged in as <a href=\"user_view.php?user_id=%3\$d\"><strong>%1\$s</strong></a>.<br />Last login: %2\$s &gt;&zwnj;<a href=\"logout.php\"><strong>Log&nbsp;out</strong></a>&zwnj;&lt;
			</div>",
			htmlentities($CurrentUser->getUserName()),
			$CurrentUser->getPreLastLogin() > 0 ? date('j F Y @ G:i', $CurrentUser->getPreLastLogin()) : 'never',
			$CurrentUser->getID()
		) : null,
		
		$CurrentUser == null ? ' style="background-position:230px 0;"' : null,
		
		Error::GenerateErrorList()
		);
	}
	
	/**
	 * Generate a HTML footer, corresponding to this class' HTML-header function.
	 * @return string
	 */
	public static function HtmlFooter()
	{
		return sprintf("
		</div>

		</div>

		</body>
		</html>");
	}
	
	/**
	 * Generate a hyperlink pointing to the given URL, or index.php.  
	 * @param string $URL
	 * @param string $ButtonText
	 * @return string
	 */
	public static function Button($URL = null, $ButtonText = null)
	{
		return sprintf(
			"<a href=\"%1\$s\" class=\"Button\">%2\$s</a>",
		 	$URL ? $URL : (array_key_exists('HTTP_REFERER', $_SERVER) ? $_SERVER['HTTP_REFERER'] : 'index.php'),
			$ButtonText ? htmlentities($ButtonText) : 'Home' 
		);
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
		if(array_key_exists('HTTP_REFERER', $_SERVER))
		{ header('location:'.$_SERVER['HTTP_REFERER']); }
		else
		{ header('location:'.$RedirURL); }	
	}
}

?>