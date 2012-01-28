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

class Error
{
	private $ErrorNumber;
	
	/**
	 * return int
	 */
	public function getErrorNumber()
	{ return $this->ErrorNumber; }
	
	/**
	 * Sets this Error's number.
	 * @param int $InErrorNumber
	 */
	public function setErrorNumber($InErrorNumber)
	{ $this->ErrorNumber = $InErrorNumber; }
	
	private $ErrorMessage;
	
	/**
	 * return string
	 */
	public function getErrorMessage()
	{ return $this->ErrorMessage; }
	
	/**
	 * Sets this Error's message.
	 * @param string $InErrorMessage
	 */
	public function setErrorMessage($InErrorMessage)
	{ $this->ErrorMessage = $InErrorMessage; }
	

	public function Error($ErrorNumber = null, $ErrorMessage = null)
	{
		$this->ErrorNumber = $ErrorNumber;
		$this->ErrorMessage = $ErrorMessage;
	}
	
	
	/**
	 * Adds an error to the 'global' Error-array.
	 * @param Error $InError
	 */
	public static function AddError($InError)
	{
		$Errors = unserialize($_SESSION['Errors']);
		$Errors[] = $InError;
		$_SESSION['Errors'] = serialize($Errors);
	}
	
	/**
	 * Generates a HTML unordered list, suitable for showing in an errorscreen overlay.
	 * @return string|NULL
	 */
	public static function GenerateErrorList()
	{
		$errorList = '';
		$errorCount = 0;
		$inErrors = unserialize($_SESSION['Errors']);
	
		if($inErrors !== null && is_array($inErrors) && count($inErrors) > 0)
		{
			$inErrors = array_reverse($inErrors);
			$errorList .= "\n<div class=\"ErrorList\" title=\"Click to close this message.\"><div><ul>";
			
			/* @var $Error Error */
		 	while(($Error = array_pop($inErrors)) !== null)
			{
				$errorList .= sprintf("\n<li>%1\$s<br /><br /></li>",
					htmlentities($Error->getErrorMessage())
				);
				$errorCount++;
			}
			$errorList .= "</ul></div></div>";
		}
		
		$_SESSION['Errors'] = serialize($inErrors);
		
		if($errorCount > 0)
		{ return "<div id=\"ErrorContainer\"></div>".$errorList; }
		else
		{ return null; }
	}
	
	/**
	 * Translates the numeric generic error into a human readable string
	 * @param int $InError
	 * @return string
	 */
	public static function TranslateError($InError)
	{
		$OutMessage = null;
		
		switch($InError)
		{
			case REQUIRED_FIELD_MISSING:
				$OutMessage = 'Not all required data was entered.'; break;
			default:
				$OutMessage = 'Unknown error'; break;
		}
		return $OutMessage;
	}
}

class LoginError extends Error
{
	public function LoginError()
	{ parent::Error(); }
	
	/**
	 * Translates the numeric login-error into a human readable string
	 * @param int $InError
	 * @return string
	 */
	public static function TranslateLoginError($InError)
	{
		$OutMessage = null;
		
		switch($InError)
		{
			case LOGIN_ERR_PASSWORDSNOTIDENTICAL:
				$OutMessage = 'The provided passwords are not identical'; break;
			case LOGIN_ERR_RESETCODENOTFOUND:
				$OutMessage = 'The hypelink you have used is not or no longer valid'; break;
			case LOGIN_ERR_USERNAMENOTFOUND:
				$OutMessage = 'The specified username was not found'; break;
			case LOGIN_ERR_USERNAMEANDMAILADDRESNOTFOUND:
				$OutMessage = 'The specified combination of username and e-mailaddress was not found'; break;
			case LOGIN_ERR_PASSWORDINCORRECT:
				$OutMessage = 'The specified password is not correct'; break;
			default:
				$OutMessage = 'Unknown login error'; break;
		}
		return $OutMessage;
	}
}

class SQLerror extends Error
{
	public function SQLerror()
	{ parent::Error(); }
	
	/**
	 * Translates the numeric SQL-error into a human readable string
	 * @param int $InError
	 * @return string
	 */
	public static function TranslateSQLError($InError)
	{
		$OutMessage = null;
		
		switch($InError)
		{
			case SQL_ERR_NOSUCHTABLE:
				$OutMessage = 'The specified table does not exist'; break;
			default:
				$OutMessage = 'Unknown SQL error'; break;
		}
		return $OutMessage;
	}
}

class SyntaxError extends Error
{
	public function SyntaxError()
	{ parent::Error(); }
	
	/**
	 * Translates the numeric Syntax-error into a human readable string
	 * @param int $InError
	 * @return string
	 */
	public static function TranslateSyntaxError($InError)
	{
		$OutMessage = null;
		
		switch($InError)
		{
			case SYNTAX_ERR_EMAILADDRESS:
				$OutMessage = 'The specified emailaddress is not valid'; break;
			default:
				$OutMessage = 'Unknown syntax error'; break;
		}
		return $OutMessage;
	}
}

class UploadError extends Error
{
	public function UploadError()
	{ parent::Error(); }
	
	/**
	 * Translates the numeric upload-error ($_FILES['upload']['error']) into a human readable string
	 * @param int $InError
	 * @return string
	 */
	public static function TranslateUploadError($InError)
	{
		$OutMessage = null;
		
		switch($InError)
		{
			case UPLOAD_ERR_OK:
				$OutMessage = 'There is no error, the file uploaded with success'; break;
			case UPLOAD_ERR_INI_SIZE:
				$OutMessage = 'The uploaded file exceeds the upload_max_filesize directive in php.ini'; break;
			case UPLOAD_ERR_FORM_SIZE:
				$OutMessage = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form'; break;
			case UPLOAD_ERR_PARTIAL:
				$OutMessage = 'The uploaded file was only partially uploaded'; break;
			case UPLOAD_ERR_NO_FILE:
				$OutMessage = 'No file was uploaded'; break;
			case UPLOAD_ERR_NO_TMP_DIR:
				$OutMessage = 'Missing a temporary folder'; break;
			case UPLOAD_ERR_CANT_WRITE:
				$OutMessage = 'Failed to write file to disk'; break;
			case UPLOAD_ERR_EXTENSION:
				$OutMessage = 'A PHP extension stopped the file upload'; break;
			default:
				$OutMessage = 'Unknown upload error'; break;
		}
		return $OutMessage;
	}
}

?>